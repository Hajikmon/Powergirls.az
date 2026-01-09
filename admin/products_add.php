<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$error = '';
$categories = get_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    $error = 'CSRF error';
  } else {
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? ($categories[0]['slug'] ?? ''));
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
      $check = db()->prepare('SELECT COUNT(*) as c FROM products WHERE slug = :slug');
      $check->execute([':slug' => $slug]);
      if ((int)($check->fetch()['c'] ?? 0) > 0) {
        $error = 'Bu slug artiq movcuddur. Ferqli slug yazin.';
      }
    }
    if ($error === '') {
      $stmt = db()->prepare('INSERT INTO products (slug, category, price, discount_percent, status, is_new, is_bestseller,
        title_az, title_ru, title_en, short_desc_az, short_desc_ru, short_desc_en, full_desc_az, full_desc_ru, full_desc_en, created_at, updated_at)
        VALUES (:slug, :cat, :price, :discount, :status, :is_new, :is_best, :taz, :tru, :ten, :saz, :sru, :sen, :faz, :fru, :fen, NOW(), NOW())');
      $stmt->execute([
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
        ':fen' => $full_en
      ]);
      $product_id = (int)db()->lastInsertId();

      $primary_path = null;
      if (!empty($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $primary_path = upload_product_image($_FILES['main_image']);
        if (!$primary_path) {
          $error = 'Esas shekil yuklenmedi. JPG/PNG/WEBP/SVG istifade edin.';
        }
      }

      $gallery_paths = [];
      if ($error === '' && !empty($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
        $count = 0;
        foreach ($_FILES['gallery_images']['name'] as $i => $name) {
          if ($count >= 5) {
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
          $gallery_paths[] = $path;
          $count++;
        }
      }

      if ($error === '') {
        if (!$primary_path && $gallery_paths) {
          $primary_path = array_shift($gallery_paths);
        }
        if ($primary_path) {
          $img = db()->prepare('INSERT INTO product_images (product_id, file_path, is_primary, sort_order) VALUES (:pid, :path, 1, 0)');
          $img->execute([':pid' => $product_id, ':path' => $primary_path]);
        }
        $sort = 1;
        foreach ($gallery_paths as $path) {
          $img = db()->prepare('INSERT INTO product_images (product_id, file_path, is_primary, sort_order) VALUES (:pid, :path, 0, :sort)');
          $img->execute([':pid' => $product_id, ':path' => $path, ':sort' => $sort]);
          $sort++;
        }
      }
    }
  }
  if ($error === '') {
    header('Location: products_list.php');
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Mehsul elave et</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Mehsul elave et</h1>
      <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
      <form class="order-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <label>Slug <input type="text" name="slug" required /></label>
        <label>Kateqoriya
          <select name="category" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo e($cat['slug']); ?>"><?php echo e($cat['name_az']); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Qiymet <input type="number" step="0.01" name="price" required /></label>
        <label>Endirim (%) <input type="number" name="discount_percent" value="0" /></label>
        <label>Status
          <select name="status">
            <option value="active">active</option>
            <option value="inactive">inactive</option>
          </select>
        </label>
        <label><input type="checkbox" name="is_new" /> Yeni</label>
        <label><input type="checkbox" name="is_bestseller" /> Bestseller</label>
        <label>Esas shekil (jpg/png/webp/svg) <input type="file" name="main_image" /></label>
        <label>Elave shekiller (max 5) <input type="file" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp,.svg" /></label>

        <label>Basliq AZ <input type="text" name="title_az" required /></label>
        <label>Basliq RU <input type="text" name="title_ru" required /></label>
        <label>Basliq EN <input type="text" name="title_en" required /></label>

        <label>Qisa tesvir AZ <textarea name="short_desc_az"></textarea></label>
        <label>Qisa tesvir RU <textarea name="short_desc_ru"></textarea></label>
        <label>Qisa tesvir EN <textarea name="short_desc_en"></textarea></label>

        <label>Full tesvir AZ <textarea name="full_desc_az"></textarea></label>
        <label>Full tesvir RU <textarea name="full_desc_ru"></textarea></label>
        <label>Full tesvir EN <textarea name="full_desc_en"></textarea></label>

        <button class="btn primary" type="submit">Yadda saxla</button>
        <a class="btn ghost" href="products_list.php">Geri</a>
      </form>
    </div>
  </section>
</body>
</html>
