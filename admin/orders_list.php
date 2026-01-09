<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$orders = db()->query('SELECT id, created_at, status, customer_name, customer_phone FROM orders ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Sifarishler</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Sifarishler</h1>
      <p><a class="btn ghost" href="index.php">Geri</a></p>
      <div class="card">
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left; padding:8px;">ID</th>
              <th style="text-align:left; padding:8px;">Ad</th>
              <th style="text-align:left; padding:8px;">Telefon</th>
              <th style="text-align:left; padding:8px;">Status</th>
              <th style="text-align:left; padding:8px;">Tarix</th>
              <th style="text-align:left; padding:8px;">Emeliyyat</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td style="padding:8px;"><?php echo e((string)$o['id']); ?></td>
              <td style="padding:8px;"><?php echo e($o['customer_name']); ?></td>
              <td style="padding:8px;"><?php echo e($o['customer_phone']); ?></td>
              <td style="padding:8px;"><?php echo e($o['status']); ?></td>
              <td style="padding:8px;"><?php echo e($o['created_at']); ?></td>
              <td style="padding:8px;">
                <a class="btn ghost" href="orders_view.php?id=<?php echo e((string)$o['id']); ?>">Bax</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</body>
</html>
