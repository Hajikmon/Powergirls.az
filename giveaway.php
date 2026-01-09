<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';

$lang = current_lang();
$settings = get_settings();

if (($settings['giveaway_status'] ?? 'OFF') !== 'ON') {
  header('Location: index.php');
  exit;
}

$page_title = t('giveaway_title');
$page_desc = $settings['giveaway_terms_' . $lang] ?? '';

$steps_title = $settings['give_steps_title_' . $lang] ?? t('give_steps');
$step_1 = $settings['give_step_1_' . $lang] ?? t('give_step_1');
$step_2 = $settings['give_step_2_' . $lang] ?? t('give_step_2');
$step_3 = $settings['give_step_3_' . $lang] ?? t('give_step_3');
$winner_title = $settings['giveaway_winner_title_' . $lang] ?? t('give_winners');

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="giveaway-hero grid-2">
      <div class="section-head">
        <h1><?php echo e(t('giveaway_title')); ?></h1>
        <p class="lead"><?php echo e($settings['giveaway_terms_' . $lang] ?? t('giveaway_text_fallback')); ?></p>
      </div>
      <?php if (!empty($settings['gift_image_path'])): ?>
        <div class="card gift-card">
          <img src="<?php echo e($settings['gift_image_path']); ?>" alt="Gift" class="media-image" />
          <h3><?php echo e($settings['gift_title_' . $lang] ?? ''); ?></h3>
          <p><?php echo e($settings['gift_description_' . $lang] ?? ''); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <div class="grid-2">
      <div class="card">
        <h3><?php echo e($steps_title); ?></h3>
        <ol class="steps">
          <li><?php echo e($step_1); ?></li>
          <li><?php echo e($step_2); ?></li>
          <li><?php echo e($step_3); ?></li>
        </ol>
      </div>
      <div class="card">
        <h3><?php echo e($winner_title); ?></h3>
        <p><?php echo e($settings['giveaway_winner_text_' . $lang] ?? t('giveaway_winner_fallback')); ?></p>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
