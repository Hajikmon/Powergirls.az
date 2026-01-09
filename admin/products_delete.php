<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) {
  header('Location: products_list.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) die('CSRF error');

  $imgs = db()->prepare('SELECT file_path FROM product_images WHERE product_id = :id');
  $imgs->execute([':id' => $id]);
  foreach ($imgs->fetchAll() as $img) {
    delete_product_image_file($img['file_path']);
  }
  db()->prepare('DELETE FROM products WHERE id = :id')->execute([':id' => $id]);
  header('Location: products_list.php');
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Mehsul sil</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Mehsul sil</h1>
      <div class="alert">"<?php echo e(product_title($product)); ?>" silinsin?</div>
      <form method="post">
        <?php echo csrf_field(); ?>
        <button class="btn primary" type="submit">Beli, sil</button>
        <a class="btn ghost" href="products_list.php">Imtina</a>
      </form>
    </div>
  </section>
</body>
</html>
