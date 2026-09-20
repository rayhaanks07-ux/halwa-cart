<?php
/**
 * Halwa Haweli — Authentic Kerala Halwa (PHP Entry)
 */
require_once __DIR__ . '/backend/db.php';
$products = HalwaDB::readJson('products.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halwa Haweli | Authentic Kerala Halwa</title>
  <meta name="description" content="Buy authentic Kerala halwa online. Dates Halwa, Black Halwa, Jackfruit Halwa, Fruit & Nut Halwa, and 5 Premium Varieties Heritage Gift Box.">
  
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
</head>
<body>

  <!-- Top Continuous Moving Marquee Announcement Bar -->
  <div class="haweli-top-bar">
    <div class="marquee-container">
      <div class="marquee-track">
        <span>🔥 FLAT 50% OFF ON ALL AUTHENTIC KERALA HALWAS</span>
        <span>•</span>
        <span>🚚 FREE DELIVERY ON ORDERS OVER RS. 499</span>
        <span>•</span>
        <span>🌿 100% PURE COCONUT OIL & DESI GHEE</span>
        <span>•</span>
        <span>🎁 USE CODE: <strong>HALWA10</strong> FOR EXTRA 10% OFF</span>
        <span>•</span>
        <span>⭐ OVER 2000+ VERIFIED 5-STAR REVIEWS</span>
        <span>•</span>
        <span>📦 HANDCRAFTED DAILY & SHIPPED FRESH</span>
      </div>
    </div>
  </div>

  <!-- Main Site Header -->
  <header class="haweli-header">
    <div class="container header-container">
      <a href="index.php" class="brand-wrap">
        <div class="brand-badge">HH</div>
        <div class="brand-title-text">HALWA <span>HAWELI</span></div>
      </a>

      <nav class="header-nav">
        <a href="#single-varieties" class="nav-link-item active">Single Varieties</a>
        <a href="#fruit-halwa" class="nav-link-item">Fruit Halwa</a>
        <a href="#heritage-spotlight" class="nav-link-item">Halwa Combo</a>
        <a href="track.php" class="nav-link-item">Track Order</a>
        <a href="admin.php" class="nav-link-item">Admin</a>
      </nav>

      <div class="header-actions">
        <a href="track.php" style="font-size: 13px; font-weight: 700; color: #374151; padding: 6px 12px; border: 1px solid #E5E7EB; border-radius: 20px;">
          Track Order
        </a>
        <button type="button" class="btn-header-cart" onclick="window.haweliApp.openCart()" aria-label="Cart">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span>Cart</span>
          <span class="cart-pill-count" id="header-cart-count" style="display: none;">0</span>
        </button>
      </div>
    </div>
  </header>

  <!-- =========================================================================
       MOVABLE HERO CAROUSEL BANNERS (At the Top / First Section)
       ========================================================================= -->
  <section class="haweli-hero-slider-section">
    <div class="container">
      <div class="hero-slider-wrapper" id="hero-slider-wrapper">
        <!-- Automatic Slide Progress Bar Indicator -->
        <div class="slider-progress-track">
          <div class="slider-progress-bar" id="slider-progress-bar"></div>
        </div>

        <!-- Track Container -->
        <div class="hero-slider-track" id="hero-slider-track">
          <!-- Slide 1 -->
          <div class="hero-slide-item">
            <div class="slide-content-left">
              <span class="slide-tag-pill">🔥 55.06% OFF • FESTIVE SALE</span>
              <h2 class="slide-main-heading">
                Authentic Kozhikode <span>Kerala Halwa</span>
              </h2>
              <p class="slide-sub-text">
                Slow-simmered in pure coconut oil, natural dates, and rich jaggery, loaded with whole roasted cashews.
              </p>
              <a href="#single-varieties" class="btn-slide-cta">
                <span>Shop Dates & Black Halwa</span>
                <span>→</span>
              </a>
            </div>
            <div class="slide-image-box">
              <img src="assets/images/omani_halwa.jpg" alt="Authentic Kerala Black Halwa">
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="hero-slide-item slide-theme-2">
            <div class="slide-content-left">
              <span class="slide-tag-pill" style="background: #7C3AED;">🎁 56.29% OFF • GIFT SPECIAL</span>
              <h2 class="slide-main-heading">
                5 Premium Varieties <span>Heritage Gift Box</span>
              </h2>
              <p class="slide-sub-text">
                5 snack-sized snack packs in a royal purple keepsake gift box. The ultimate celebration treat for family & friends.
              </p>
              <a href="#heritage-spotlight" class="btn-slide-cta" style="background: #7C3AED;">
                <span>Order Heritage Box (Rs. 699)</span>
                <span>→</span>
              </a>
            </div>
            <div class="slide-image-box">
              <img src="assets/images/gift_box.jpg" alt="5 Premium Varieties Heritage Box">
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="hero-slide-item slide-theme-3">
            <div class="slide-content-left">
              <span class="slide-tag-pill" style="background: #059669;">🌿 100% PURE INGREDIENTS</span>
              <h2 class="slide-main-heading">
                Jackfruit & Tender <span>Coconut Halwas</span>
              </h2>
              <p class="slide-sub-text">
                Fresh seasonal Chakka Varatti & Elaneer halwas crafted with time-tested heritage recipes since 1990.
              </p>
              <a href="#single-varieties" class="btn-slide-cta" style="background: #059669;">
                <span>Explore Fruit Halwas</span>
                <span>→</span>
              </a>
            </div>
            <div class="slide-image-box">
              <img src="assets/images/sooji_halwa.jpg" alt="Jackfruit Halwa">
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button type="button" class="slider-arrow-btn slider-arrow-prev" onclick="window.haweliApp.prevSlide()" aria-label="Previous Slide">
          ‹
        </button>
        <button type="button" class="slider-arrow-btn slider-arrow-next" onclick="window.haweliApp.nextSlide()" aria-label="Next Slide">
          ›
        </button>

        <!-- Pagination Dots -->
        <div class="slider-dots-row" id="slider-dots-container">
          <div class="slider-dot active" onclick="window.haweliApp.goToSlide(0)"></div>
          <div class="slider-dot" onclick="window.haweliApp.goToSlide(1)"></div>
          <div class="slider-dot" onclick="window.haweliApp.goToSlide(2)"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Category Arrow Navigation Strip -->
  <div class="container category-arrow-bar">
    <div class="category-arrow-list">
      <a href="#single-varieties" class="category-arrow-link active" onclick="window.haweliApp.filterCategory('all', this)">
        Single Varieties Of Halwa →
      </a>
      <a href="#single-varieties" class="category-arrow-link" onclick="window.haweliApp.filterCategory('fruit', this)">
        Fruit Halwa →
      </a>
      <a href="#heritage-spotlight" class="category-arrow-link" onclick="window.haweliApp.filterCategory('combo', this)">
        Halwa Combo →
      </a>
      <a href="#heritage-spotlight" class="category-arrow-link">
        Special Deals →
      </a>
    </div>
  </div>

  <!-- Single Varieties Of Halwa Section -->
  <main class="container" id="single-varieties">
    <h1 class="haweli-section-title">Single Varieties Of Halwa</h1>

    <!-- Products Grid -->
    <div class="haweli-products-grid" id="haweli-products-grid">
      <!-- Injected via JavaScript -->
    </div>
  </main>

  <!-- Trust & Quality Badges Bar -->
  <section class="container">
    <div class="trust-badges-bar">
      <div class="trust-badge-card">
        <div class="trust-icon-hex" style="background: #E8F5E9; border: 2px solid #4CAF50; color: #2E7D32;">
          <span style="font-size: 14px; font-weight: 800;">fssai</span>
        </div>
        <div class="trust-label">FSSAI Certificate</div>
      </div>

      <div class="trust-badge-card">
        <div class="trust-icon-hex" style="background: #E1F5FE; border: 2px solid #03A9F4; color: #0277BD;">
          <span style="font-size: 16px; font-weight: 800;">1990</span>
        </div>
        <div class="trust-label">Since 1990</div>
      </div>

      <div class="trust-badge-card">
        <div class="trust-icon-hex" style="background: #FFF8E1; border: 2px solid #FFC107; color: #FF8F00;">
          <span>🚚</span>
        </div>
        <div class="trust-label">Free Delivery Above Rs. 499</div>
      </div>

      <div class="trust-badge-card">
        <div class="trust-icon-hex" style="background: #F1F8E9; border: 2px solid #8BC34A; color: #558B2F;">
          <span style="font-size: 13px; font-weight: 800;">2000kg</span>
        </div>
        <div class="trust-label">2000 kg Sold Per Week</div>
      </div>
    </div>
  </section>

  <!-- 5 Premium Varieties Spotlight Section -->
  <section class="container spotlight-heritage-section" id="heritage-spotlight">
    <div class="spotlight-grid">
      <div class="spotlight-image-card">
        <span class="badge-discount-orange" style="top: 14px; right: 14px;">56.29% OFF</span>
        <img src="assets/images/gift_box.jpg" alt="5 Premium Varieties of Authentic Halwa Heritage Box">
        
        <div class="packaging-callout callout-top-right">
          ↖ Snack-sized Packs
        </div>
        <div class="packaging-callout callout-bottom-left">
          ↗ Time-Tested Recipe
        </div>
        <div class="packaging-callout callout-bottom-right">
          ↖ 5 Unique Flavours
        </div>
      </div>

      <div class="spotlight-content">
        <span class="brand-subtitle-tag">HALWA HAWELI</span>
        <h2 class="spotlight-title">5 Premium Varieties of Authentic Halwa</h2>

        <div class="card-rating-row" style="margin-bottom: 12px;">
          <span class="stars-teal">★★★★★</span>
          <span class="review-count-text">6 reviews</span>
        </div>

        <div class="price-large-row">
          <span class="price-large-strikethrough">Rs. 1,599.00</span>
          <span class="price-large-active">Rs. 699.00</span>
          <span class="badge-sale-black">Sale</span>
        </div>

        <p class="shipping-note">Shipping calculated at checkout.</p>

        <div class="weight-selection-group">
          <div style="font-size: 13px; font-weight: 600; color: #374151;">Weight</div>
          <div class="weight-btn-pill">700 g</div>
        </div>

        <div class="quantity-stepper-row">
          <div style="font-size: 13px; font-weight: 600; color: #374151;">Quantity</div>
          <div class="stepper-box">
            <button type="button" class="step-btn" onclick="window.haweliApp.adjustSpotlightQty(-1)">−</button>
            <span class="step-value" id="spotlight-qty-val">1</span>
            <button type="button" class="step-btn" onclick="window.haweliApp.adjustSpotlightQty(1)">+</button>
          </div>
        </div>

        <button type="button" class="btn-spotlight-add" onclick="window.haweliApp.addSpotlightBox()">
          Add to cart
        </button>
      </div>
    </div>
  </section>

  <!-- Slide-Out Cart Drawer -->
  <div class="cart-drawer-backdrop" id="cart-drawer" onclick="if(event.target === this) window.haweliApp.closeCart()">
    <aside class="cart-drawer-panel">
      <div class="cart-drawer-head">
        <h3>Shopping Cart</h3>
        <button type="button" class="btn-close-cart" onclick="window.haweliApp.closeCart()">✕</button>
      </div>

      <div class="cart-items-container" id="cart-items-list">
        <!-- Injected via JS -->
      </div>

      <div class="cart-drawer-foot">
        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
          <input type="text" id="coupon-box" placeholder="Discount Code (e.g. HALWA10)" style="flex: 1; padding: 8px 10px; border: 1px solid #E5E7EB; border-radius: 6px; font-size: 13px; text-transform: uppercase;">
          <button type="button" onclick="window.haweliApp.applyDiscount()" style="padding: 8px 14px; background: #111827; color: #FFF; border-radius: 6px; font-size: 13px; font-weight: 700;">Apply</button>
        </div>

        <div class="cart-total-row">
          <span>Total:</span>
          <span id="cart-total-amount">Rs. 0.00</span>
        </div>

        <button type="button" class="btn-checkout-haweli" onclick="window.haweliApp.openCheckout()">
          Proceed to Checkout
        </button>
      </div>
    </aside>
  </div>

  <!-- Checkout Modal -->
  <div class="modal-overlay" id="checkout-modal" onclick="if(event.target === this) window.haweliApp.closeCheckout()">
    <div class="modal-dialog">
      <div class="modal-head">
        <h3 style="font-size: 18px; font-weight: 800;">Complete Your Order</h3>
        <button type="button" class="btn-close-cart" onclick="window.haweliApp.closeCheckout()">✕</button>
      </div>
      <div class="modal-body" id="checkout-modal-body">
        <form id="haweli-checkout-form" onsubmit="window.haweliApp.placeOrder(event)">
          <div id="checkout-summary-box" style="background: #F9FAFB; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;"></div>

          <div style="margin-bottom: 12px;">
            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Full Name *</label>
            <input type="text" id="cust-name" required placeholder="Enter your name" style="width: 100%; padding: 10px; border: 1px solid #E5E7EB; border-radius: 6px; font-size: 14px;">
          </div>

          <div style="margin-bottom: 12px;">
            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Phone Number *</label>
            <input type="tel" id="cust-phone" required placeholder="e.g. +91 9876543210" style="width: 100%; padding: 10px; border: 1px solid #E5E7EB; border-radius: 6px; font-size: 14px;">
          </div>

          <div style="margin-bottom: 12px;">
            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Delivery Address *</label>
            <input type="text" id="cust-address" required placeholder="House No, Street, Landmark, Pin Code" style="width: 100%; padding: 10px; border: 1px solid #E5E7EB; border-radius: 6px; font-size: 14px;">
          </div>

          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Payment Method</label>
            <div style="display: flex; gap: 16px; font-size: 14px;">
              <label><input type="radio" name="pay_opt" value="Cash on Delivery" checked> Cash on Delivery</label>
              <label><input type="radio" name="pay_opt" value="UPI / Online"> UPI / Online Payment</label>
            </div>
          </div>

          <button type="submit" class="btn-checkout-haweli">
            Confirm & Place Order
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Floating WhatsApp Widget -->
  <a href="https://wa.me/" target="_blank" class="whatsapp-floating-btn" aria-label="Contact on WhatsApp">
    <svg viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.23.9 2.43 1.03 2.6.13.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>
  </a>

  <script src="assets/js/app.js"></script>
</body>
</html>
