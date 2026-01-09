<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';

$lang = current_lang();
$settings = get_settings();
$page_title = 'Sevdiklərim';
$page_desc = '';
$enable_favs = true;

include __DIR__ . '/includes/header.php';
?>

<section class="section" data-favs-page>
  <div class="container">
    <div class="section-head">
      <h1>Sevdiklərim</h1>
    </div>
    <p class="lead" data-favs-empty>Hələlik seçilmiş məhsul yoxdur.</p>
    <div class="grid-3 products favs-grid" data-favs-list></div>
    <div class="fav-page-actions">
      <a class="btn primary" data-fav-wa data-wa="https://wa.me/<?php echo e(clean_phone($settings['whatsapp_number'] ?? '')); ?>?text=" href="#" target="_blank">WhatsApp ilə göndər</a>
      <a class="btn ghost" data-fav-ig href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank">Instagram ilə göndər</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
