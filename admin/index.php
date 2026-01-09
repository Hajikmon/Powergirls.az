<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$settings = get_settings();
$campaign_status = $settings['campaign_status'] ?? 'OFF';
$giveaway_status = $settings['giveaway_status'] ?? 'OFF';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Panel</h1>
      <div class="grid-3">
        <div class="card">Kampaniya status: <?php echo e($campaign_status); ?></div>
        <div class="card">Hediyye cekilisi: <?php echo e($giveaway_status); ?></div>
        <div class="card">Son yenileme: <?php echo e(date('Y-m-d')); ?></div>
      </div>
      <p>
        <a class="btn primary" href="products_list.php">Mehsullar</a>
        <a class="btn ghost" href="products_add.php">Mehsul elave et</a>
        <a class="btn ghost" href="categories.php">Kateqoriyalar</a>
        <a class="btn ghost" href="orders_list.php">Sifarisler</a>
        <a class="btn ghost" href="campaigns.php">Kampaniyalar</a>
        <a class="btn ghost" href="giveaway.php">Hediyye cekilisi</a>
        <a class="btn ghost" href="settings.php">Ayarlar</a>
        <a class="btn ghost" href="logout.php">Cixis</a>
      </p>
    </div>
  </section>
</body>
</html>
