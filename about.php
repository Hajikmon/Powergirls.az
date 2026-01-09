<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';

$lang = current_lang();
$settings = get_settings();
$page_title = $settings['about_title_' . $lang] ?? t('about_title');
$page_desc = $settings['default_meta_description_' . $lang] ?? '';

$about_title = $settings['about_title_' . $lang] ?? t('about_title');
$about_subtitle = $settings['about_subtitle_' . $lang] ?? t('about_subtitle');
$about_text_1 = $settings['about_text_1_' . $lang] ?? t('about_text_1');
$about_text_2 = $settings['about_text_2_' . $lang] ?? t('about_text_2');

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h1><?php echo e($about_title); ?></h1>
      <p class="lead"><?php echo e($about_subtitle); ?></p>
    </div>
    <div class="content">
      <p><?php echo e($about_text_1); ?></p>
      <p><?php echo e($about_text_2); ?></p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
