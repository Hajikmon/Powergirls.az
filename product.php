<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$lang = current_lang();
$settings = get_settings();

$product_id = (int)($_GET['id'] ?? 0);
$product = get_product($product_id);

if (!$product) {
  http_response_code(404);
  echo 'Product not found';
  exit;
}

$errors = [];
$order_success = false;
$order_text = '';
$wa_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_submit'])) {
  if (!verify_csrf()) {
    $errors[] = t('order_validation_error');
  }

  $name = trim($_POST['customer_name'] ?? '');
  $phone = trim($_POST['customer_phone'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $delivery_type = trim($_POST['delivery_type'] ?? '');
  $note = trim($_POST['note'] ?? '');
  $qty = (int)($_POST['quantity'] ?? 1);

  if ($name === '' || $phone === '' || $city === '' || $delivery_type === '' || $qty < 1 || $qty > 10) {
    $errors[] = t('order_validation_error');
  }

  if (!$errors) {
    $product_title = product_title($product);
    $order_text = build_order_text([
      'product' => $product_title,
      'quantity' => $qty,
      'name' => $name,
      'phone' => $phone,
      'city' => $city,
      'address' => $address,
      'delivery' => $delivery_type,
      'note' => $note
    ], $lang);

    save_order([
      'customer_name' => $name,
      'customer_phone' => $phone,
      'city' => $city,
      'address' => $address,
      'delivery_type' => $delivery_type,
      'note' => $note,
      'quantity' => $qty,
      'product_id' => $product_id,
      'lang' => $lang,
      'order_text_snapshot' => $order_text
    ]);

    $wa_link = build_wa_link($settings['whatsapp_number'] ?? '', $order_text);
    $order_success = true;
  }
}

$page_title = product_title($product);
$page_desc = product_desc($product, 'short');
$images = get_product_images($product_id);
$product_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
$schema_images = [];
if ($images) {
  foreach ($images as $img) {
    $schema_images[] = $base_url . ltrim($img['file_path'], '/');
  }
} else {
  $schema_images[] = $base_url . 'assets/images/product-01.svg';
}
$final_price = (float)$product['price'];
if ((int)$product['discount_percent'] > 0) {
  $final_price = $final_price * (1 - ((int)$product['discount_percent'] / 100));
}
$schema = [
  '@context' => 'https://schema.org',
  '@type' => 'Product',
  'name' => product_title($product),
  'description' => product_desc($product, 'full'),
  'image' => $schema_images,
  'offers' => [
    '@type' => 'Offer',
    'priceCurrency' => 'AZN',
    'price' => number_format($final_price, 2, '.', ''),
    'availability' => 'https://schema.org/InStock',
    'url' => $product_url
  ]
];
$schema_json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$enable_favs = true;

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container grid-2">
    <div class="gallery">
      <div class="main-image" data-gallery>
        <img src="<?php echo e($images[0]['file_path'] ?? 'assets/images/product-01.svg'); ?>" data-lightbox="<?php echo e($images[0]['file_path'] ?? 'assets/images/product-01.svg'); ?>" alt="<?php echo e(product_title($product)); ?>" loading="lazy" />
      </div>
      <div class="thumbs" data-gallery-thumbs>
        <?php foreach ($images as $i => $img): ?>
          <button class="thumb<?php echo $i === 0 ? ' active' : ''; ?>" data-gallery-thumb data-src="<?php echo e($img['file_path']); ?>">
            <img src="<?php echo e($img['file_path']); ?>" alt="<?php echo e(product_title($product)); ?>" loading="lazy" />
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="product-info">
      <h1><?php echo e(product_title($product)); ?></h1>
      <div class="price">
        <?php echo price_html($product); ?>
      </div>
      <p class="lead"><?php echo e(product_desc($product, 'short')); ?></p>

      <div class="product-actions">
        <a class="btn primary" href="<?php echo e(build_wa_link($settings['whatsapp_number'] ?? '', product_title($product))); ?>" target="_blank" data-track="whatsapp_click">
          <?php echo e(t('product_order_whatsapp')); ?>
        </a>
        <button class="btn ghost" type="button" data-ig-order data-product="<?php echo e(product_title($product)); ?>">
          <?php echo e(t('product_order_instagram')); ?>
        </button>
        <a class="btn ghost" href="#order-form"><?php echo e(t('product_order_form')); ?></a>
      </div>

      <div class="tabs">
        <button class="tab active" data-tab="desc"><?php echo e(t('tabs_description')); ?></button>
        <button class="tab" data-tab="delivery"><?php echo e(t('tabs_delivery')); ?></button>
        <button class="tab" data-tab="returns"><?php echo e(t('tabs_returns')); ?></button>
      </div>
      <div class="tab-content active" id="tab-desc">
        <p><?php echo nl2br(e(product_desc($product, 'full'))); ?></p>
      </div>
      <div class="tab-content" id="tab-delivery">
        <p><?php echo e($settings['delivery_text_' . $lang] ?? t('delivery_text_fallback')); ?></p>
      </div>
      <div class="tab-content" id="tab-returns">
        <p><?php echo e(t('returns_text')); ?></p>
      </div>
    </div>
  </div>
</section>

<section class="section alt" id="order-form">
  <div class="container">
    <div class="section-head">
      <h2><?php echo e(t('order_form_title')); ?></h2>
      <p class="lead"><?php echo e(t('order_form_note')); ?></p>
    </div>

    <?php if ($errors): ?>
      <div class="alert"><?php echo e(implode(' ', $errors)); ?></div>
    <?php endif; ?>

    <?php if ($order_success): ?>
      <div class="success">
        <h3><?php echo e(t('order_success_title')); ?></h3>
        <p><?php echo e(t('order_success_text')); ?></p>
        <div class="order-actions">
          <a class="btn primary" href="<?php echo e($wa_link); ?>" target="_blank" data-track="whatsapp_click"><?php echo e(t('order_open_whatsapp')); ?></a>
          <button class="btn ghost" data-copy="<?php echo e($order_text); ?>" data-track="copy_order_text"><?php echo e(t('order_copy')); ?></button>
        </div>
      </div>
    <?php endif; ?>

    <form class="order-form" method="post" data-track-form="order_submit">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="order_submit" value="1" />
      <div class="grid-2">
        <label>
          <?php echo e(t('order_name')); ?>
          <input type="text" name="customer_name" required />
        </label>
        <label>
          <?php echo e(t('order_phone')); ?>
          <input type="tel" name="customer_phone" required />
        </label>
      </div>
      <div class="grid-2">
        <label>
          <?php echo e(t('order_city')); ?>
          <select name="city" required>
            <option value="Baku"><?php echo e(t('city_baku')); ?></option>
            <option value="Rayon"><?php echo e(t('city_region')); ?></option>
          </select>
        </label>
        <label>
          <?php echo e(t('order_delivery')); ?>
          <select name="delivery_type" required>
            <option value="courier"><?php echo e(t('delivery_courier')); ?></option>
            <option value="post"><?php echo e(t('delivery_post')); ?></option>
          </select>
        </label>
      </div>
      <label>
        <?php echo e(t('order_address')); ?>
        <input type="text" name="address" />
      </label>
      <label>
        <?php echo e(t('order_note')); ?>
        <textarea name="note" rows="3"></textarea>
      </label>
      <label>
        <?php echo e(t('product_quantity')); ?>
        <input type="number" name="quantity" min="1" max="10" value="1" required />
      </label>
      <button class="btn primary" type="submit"><?php echo e(t('order_submit')); ?></button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?php echo e(t('related_products')); ?></h2>
    </div>
    <div class="grid-3 products">
      <?php foreach (get_related_products($product) as $p): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="lightbox" id="lightbox" aria-hidden="true">
  <button class="lightbox-close" type="button">x</button>
  <button class="lightbox-nav prev" type="button" aria-label="Previous">&larr;</button>
  <img src="" alt="Preview" />
  <button class="lightbox-nav next" type="button" aria-label="Next">&rarr;</button>
</div>

  <script type="application/ld+json"><?php echo $schema_json; ?></script>

  <?php include __DIR__ . '/includes/footer.php'; ?>
