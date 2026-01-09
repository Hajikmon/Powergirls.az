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

$error = '';
$categories = get_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    $error = 'CSRF error';
  } else {
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? $product['category']);
    $price = (float)($_POST['price'] ?? 0);
    $discount = (int)($_POST['discount_percent'] ?? 0);
    $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
    $title_az = trim($_POST['title_az'] ?? '');
    $title_ru = trim($_POST['title_ru'] ?? '');
    $title_en = trim($_POST['title_en'] ?? '');
    $short_az = trim($_POST['short_desc_az'] ?? '');
    $short_ru = trim($_POST['short_desc_ru'] ?? '');
    $short_en = trim($_POST['short_desc_en'] ?? '');
    $full_az = trim($_POST['full_desc_az'] ?? '');
    $full_ru = trim($_POST['full_desc_ru'] ?? '');
    $full_en = trim($_POST['full_desc_en'] ?? '');

    if ($slug === '' || $title_az === '' || $title_ru === '' || $title_en === '') {
      $error = 'Zehmet olmasa butun basliqlari doldurun.';
    } elseif ($category === '') {
      $error = 'Kateqoriya secin.';
    } else {
      $upd = db()->prepare('UPDATE products SET slug=:slug, category=:cat, price=:price, discount_percent=:discount, status=:status,
        is_new=:is_new, is_bestseller=:is_best, title_az=:taz, title_ru=:tru, title_en=:ten,
        short_desc_az=:saz, short_desc_ru=:sru, short_desc_en=:sen, full_desc_az=:faz, full_desc_ru=:fru, full_desc_en=:fen,
        updated_at=NOW() WHERE id=:id');
      $upd->execute([
        ':slug' => $slug,
        ':cat' => $category,
        ':price' => $price,
        ':discount' => $discount,
        ':status' => $status,
        ':is_new' => $is_new,
        ':is_best' => $is_bestseller,
        ':taz' => $title_az,
        ':tru' => $title_ru,
        ':ten' => $title_en,
        ':saz' => $short_az,
        ':sru' => $short_ru,
        ':sen' => $short_en,
        ':faz' => $full_az,
        ':fru' => $full_ru,
        ':fen' => $full_en,
        ':id' => $id
      ]);

      $primary_stmt = db()->prepare('SELECT id FROM product_images WHERE product_id = :id AND is_primary = 1 ORDER BY id DESC LIMIT 1');
      $primary_stmt->execute([':id' => $id]);
      $primary_row = $primary_stmt->fetch();
      $has_primary = $primary_row && isset($primary_row['id']);
      $inserted_primary = false;

      if (!empty($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $path = upload_product_image($_FILES['main_image']);
        if ($path) {
          db()->prepare('UPDATE product_images SET is_primary = 0 WHERE product_id = :id')->execute([':id' => $id]);
          if ($has_primary) {
            $img = db()->prepare('UPDATE product_images SET file_path = :path, is_primary = 1, sort_order = 0 WHERE id = :img_id');
            $img->execute([':path' => $path, ':img_id' => $primary_row['id']]);
          } else {
            $img = db()->prepare('INSERT INTO product_images (product_id, file_path, is_primary, sort_order) VALUES (:pid, :path, 1, 0)');
            $img->execute([':pid' => $id, ':path' => $path]);
            $inserted_primary = true;
          }
        } else {
          $error = 'Esas shekil yuklenmedi. JPG/PNG/WEBP/SVG istifade edin.';
        }
      }

      $count_stmt = db()->prepare('SELECT COUNT(*) as c FROM product_images WHERE product_id = :id');
      $count_stmt->execute([':id' => $id]);
      $existing_count = (int)($count_stmt->fetch()['c'] ?? 0);
      if ($inserted_primary) {
        $existing_count++;
      }
      $available = max(0, 6 - $existing_count);

      if ($error === '' && $available > 0 && !empty($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
        $count = 0;
        $sort_stmt = db()->prepare('SELECT MAX(sort_order) as m FROM product_images WHERE product_id = :id');
        $sort_stmt->execute([':id' => $id]);
        $sort = (int)($sort_stmt->fetch()['m'] ?? 0) + 1;
        foreach ($_FILES['gallery_images']['name'] as $i => $name) {
          if ($count >= $available) {
            break;
          }
          $file = [
            'name' => $name,
            'type' => $_FILES['gallery_images']['type'][$i] ?? '',
            'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i] ?? '',
            'error' => $_FILES['gallery_images']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size' => $_FILES['gallery_images']['size'][$i] ?? 0
          ];
          if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
          }
          $path = upload_product_image($file);
          if (!$path) {
            $error = 'Sekil yuklenmedi. JPG/PNG/WEBP/SVG istifade edin.';
            break;
          }
          $img = db()->prepare('INSERT INTO product_images (product_id, file_path, is_primary, sort_order) VALUES (:pid, :path, 0, :sort)');
          $img->execute([':pid' => $id, ':path' => $path, ':sort' => $sort]);
          $sort++;
          $count++;
        }
      }
    }
  }
  if ($error === '') {
    header('Location: products_list.php');
    exit;
  }
}

$imgs = db()->prepare('SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC');
$imgs->execute([':id' => $id]);
$images = $imgs->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Mehsul duzelt</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Mehsul duzelt</h1>
      <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
      <form class="order-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <label>Slug <input type="text" name="slug" value="<?php echo e($product['slug']); ?>" required /></label>
        <label>Kateqoriya
          <select name="category" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo e($cat['slug']); ?>" <?php echo $product['category'] === $cat['slug'] ? 'selected' : ''; ?>>
                <?php echo e($cat['name_az']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Qiymet <input type="number" step="0.01" name="price" value="<?php echo e((string)$product['price']); ?>" required /></label>
        <label>Endirim (%) <input type="number" name="discount_percent" value="<?php echo e((string)$product['discount_percent']); ?>" /></label>
        <label>Status
          <select name="status">
            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>active</option>
            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
          </select>
        </label>
        <label><input type="checkbox" name="is_new" <?php echo (int)$product['is_new'] === 1 ? 'checked' : ''; ?> /> Yeni</label>
        <label><input type="checkbox" name="is_bestseller" <?php echo (int)$product['is_bestseller'] === 1 ? 'checked' : ''; ?> /> Bestseller</label>
        <label>Yeni esas shekil (jpg/png/webp/svg) <input type="file" name="main_image" /></label>
        <label>Elave shekiller (max 5) <input type="file" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp,.svg" /></label>

        <label>Basliq AZ <input type="text" name="title_az" value="<?php echo e($product['title_az']); ?>" required /></label>
        <label>Basliq RU <input type="text" name="title_ru" value="<?php echo e($product['title_ru']); ?>" required /></label>
        <label>Basliq EN <input type="text" name="title_en" value="<?php echo e($product['title_en']); ?>" required /></label>

        <label>Qisa tesvir AZ <textarea name="short_desc_az"><?php echo e($product['short_desc_az'] ?? ''); ?></textarea></label>
        <label>Qisa tesvir RU <textarea name="short_desc_ru"><?php echo e($product['short_desc_ru'] ?? ''); ?></textarea></label>
        <label>Qisa tesvir EN <textarea name="short_desc_en"><?php echo e($product['short_desc_en'] ?? ''); ?></textarea></label>

        <label>Full tesvir AZ <textarea name="full_desc_az"><?php echo e($product['full_desc_az'] ?? ''); ?></textarea></label>
        <label>Full tesvir RU <textarea name="full_desc_ru"><?php echo e($product['full_desc_ru'] ?? ''); ?></textarea></label>
        <label>Full tesvir EN <textarea name="full_desc_en"><?php echo e($product['full_desc_en'] ?? ''); ?></textarea></label>

        <?php if ($images): ?>
          <div class="card" style="margin-bottom:16px;">
            <?php foreach ($images as $img): ?>
              <span style="display:inline-block; margin-right:8px;">
                <img src="../<?php echo e($img['file_path']); ?>" alt="" style="width:70px; height:70px; object-fit:cover; border-radius:10px; display:block;" />
                <?php if ((int)$img['is_primary'] === 1): ?>
                  <small>Esas</small>
                <?php endif; ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <button class="btn primary" type="submit">Yadda saxla</button>
        <a class="btn ghost" href="products_list.php">Geri</a>
      </form>
    </div>
  </section>
</body>
</html>
