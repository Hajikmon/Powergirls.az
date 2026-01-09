<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT o.*, p.title_az AS product_title FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.id = :id');
$stmt->execute([':id' => $id]);
$order = $stmt->fetch();
if (!$order) {
  header('Location: orders_list.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) die('CSRF error');
  $status = trim($_POST['status'] ?? 'new');
  db()->prepare('UPDATE orders SET status = :s WHERE id = :id')->execute([':s' => $status, ':id' => $id]);
  header('Location: orders_view.php?id=' . $id);
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Sifaris #<?php echo e((string)$order['id']); ?></title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Sifaris #<?php echo e((string)$order['id']); ?></h1>
      <div class="card">
        <p><strong>Mehsul:</strong> <?php echo e($order['product_title'] ?? ''); ?></p>
        <p><strong>Ad:</strong> <?php echo e($order['customer_name']); ?></p>
        <p><strong>Telefon:</strong> <?php echo e($order['customer_phone']); ?></p>
        <p><strong>Seher:</strong> <?php echo e($order['city']); ?></p>
        <p><strong>Unvan:</strong> <?php echo e($order['address']); ?></p>
        <p><strong>Catdirilma:</strong> <?php echo e($order['delivery_type']); ?></p>
        <p><strong>Miqdar:</strong> <?php echo e((string)$order['quantity']); ?></p>
        <p><strong>Qeyd:</strong> <?php echo e($order['note']); ?></p>
        <p><strong>Status:</strong> <?php echo e($order['status']); ?></p>
        <p><strong>Tarix:</strong> <?php echo e($order['created_at']); ?></p>
      </div>
      <form class="order-form" method="post" style="margin-top:16px;">
        <?php echo csrf_field(); ?>
        <label>Status
          <select name="status">
            <option value="new" <?php echo $order['status'] === 'new' ? 'selected' : ''; ?>>new</option>
            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>processing</option>
            <option value="done" <?php echo $order['status'] === 'done' ? 'selected' : ''; ?>>done</option>
          </select>
        </label>
        <button class="btn primary" type="submit">Yadda saxla</button>
        <a class="btn ghost" href="orders_list.php">Geri</a>
      </form>
    </div>
  </section>
</body>
</html>
