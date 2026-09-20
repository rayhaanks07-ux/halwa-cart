<?php
/**
 * Zaika Shahi — Kitchen Admin Dashboard (PHP)
 */
require_once __DIR__ . '/backend/db.php';
$orders = HalwaDB::readJson('orders.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kitchen Management Console — Zaika Shahi</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏺</text></svg>">
  <style>
    .admin-wrap {
      max-width: 1140px;
      margin: 40px auto 80px;
      padding: 0 20px;
    }
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-bottom: 30px;
    }
    .kpi-card {
      background: #FFF;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-sm);
      padding: 20px;
    }
    .kpi-card .kpi-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--color-text-muted);
      font-weight: 600;
    }
    .kpi-card .kpi-val {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--color-primary);
      margin-top: 4px;
    }
    .table-container {
      background: #FFF;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-sm);
      overflow-x: auto;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.875rem;
      text-align: left;
    }
    table.data-table th {
      background: var(--color-bg);
      padding: 12px 16px;
      font-weight: 600;
      color: var(--color-primary);
      border-bottom: 1px solid var(--color-border);
      white-space: nowrap;
    }
    table.data-table td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--color-border);
      vertical-align: middle;
    }
    .status-dropdown {
      padding: 6px 10px;
      border-radius: var(--radius-xs);
      font-size: 0.8125rem;
      font-weight: 500;
      border: 1px solid var(--color-border);
      background: var(--color-bg);
      cursor: pointer;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="site-header">
    <div class="container header-row">
      <a href="index.php" class="brand">
        <div class="brand-monogram">Z</div>
        <div class="brand-text">
          <span class="brand-title">ZAIKA SHAHI</span>
          <span class="brand-subtitle">Kitchen & Operations Console</span>
        </div>
      </a>

      <nav class="main-nav">
        <a href="index.php" class="nav-item">Storefront</a>
        <a href="track.php" class="nav-item">Track Order</a>
        <a href="admin.php" class="nav-item active">Console</a>
      </nav>

      <div class="header-utilities">
        <button type="button" class="btn-cta-secondary" onclick="window.location.reload()" style="padding: 8px 16px; font-size: 0.8125rem;">
          Refresh Orders
        </button>
      </div>
    </div>
  </header>

  <main class="admin-wrap">
    <div style="margin-bottom: 24px;">
      <h1 style="font-family: var(--font-serif); font-size: 2rem; color: var(--color-primary); margin-bottom: 4px;">Kitchen Orders Management</h1>
      <p style="color: var(--color-text-muted); font-size: 0.875rem;">Monitor preparation stages, order itemization, and dispatch status.</p>
    </div>

    <!-- KPIs -->
    <div class="kpi-row">
      <div class="kpi-card">
        <div class="kpi-label">Active Orders</div>
        <div class="kpi-val" id="stat-total-orders"><?= count($orders) ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Simmering in Cauldron</div>
        <div class="kpi-val" style="color: var(--color-accent);" id="stat-active-cooking">
          <?= count(array_filter($orders, fn($o) => in_array($o['status'], ['cooking', 'received']))) ?>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Out for Courier Delivery</div>
        <div class="kpi-val" style="color: var(--color-success);" id="stat-out-delivery">
          <?= count(array_filter($orders, fn($o) => $o['status'] === 'out_for_delivery')) ?>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-val" id="stat-revenue">
          $<?= number_format(array_sum(array_column($orders, 'total')), 2) ?>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Order Ref</th>
            <th>Customer</th>
            <th>Confections</th>
            <th>Total</th>
            <th>Dispatch Slot</th>
            <th>Status</th>
            <th>Lookup</th>
          </tr>
        </thead>
        <tbody id="admin-orders-tbody">
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>
                <strong style="font-family: var(--font-serif); color: var(--color-primary); font-size: 0.95rem;"><?= htmlspecialchars($o['order_id']) ?></strong>
              </td>
              <td>
                <strong><?= htmlspecialchars($o['customer']['name']) ?></strong><br>
                <span style="color: var(--color-text-muted); font-size: 0.75rem;"><?= htmlspecialchars($o['customer']['phone']) ?></span>
              </td>
              <td>
                <?php foreach ($o['items'] as $i): ?>
                  <div style="font-size: 0.8125rem;">• <?= intval($i['quantity']) ?>x <?= htmlspecialchars($i['name']) ?> <span style="color: var(--color-text-muted);">(<?= htmlspecialchars($i['size']) ?>)</span></div>
                <?php endforeach; ?>
              </td>
              <td>
                <strong>$<?= number_format(floatval($o['total']), 2) ?></strong>
              </td>
              <td>
                <span style="font-size: 0.8125rem;"><?= htmlspecialchars($o['delivery_slot'] ?? 'Express') ?></span>
              </td>
              <td>
                <select class="status-dropdown" onchange="updateStatus('<?= htmlspecialchars($o['order_id']) ?>', this.value)">
                  <option value="received" <?= $o['status'] === 'received' ? 'selected' : '' ?>>Received</option>
                  <option value="cooking" <?= $o['status'] === 'cooking' ? 'selected' : '' ?>>Simmering</option>
                  <option value="packed" <?= $o['status'] === 'packed' ? 'selected' : '' ?>>Packed</option>
                  <option value="out_for_delivery" <?= $o['status'] === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                  <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                </select>
              </td>
              <td>
                <a href="track.php?order_id=<?= htmlspecialchars($o['order_id']) ?>" target="_blank" class="btn-utility-track" style="padding: 4px 10px; font-size: 0.75rem;">
                  View
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script src="assets/js/app.js"></script>
  <script>
    async function updateStatus(orderId, newStatus) {
      try {
        const res = await fetch('backend/api.php?action=update_order_status', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order_id: orderId, status: newStatus })
        });
        const data = await res.json();
        if (data.success) {
          window.location.reload();
        }
      } catch (e) {
        alert('Server unreachable');
      }
    }
  </script>
</body>
</html>
