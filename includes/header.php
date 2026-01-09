<?php
$lang = current_lang();
$settings = $settings ?? get_settings();
$title = $page_title ?? ($settings['site_title_' . $lang] ?? 'powergirls.az');
$desc = $page_desc ?? ($settings['default_meta_description_' . $lang] ?? '');
$og_image = $settings['og_image_path'] ?? 'assets/images/product-01.svg';
$canonical = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$enable_favs = $enable_favs ?? false;
?>
<!doctype html>
<html lang="<?php echo e($lang); ?>">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo e($title); ?></title>
  <meta name="description" content="<?php echo e($desc); ?>" />
  <link rel="canonical" href="<?php echo e($canonical); ?>" />
  <meta property="og:title" content="<?php echo e($title); ?>" />
  <meta property="og:description" content="<?php echo e($desc); ?>" />
  <meta property="og:image" content="<?php echo e($og_image); ?>" />
  <meta property="og:type" content="website" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <script defer src="assets/js/app.js"></script>
</head>
<body class="<?php echo e($body_class ?? ''); ?>">
  <div class="topbar">
    <div class="container">
      <span><?php echo e($settings['topbar_text_' . $lang] ?? t('topbar_delivery')); ?></span>
      <div class="topbar-actions">
        <span><?php echo e($settings['delivery_text_' . $lang] ?? t('topbar_order')); ?></span>
      </div>
    </div>
  </div>

  <header class="header">
    <div class="container">
      <a class="logo" href="index.php">powergirls.az</a>
      <nav class="nav">
        <a href="index.php"><?php echo e(t('nav_home')); ?></a>
        <a href="shop.php"><?php echo e(t('nav_shop')); ?></a>
        <a href="campaigns.php"><?php echo e(t('nav_campaigns')); ?></a>
        <a href="giveaway.php"><?php echo e(t('nav_giveaway')); ?></a>
        <a href="about.php"><?php echo e(t('nav_about')); ?></a>
        <a href="contact.php"><?php echo e(t('nav_contact')); ?></a>
      </nav>
      <button class="mobile-toggle" type="button" aria-label="Sevdiklerim" aria-expanded="false" aria-controls="mobile-drawer">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <?php if ($enable_favs): ?>
        <a class="fav-open" href="favorites.php" aria-label="Sevdiklerim">
          <span class="fav-open-icon">&#9829;</span>
          <span class="fav-count" data-fav-count>0</span>
        </a>
      <?php endif; ?>
      <div class="lang-switch" aria-label="Sevdiklerim">
        <a href="?lang=az" class="<?php echo $lang === 'az' ? 'active' : ''; ?>">AZ</a>
        <a href="?lang=ru" class="<?php echo $lang === 'ru' ? 'active' : ''; ?>">RU</a>
        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
      </div>
      <a class="btn primary" href="shop.php"><?php echo e(t('cta_shop')); ?></a>
    </div>
  </header>

  <div class="mobile-overlay" data-mobile-close></div>
  <aside class="mobile-drawer" id="mobile-drawer" aria-hidden="true">
    <div class="mobile-drawer-head">
      <span class="logo">powergirls.az</span>
      <button class="mobile-close" type="button" data-mobile-close aria-label="Sevdiklerim">&times;</button>
    </div>
    <nav class="mobile-nav">
      <a href="index.php"><?php echo e(t('nav_home')); ?></a>
      <a href="shop.php"><?php echo e(t('nav_shop')); ?></a>
      <a href="campaigns.php"><?php echo e(t('nav_campaigns')); ?></a>
      <a href="giveaway.php"><?php echo e(t('nav_giveaway')); ?></a>
      <a href="about.php"><?php echo e(t('nav_about')); ?></a>
      <a href="contact.php"><?php echo e(t('nav_contact')); ?></a>
    </nav>
    <div class="mobile-lang">
      <a href="?lang=az" class="<?php echo $lang === 'az' ? 'active' : ''; ?>">AZ</a>
      <a href="?lang=ru" class="<?php echo $lang === 'ru' ? 'active' : ''; ?>">RU</a>
      <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
    </div>
    <a class="btn primary" href="shop.php"><?php echo e(t('cta_shop')); ?></a>
  </aside>


