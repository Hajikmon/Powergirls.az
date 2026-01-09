<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$settings = get_settings();
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    $error = 'CSRF error';
  } else {
    $status = ($_POST['campaign_status'] ?? 'OFF') === 'ON' ? 'ON' : 'OFF';
    $title_az = trim($_POST['campaign_title_az'] ?? '');
    $title_ru = trim($_POST['campaign_title_ru'] ?? '');
    $title_en = trim($_POST['campaign_title_en'] ?? '');
    $text_az = trim($_POST['campaign_text_az'] ?? '');
    $text_ru = trim($_POST['campaign_text_ru'] ?? '');
    $text_en = trim($_POST['campaign_text_en'] ?? '');

    $image_path = $settings['campaign_image_path'] ?? '';
    if (!empty($_POST['remove_campaign_image']) && $image_path) {
      delete_uploaded_file($image_path, 'campaigns');
      $image_path = '';
    }
    if (!empty($_FILES['campaign_image']) && $_FILES['campaign_image']['error'] !== UPLOAD_ERR_NO_FILE) {
      $uploaded = upload_image($_FILES['campaign_image'], 'campaigns');
      if ($uploaded) {
        if ($image_path) {
          delete_uploaded_file($image_path, 'campaigns');
        }
        $image_path = $uploaded;
      } else {
        $error = 'Image upload failed. Use jpg/png/webp only.';
      }
    }

    if ($error === '') {
      $stmt = db()->prepare('UPDATE settings SET campaign_status=:status, campaign_title_az=:taz, campaign_title_ru=:tru, campaign_title_en=:ten,
        campaign_text_az=:xaz, campaign_text_ru=:xru, campaign_text_en=:xen, campaign_image_path=:img, updated_at=NOW() WHERE id=1');
      $stmt->execute([
        ':status' => $status,
        ':taz' => $title_az,
        ':tru' => $title_ru,
        ':ten' => $title_en,
        ':xaz' => $text_az,
        ':xru' => $text_ru,
        ':xen' => $text_en,
        ':img' => $image_path
      ]);
      $success = true;
      $settings = get_settings();
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Kampaniyalar</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Kampaniyalar</h1>
      <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="success">Yadda saxlandi.</div><?php endif; ?>
      <form class="order-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <label>Kampaniya status
          <select name="campaign_status">
            <option value="ON" <?php echo ($settings['campaign_status'] ?? '') === 'ON' ? 'selected' : ''; ?>>ON</option>
            <option value="OFF" <?php echo ($settings['campaign_status'] ?? '') === 'OFF' ? 'selected' : ''; ?>>OFF</option>
          </select>
        </label>
        <label>Basliq AZ <input type="text" name="campaign_title_az" value="<?php echo e($settings['campaign_title_az'] ?? ''); ?>" /></label>
        <label>Basliq RU <input type="text" name="campaign_title_ru" value="<?php echo e($settings['campaign_title_ru'] ?? ''); ?>" /></label>
        <label>Basliq EN <input type="text" name="campaign_title_en" value="<?php echo e($settings['campaign_title_en'] ?? ''); ?>" /></label>
        <label>Metn AZ <textarea name="campaign_text_az"><?php echo e($settings['campaign_text_az'] ?? ''); ?></textarea></label>
        <label>Metn RU <textarea name="campaign_text_ru"><?php echo e($settings['campaign_text_ru'] ?? ''); ?></textarea></label>
        <label>Metn EN <textarea name="campaign_text_en"><?php echo e($settings['campaign_text_en'] ?? ''); ?></textarea></label>
        <label>Shekil yukle (jpg/png/webp)
          <input type="file" name="campaign_image" accept=".jpg,.jpeg,.png,.webp" />
        </label>
        <?php if (!empty($settings['campaign_image_path'])): ?>
          <div class="card" style="margin-bottom:16px;">
            <img src="../<?php echo e($settings['campaign_image_path']); ?>" alt="Campaign image" style="max-width:240px; border-radius:12px;" />
            <label style="margin-top:12px; display:block;">
              <input type="checkbox" name="remove_campaign_image" value="1" /> Shekli sil
            </label>
          </div>
        <?php endif; ?>
        <button class="btn primary" type="submit">Yadda saxla</button>
        <a class="btn ghost" href="index.php">Geri</a>
      </form>
    </div>
  </section>
</body>
</html>
