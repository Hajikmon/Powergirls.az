<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    $error = 'CSRF error';
  } elseif (!empty($_POST['update_slug'])) {
    $slug = trim($_POST['update_slug']);
    $name_az = trim($_POST['name_az'] ?? '');
    $name_ru = trim($_POST['name_ru'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $sort = (int)($_POST['sort_order'] ?? 0);

    if ($name_az === '' || $name_ru === '' || $name_en === '') {
      $error = 'Butun adlari doldurun.';
    } else {
      $upd = db()->prepare('UPDATE categories SET name_az = :az, name_ru = :ru, name_en = :en, sort_order = :sort WHERE slug = :slug');
      $upd->execute([
        ':az' => $name_az,
        ':ru' => $name_ru,
        ':en' => $name_en,
        ':sort' => $sort,
        ':slug' => $slug
      ]);
      $success = true;
    }
  } elseif (!empty($_POST['delete_slug'])) {
    $slug = trim($_POST['delete_slug']);
    $check = db()->prepare('SELECT COUNT(*) as c FROM products WHERE category = :slug');
    $check->execute([':slug' => $slug]);
    if ((int)($check->fetch()['c'] ?? 0) > 0) {
      $error = 'Bu kateqoriyada mehsul var. Evvel mehsullari deyisin.';
    } else {
      $del = db()->prepare('DELETE FROM categories WHERE slug = :slug');
      $del->execute([':slug' => $slug]);
      $success = true;
    }
  } else {
    $slug = trim($_POST['slug'] ?? '');
    $name_az = trim($_POST['name_az'] ?? '');
    $name_ru = trim($_POST['name_ru'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $sort = (int)($_POST['sort_order'] ?? 0);

    if ($slug === '' || $name_az === '' || $name_ru === '' || $name_en === '') {
      $error = 'Butun adlari doldurun.';
    } else {
      $stmt = db()->prepare('INSERT INTO categories (slug, name_az, name_ru, name_en, sort_order, created_at)
        VALUES (:slug, :az, :ru, :en, :sort, NOW())');
      try {
        $stmt->execute([
          ':slug' => $slug,
          ':az' => $name_az,
          ':ru' => $name_ru,
          ':en' => $name_en,
          ':sort' => $sort
        ]);
        $success = true;
      } catch (PDOException $e) {
        $error = 'Slug uniq olmalidir.';
      }
    }
  }
}

$categories = get_categories();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Kateqoriyalar</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Kateqoriyalar</h1>
      <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="success">Yadda saxlandi.</div><?php endif; ?>

      <div class="card" style="margin-bottom:18px;">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="text-align:left; padding:8px;">Slug</th>
              <th style="text-align:left; padding:8px;">AZ</th>
              <th style="text-align:left; padding:8px;">RU</th>
              <th style="text-align:left; padding:8px;">EN</th>
              <th style="text-align:left; padding:8px;">Siralama</th>
              <th style="text-align:left; padding:8px;">Emeliyyat</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($categories as $cat): ?>
            <?php $form_id = 'update-' . $cat['slug']; ?>
            <form method="post" id="<?php echo e($form_id); ?>">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="update_slug" value="<?php echo e($cat['slug']); ?>" />
            </form>
            <tr>
              <td class="admin-slug"><?php echo e($cat['slug']); ?></td>
              <td style="padding:8px;">
                <input type="text" name="name_az" value="<?php echo e($cat['name_az']); ?>" form="<?php echo e($form_id); ?>" />
              </td>
              <td style="padding:8px;">
                <input type="text" name="name_ru" value="<?php echo e($cat['name_ru']); ?>" form="<?php echo e($form_id); ?>" />
              </td>
              <td style="padding:8px;">
                <input type="text" name="name_en" value="<?php echo e($cat['name_en']); ?>" form="<?php echo e($form_id); ?>" />
              </td>
              <td style="padding:8px;">
                <input type="number" name="sort_order" value="<?php echo e($cat['sort_order']); ?>" form="<?php echo e($form_id); ?>" style="width:80px;" />
              </td>
              <td style="padding:8px;">
                <button class="btn primary" type="submit" form="<?php echo e($form_id); ?>">Yadda saxla</button>
                <form method="post" style="display:inline;">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="delete_slug" value="<?php echo e($cat['slug']); ?>" />
                  <button class="btn ghost" type="submit">Sil</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <form class="order-form" method="post">
        <?php echo csrf_field(); ?>
        <label>Slug <input type="text" name="slug" required /></label>
        <label>Ad AZ <input type="text" name="name_az" required /></label>
        <label>Ad RU <input type="text" name="name_ru" required /></label>
        <label>Ad EN <input type="text" name="name_en" required /></label>
        <label>Siralama <input type="number" name="sort_order" value="0" /></label>
        <button class="btn primary" type="submit">Elave et</button>
        <a class="btn ghost" href="index.php">Geri</a>
      </form>
    </div>
  </section>
</body>
</html>
