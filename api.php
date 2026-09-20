<?php
/**
 * Zaika Shahi Halwa - RESTful API Handler
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true) ?? $_POST;

function sendResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

switch ($action) {
    case 'get_products':
        $products = HalwaDB::readJson('products.json');
        sendResponse(true, $products, 'Products retrieved successfully');
        break;

    case 'get_coupons':
        $coupons = HalwaDB::readJson('coupons.json');
        sendResponse(true, $coupons, 'Coupons retrieved');
        break;

    case 'validate_coupon':
        $code = strtoupper(trim($inputData['code'] ?? ''));
        $subtotal = floatval($inputData['subtotal'] ?? 0);
        $coupons = HalwaDB::readJson('coupons.json');
        
        $found = null;
        foreach ($coupons as $c) {
            if (strtoupper($c['code']) === $code && $c['active']) {
                $found = $c;
                break;
            }
        }

        if (!$found) {
            sendResponse(false, null, 'Invalid or expired coupon code', 400);
        }

        if ($subtotal < floatval($found['min_order'])) {
            sendResponse(false, null, "Minimum order amount of $" . number_format($found['min_order'], 2) . " required for this code.", 400);
        }

        $discount = 0;
        if ($found['discount_type'] === 'percentage') {
            $discount = round(($subtotal * $found['value']) / 100, 2);
        } elseif ($found['discount_type'] === 'fixed') {
            $discount = min($subtotal, floatval($found['value']));
        } elseif ($found['discount_type'] === 'free_shipping') {
            $discount = floatval($found['value']);
        }

        sendResponse(true, [
            'coupon' => $found,
            'discount' => $discount
        ], "Coupon '{$code}' applied successfully!");
        break;

    case 'create_order':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $customer = $inputData['customer'] ?? null;
        $items = $inputData['items'] ?? [];
        $orderType = $inputData['order_type'] ?? 'delivery';
        $slot = $inputData['delivery_slot'] ?? 'As soon as fresh (30-45 mins)';
        $paymentMethod = $inputData['payment_method'] ?? 'Cash on Delivery';
        $couponCode = trim($inputData['coupon_code'] ?? '');
        $notes = trim($inputData['notes'] ?? '');

        if (empty($customer['name']) || empty($customer['phone'])) {
            sendResponse(false, null, 'Customer name and phone number are required', 400);
        }

        if (empty($items) || !is_array($items)) {
            sendResponse(false, null, 'Cart is empty', 400);
        }

        // Calculate Subtotal & verify
        $subtotal = 0;
        foreach ($items as $item) {
            $qty = max(1, intval($item['quantity'] ?? 1));
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $subtotal += ($unitPrice * $qty);
        }

        // Compute discount
        $discount = 0;
        if (!empty($couponCode)) {
            $coupons = HalwaDB::readJson('coupons.json');
            foreach ($coupons as $c) {
                if (strtoupper($c['code']) === strtoupper($couponCode) && $c['active'] && $subtotal >= floatval($c['min_order'])) {
                    if ($c['discount_type'] === 'percentage') {
                        $discount = round(($subtotal * $c['value']) / 100, 2);
                    } elseif ($c['discount_type'] === 'fixed') {
                        $discount = min($subtotal, floatval($c['value']));
                    }
                    break;
                }
            }
        }

        $deliveryFee = ($orderType === 'pickup' || $subtotal >= 45.00) ? 0.00 : 4.50;
        $total = max(0, ($subtotal - $discount) + $deliveryFee);

        $orderId = HalwaDB::generateOrderId();
        $newOrder = [
            'order_id' => $orderId,
            'customer' => [
                'name' => htmlspecialchars($customer['name']),
                'phone' => htmlspecialchars($customer['phone']),
                'email' => htmlspecialchars($customer['email'] ?? ''),
                'address' => htmlspecialchars($customer['address'] ?? ''),
                'city' => htmlspecialchars($customer['city'] ?? 'London'),
                'postal_code' => htmlspecialchars($customer['postal_code'] ?? '')
            ],
            'items' => $items,
            'order_type' => $orderType,
            'delivery_slot' => $slot,
            'payment_method' => $paymentMethod,
            'payment_status' => ($paymentMethod === 'Cash on Delivery') ? 'Pending' : 'Paid (Simulated)',
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'coupon_code' => $couponCode,
            'delivery_fee' => round($deliveryFee, 2),
            'total' => round($total, 2),
            'notes' => htmlspecialchars($notes),
            'status' => 'received',
            'status_history' => [
                [
                    'status' => 'received',
                    'time' => date('Y-m-d H:i'),
                    'note' => 'Order placed and received by Master Halwai'
                ]
            ],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $orders = HalwaDB::readJson('orders.json');
        array_unshift($orders, $newOrder);
        HalwaDB::writeJson('orders.json', $orders);

        sendResponse(true, $newOrder, 'Order placed successfully! Warm confections in preparation.');
        break;

    case 'track_order':
        $query = trim($_GET['order_id'] ?? ($_GET['phone'] ?? ''));
        if (empty($query)) {
            sendResponse(false, null, 'Order ID or phone number required', 400);
        }

        $orders = HalwaDB::readJson('orders.json');
        $found = null;
        $cleanQuery = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $query));

        foreach ($orders as $order) {
            $cleanOrderId = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $order['order_id']));
            $cleanPhone = preg_replace('/[^0-9]/', '', $order['customer']['phone'] ?? '');
            
            if ($cleanOrderId === $cleanQuery || (!empty($cleanPhone) && strpos($cleanPhone, $cleanQuery) !== false)) {
                $found = $order;
                break;
            }
        }

        if ($found) {
            sendResponse(true, $found, 'Order found');
        } else {
            sendResponse(false, null, 'No order found matching "' . htmlspecialchars($query) . '". Please check your order ID or phone number.', 404);
        }
        break;

    case 'get_reviews':
        $reviews = HalwaDB::readJson('reviews.json');
        sendResponse(true, $reviews, 'Reviews retrieved');
        break;

    case 'add_review':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $name = trim($inputData['name'] ?? 'Artisan Patron');
        $rating = max(1, min(5, intval($inputData['rating'] ?? 5)));
        $productId = trim($inputData['product_id'] ?? 'halwa-01');
        $productName = trim($inputData['product_name'] ?? 'Shahi Halwa');
        $title = trim($inputData['title'] ?? 'Delicious Halwa Experience');
        $comment = trim($inputData['comment'] ?? '');
        $city = trim($inputData['city'] ?? 'London');

        if (empty($comment)) {
            sendResponse(false, null, 'Review comment cannot be empty', 400);
        }

        $reviews = HalwaDB::readJson('reviews.json');
        $newReview = [
            'id' => 'rev-' . (count($reviews) + 1) . '-' . rand(100, 999),
            'name' => htmlspecialchars($name),
            'rating' => $rating,
            'product_id' => $productId,
            'product_name' => htmlspecialchars($productName),
            'title' => htmlspecialchars($title),
            'comment' => htmlspecialchars($comment),
            'date' => date('Y-m-d'),
            'verified' => true,
            'city' => htmlspecialchars($city)
        ];

        array_unshift($reviews, $newReview);
        HalwaDB::writeJson('reviews.json', $reviews);

        sendResponse(true, $newReview, 'Thank you for your review! It has been published.');
        break;

    case 'admin_orders':
        $orders = HalwaDB::readJson('orders.json');
        sendResponse(true, $orders, 'All orders loaded');
        break;

    case 'update_order_status':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $orderId = trim($inputData['order_id'] ?? '');
        $newStatus = trim($inputData['status'] ?? '');
        $note = trim($inputData['note'] ?? '');

        $validStatuses = ['received', 'cooking', 'packed', 'out_for_delivery', 'delivered', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            sendResponse(false, null, 'Invalid status code', 400);
        }

        $orders = HalwaDB::readJson('orders.json');
        $updated = false;
        $updatedOrder = null;

        $statusLabels = [
            'received' => 'Order Received & Verified',
            'cooking' => 'Simmering in Pure Desi Ghee & Fresh Saffron',
            'packed' => 'Hand-packed in Velvet Keepsake Box',
            'out_for_delivery' => 'Dispatched with Express Thermal Courier',
            'delivered' => 'Delivered Fresh & Warm',
            'cancelled' => 'Order Cancelled'
        ];

        foreach ($orders as &$order) {
            if ($order['order_id'] === $orderId) {
                $order['status'] = $newStatus;
                if (!isset($order['status_history'])) {
                    $order['status_history'] = [];
                }
                $order['status_history'][] = [
                    'status' => $newStatus,
                    'time' => date('Y-m-d H:i'),
                    'note' => !empty($note) ? $note : ($statusLabels[$newStatus] ?? 'Status updated')
                ];
                $updated = true;
                $updatedOrder = $order;
                break;
            }
        }

        if ($updated) {
            HalwaDB::writeJson('orders.json', $orders);
            sendResponse(true, $updatedOrder, "Order {$orderId} status updated to {$newStatus}");
        } else {
            sendResponse(false, null, 'Order not found', 404);
        }
        break;

    case 'get_banners':
        $banners = HalwaDB::readJson('banners.json');
        sendResponse(true, $banners, 'Banners retrieved');
        break;

    case 'save_banner':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $banners = HalwaDB::readJson('banners.json');
        $id = trim($inputData['id'] ?? '');
        $isNew = empty($id);

        if ($isNew) {
            $id = 'banner-' . time() . '-' . rand(100, 999);
        }

        $bannerData = [
            'id' => $id,
            'tag' => trim($inputData['tag'] ?? '🔥 SPECIAL OFFER'),
            'title' => trim($inputData['title'] ?? 'Authentic Kerala'),
            'highlight' => trim($inputData['highlight'] ?? 'Halwa Special'),
            'subtext' => trim($inputData['subtext'] ?? 'Slow-simmered in pure coconut oil and natural ingredients.'),
            'cta_text' => trim($inputData['cta_text'] ?? 'Order Now'),
            'cta_link' => trim($inputData['cta_link'] ?? '#single-varieties'),
            'image' => trim($inputData['image'] ?? 'assets/images/gajar_halwa.jpg'),
            'theme_color' => trim($inputData['theme_color'] ?? '#FFA000'),
            'bg_style' => trim($inputData['bg_style'] ?? 'linear-gradient(135deg, #FFFDF9 0%, #FFF8EE 100%)'),
            'active' => isset($inputData['active']) ? (bool)$inputData['active'] : true,
            'order' => intval($inputData['order'] ?? (count($banners) + 1))
        ];

        $foundIdx = -1;
        foreach ($banners as $idx => $b) {
            if ($b['id'] === $id) {
                $foundIdx = $idx;
                break;
            }
        }

        if ($foundIdx >= 0) {
            $banners[$foundIdx] = $bannerData;
        } else {
            $banners[] = $bannerData;
        }

        HalwaDB::writeJson('banners.json', $banners);
        sendResponse(true, $bannerData, $isNew ? 'New banner created successfully!' : 'Banner updated successfully!');
        break;

    case 'delete_banner':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $id = trim($inputData['id'] ?? '');
        $banners = HalwaDB::readJson('banners.json');
        $filtered = array_values(array_filter($banners, function($b) use ($id) {
            return $b['id'] !== $id;
        }));

        HalwaDB::writeJson('banners.json', $filtered);
        sendResponse(true, $filtered, "Banner {$id} removed successfully.");
        break;

    case 'save_product':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $products = HalwaDB::readJson('products.json');
        $id = trim($inputData['id'] ?? '');
        $isNew = empty($id);

        if ($isNew) {
            $id = 'halwa-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', trim($inputData['name'] ?? 'custom'))) . '-' . rand(100, 999);
        }

        $weights = $inputData['weights'] ?? [
            ["size" => "200 g", "price" => floatval($inputData['base_price'] ?? 299), "orig" => floatval($inputData['original_price'] ?? 599)],
            ["size" => "500 g", "price" => floatval($inputData['base_price'] ?? 299) * 1.8, "orig" => floatval($inputData['original_price'] ?? 599) * 1.8],
            ["size" => "1 kg", "price" => floatval($inputData['base_price'] ?? 299) * 3.2, "orig" => floatval($inputData['original_price'] ?? 599) * 3.2]
        ];

        $productData = [
            'id' => $id,
            'name' => trim($inputData['name'] ?? 'Kerala Halwa'),
            'category' => trim($inputData['category'] ?? 'single'),
            'discount_badge' => trim($inputData['discount_badge'] ?? '50% OFF'),
            'badge_type' => trim($inputData['badge_type'] ?? 'Sale'),
            'is_sold_out' => isset($inputData['is_sold_out']) ? (bool)$inputData['is_sold_out'] : false,
            'rating' => floatval($inputData['rating'] ?? 5.0),
            'review_count' => intval($inputData['review_count'] ?? 5),
            'original_price' => floatval($inputData['original_price'] ?? 599.00),
            'base_price' => floatval($inputData['base_price'] ?? 299.00),
            'image' => trim($inputData['image'] ?? 'assets/images/gajar_halwa.jpg'),
            'weights' => $weights
        ];

        $foundIdx = -1;
        foreach ($products as $idx => $p) {
            if ($p['id'] === $id) {
                $foundIdx = $idx;
                break;
            }
        }

        if ($foundIdx >= 0) {
            $products[$foundIdx] = $productData;
        } else {
            $products[] = $productData;
        }

        HalwaDB::writeJson('products.json', $products);
        sendResponse(true, $productData, $isNew ? 'Product added successfully!' : 'Product updated successfully!');
        break;

    case 'update_product_prices':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $id = trim($inputData['id'] ?? '');
        $basePrice = floatval($inputData['base_price'] ?? 0);
        $originalPrice = floatval($inputData['original_price'] ?? 0);
        $discountBadge = trim($inputData['discount_badge'] ?? '');
        $weights = $inputData['weights'] ?? null;

        $products = HalwaDB::readJson('products.json');
        $updated = false;

        foreach ($products as &$p) {
            if ($p['id'] === $id) {
                if ($basePrice > 0) $p['base_price'] = $basePrice;
                if ($originalPrice > 0) $p['original_price'] = $originalPrice;
                if (!empty($discountBadge)) $p['discount_badge'] = $discountBadge;
                if (!empty($weights) && is_array($weights)) $p['weights'] = $weights;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            HalwaDB::writeJson('products.json', $products);
            sendResponse(true, $products, "Prices updated successfully for product {$id}");
        } else {
            sendResponse(false, null, 'Product not found', 404);
        }
        break;

    case 'delete_product':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, null, 'Invalid request method', 405);
        }

        $id = trim($inputData['id'] ?? '');
        $products = HalwaDB::readJson('products.json');
        $filtered = array_values(array_filter($products, function($p) use ($id) {
            return $p['id'] !== $id;
        }));

        HalwaDB::writeJson('products.json', $filtered);
        sendResponse(true, $filtered, "Product {$id} deleted successfully.");
        break;

    default:
        sendResponse(false, null, 'Action not recognized', 400);
        break;
}
