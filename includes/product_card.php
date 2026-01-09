<?php
$img = get_product_images((int)$p['id']);
$thumb = $img[0]['file_path'] ?? 'assets/images/product-01.svg';
$price_text = trim(strip_tags(price_html($p)));
$base_price = (float)$p['price'];
$discount = (int)$p['discount_percent'];
$current_price = $discount > 0 ? $base_price * (1 - $discount / 100) : $base_price;
$old_price = $discount > 0 ? $base_price : 0;
$enable_favs = $enable_favs ?? false;
?>
<div class="card product-card reveal">
  <?php if ($enable_favs): ?>
    <button
      class="fav-toggle"
      type="button"
      aria-label="Sevdiklerim elave et"
      data-fav
      data-id="<?php echo e((string)$p['id']); ?>"
      data-title="<?php echo e(product_title($p)); ?>"
      data-image="<?php echo e($thumb); ?>"
      data-price="<?php echo e($price_text); ?>"
      data-price-current="<?php echo e(format_price($current_price)); ?>"
      data-price-old="<?php echo $old_price > 0 ? e(format_price($old_price)) : ''; ?>"
    >
      <span class="fav-icon">&#9825;</span>
    </button>
  <?php endif; ?>
  <a href="product.php?id=<?php echo e((string)$p['id']); ?>">
    <img src="<?php echo e($thumb); ?>" alt="<?php echo e(product_title($p)); ?>" loading="lazy" />
  </a>
  <div class="badge-slot">
    <?php if ((int)$p['discount_percent'] > 0): ?>
      <span class="badge"><?php echo e(t('badge_discount')); ?></span>
    <?php elseif ((int)$p['is_bestseller'] === 1): ?>
      <span class="badge"><?php echo e(t('badge_bestseller')); ?></span>
    <?php elseif ((int)$p['is_new'] === 1): ?>
      <span class="badge"><?php echo e(t('badge_new')); ?></span>
    <?php endif; ?>
  </div>
  <a href="product.php?id=<?php echo e((string)$p['id']); ?>">
    <h3><?php echo e(product_title($p)); ?></h3>
  </a>
  <div class="price"><?php echo price_html($p); ?></div>
</div>
