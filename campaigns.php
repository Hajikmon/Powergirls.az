<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';

$lang = current_lang();
$settings = get_settings();

$page_title = t('campaigns_title');
$page_desc = $settings['campaign_text_' . $lang] ?? '';

$discounted = get_products(['discounted' => 1, 'limit' => 24]);
$enable_favs = true;

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="campaign-hero grid-2">
      <div>
        <div class="section-head">
          <h1><?php echo e($settings['campaign_title_' . $lang] ?? t('campaigns_title')); ?></h1>
          <p class="lead"><?php echo e($settings['campaign_text_' . $lang] ?? t('campaign_text_fallback')); ?></p>
        </div>
        <?php if (($settings['campaign_status'] ?? 'OFF') === 'ON'): ?>
          <div class="countdown" data-countdown="<?php echo e($settings['campaign_end_datetime'] ?? ''); ?>">
            <span class="countdown-label"><?php echo e(t('campaign_ends_in')); ?></span>
            <div class="timer">
              <span data-days>00</span>d <span data-hours>00</span>h <span data-mins>00</span>m <span data-secs>00</span>s
            </div>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!empty($settings['campaign_image_path'])): ?>
        <div class="card media-card">
          <img src="<?php echo e($settings['campaign_image_path']); ?>" alt="Campaign" class="media-image" />
        </div>
      <?php endif; ?>
    </div>

    <?php if (($settings['campaign_status'] ?? 'OFF') === 'ON'): ?>
      <div class="grid-3 products">
        <?php foreach ($discounted as $p): ?>
          <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert"><?php echo e(t('campaign_inactive')); ?></div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
