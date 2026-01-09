<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stmt = db()->query('SELECT p.*, (SELECT file_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS thumb
  FROM products p ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Mehsullar</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Mehsullar</h1>
      <p>
        <a class="btn primary" href="products_add.php">Mehsul elave et</a>
        <a class="btn ghost" href="index.php">Geri</a>
      </p>
      <div class="card">
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left; padding:8px;">Mehsul</th>
              <th style="text-align:left; padding:8px;">Qiymet</th>
              <th style="text-align:left; padding:8px;">Status</th>
              <th style="text-align:left; padding:8px;">Emeliyyat</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td style="padding:8px;">
                <?php if (!empty($p['thumb'])): ?>
                  <img src="../<?php echo e($p['thumb']); ?>" alt="" style="width:44px; height:44px; object-fit:cover; border-radius:8px; vertical-align:middle; margin-right:10px;" />
                <?php endif; ?>
                <?php echo e(product_title($p)); ?>
              </td>
              <td style="padding:8px;"><?php echo e(format_price((float)$p['price'])); ?></td>
              <td style="padding:8px;"><?php echo e($p['status']); ?></td>
              <td style="padding:8px;">
                <a class="btn ghost" href="products_edit.php?id=<?php echo e((string)$p['id']); ?>">Duzelt</a>
                <a class="btn ghost" href="products_delete.php?id=<?php echo e((string)$p['id']); ?>">Sil</a>
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
