<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';

$lang = current_lang();
$settings = get_settings();
$page_title = t('contact_title');
$page_desc = $settings['default_meta_description_' . $lang] ?? '';

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h1><?php echo e(t('contact_title')); ?></h1>
      <p class="lead"><?php echo e(t('contact_subtitle')); ?></p>
    </div>
    <div class="grid-2">
      <div class="card">
        <h3><?php echo e(t('contact_channels')); ?></h3>
        <p><?php echo e(t('contact_text')); ?></p>
        <div class="cta-row">
          <a class="btn primary" href="https://wa.me/<?php echo e(clean_phone($settings['whatsapp_number'] ?? '')); ?>" target="_blank" data-track="whatsapp_click">WhatsApp</a>
          <a class="btn ghost" href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank" data-track="instagram_click">Instagram</a>
        </div>
      </div>
      <div class="card">
        <h3><?php echo e(t('contact_hours')); ?></h3>
        <p><?php echo e(t('contact_hours_text')); ?></p>
        <p><?php echo e($settings['delivery_text_' . $lang] ?? t('delivery_text_fallback')); ?></p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
