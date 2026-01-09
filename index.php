<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$settings = get_settings();
$lang = current_lang();
$categories = get_categories();

$page_title = $settings['site_title_' . $lang] ?? 'powergirls.az';
$page_desc = $settings['default_meta_description_' . $lang] ?? '';

$hero_tagline = $settings['hero_tagline_' . $lang] ?? t('hero_tagline');
$hero_title = $settings['hero_title_' . $lang] ?? t('hero_title');
$hero_subtitle = $settings['hero_subtitle_' . $lang] ?? t('hero_subtitle');
$hero_location = $settings['hero_location_' . $lang] ?? '';

$gift_title = $settings['home_gift_title_' . $lang] ?? t('section_gift');
$gift_text = $settings['home_gift_text_' . $lang] ?? t('gift_text');

$bestsellers = get_products(['is_bestseller' => 1, 'limit' => 6]);
$new_products = get_products(['is_new' => 1, 'limit' => 6]);
$enable_favs = true;

include __DIR__ . '/includes/header.php';
?>

<section class="section categories">
  <div class="container">
    <div class="section-head">
      <h2>Kateqoriyalar</h2>
      <a class="link" href="shop.php">Mağaza</a>
    </div>
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <?php $cat_name = $cat['name_' . $lang] ?? $cat['name_az'] ?? $cat['slug']; ?>
        <a class="category-card" href="shop.php?category=<?php echo e($cat['slug']); ?>">
          <?php echo e($cat_name); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="hero section">
  <div class="container grid-2">
    <div class="hero-text">
      <p class="eyebrow"><?php echo e($hero_tagline); ?></p>
      <h1><?php echo e($hero_title); ?></h1>
      <p class="lead"><?php echo e($hero_subtitle); ?></p>
      <div class="hero-messages">
        <?php if ($hero_location): ?>
          <span><?php echo e($hero_location); ?></span>
        <?php endif; ?>
        <span><?php echo e($settings['topbar_text_' . $lang] ?? t('message_delivery_post')); ?></span>
        <span><?php echo e($settings['delivery_text_' . $lang] ?? t('message_order_channels')); ?></span>
      </div>
    </div>
    <div class="hero-visual reveal">
      <div class="hero-card">
        <div class="hero-image"></div>
        <div class="hero-caption">
          <?php if (($settings['campaign_status'] ?? 'OFF') === 'ON'): ?>
            <a href="campaigns.php"><?php echo e(t('message_discount')); ?></a>
          <?php endif; ?>
          <?php if (($settings['giveaway_status'] ?? 'OFF') === 'ON'): ?>
            <a href="giveaway.php"><?php echo e(t('message_giveaway')); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?php echo e(t('section_bestseller')); ?></h2>
      <a class="link" href="shop.php?badge=bestseller"><?php echo e(t('cta_shop')); ?></a>
    </div>
    <div class="grid-3 products">
      <?php foreach ($bestsellers as $p): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <div class="section-head">
      <h2><?php echo e(t('section_new')); ?></h2>
      <a class="link" href="shop.php?badge=new"><?php echo e(t('cta_shop')); ?></a>
    </div>
    <div class="grid-3 products">
      <?php foreach ($new_products as $p): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container grid-2">
    <div class="gift-ready reveal">
      <h3><?php echo e($gift_title); ?></h3>
      <p><?php echo e($gift_text); ?></p>
      <a class="btn ghost" href="shop.php"><?php echo e(t('cta_shop')); ?></a>
    </div>
    <div class="spotlight reveal">
      <h3><?php echo e($settings['campaign_title_' . $lang] ?? t('section_campaign')); ?></h3>
      <p><?php echo e($settings['campaign_text_' . $lang] ?? t('campaign_text_fallback')); ?></p>
      <a class="btn primary" href="campaigns.php"><?php echo e(t('section_campaign')); ?></a>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="container grid-2">
    <div class="giveaway teaser reveal">
      <h3><?php echo e(t('section_giveaway')); ?></h3>
      <p><?php echo e($settings['giveaway_terms_' . $lang] ?? t('giveaway_text_fallback')); ?></p>
      <a class="btn ghost" href="giveaway.php"><?php echo e(t('section_giveaway')); ?></a>
    </div>
    <div class="instagram reveal">
      <div class="section-head">
        <h3><?php echo e(t('section_instagram')); ?></h3>
        <a class="btn primary" href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank" data-track="instagram_click">
          <?php echo e(t('follow_instagram')); ?>
        </a>
      </div>
      <div class="insta-grid">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <div class="insta-tile"></div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
