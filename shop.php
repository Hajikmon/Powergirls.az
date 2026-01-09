<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$lang = current_lang();
$settings = get_settings();
$body_class = 'shop-page';

$page_title = t('shop_title');
$page_desc = $settings['default_meta_description_' . $lang] ?? '';

$search_labels = [
  'az' => 'Axtaris',
  'ru' => 'Poisk',
  'en' => 'Search'
];
$search_placeholders = [
  'az' => 'Mehsul axtar...',
  'ru' => 'Iskat tovar...',
  'en' => 'Search products...'
];

$category = $_GET['category'] ?? 'all';
$badge = $_GET['badge'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$min = isset($_GET['min']) ? (float)$_GET['min'] : 0;
$max = isset($_GET['max']) ? (float)$_GET['max'] : 1000;
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;

$products = get_products([
  'q' => $q,
  'category' => $category,
  'badge' => $badge,
  'sort' => $sort,
  'min' => $min,
  'max' => $max,
  'page' => $page,
  'limit' => $per_page
]);
$total = count_products([
  'q' => $q,
  'category' => $category,
  'badge' => $badge,
  'min' => $min,
  'max' => $max
]);
$pagination = paginate($total, $per_page, $page);
$categories = get_categories();
$enable_favs = true;

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h1><?php echo e(t('shop_title')); ?></h1>
      <p class="lead"><?php echo e(t('shop_subtitle')); ?></p>
    </div>

    <form class="filters" method="get">
      <div class="filter-group">
        <label><?php echo e($search_labels[$lang] ?? 'Search'); ?></label>
        <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="<?php echo e($search_placeholders[$lang] ?? 'Search products...'); ?>" />
      </div>
      <div class="filter-group">
        <label><?php echo e(t('filters_category')); ?></label>
        <select name="category">
          <option value="all"<?php echo $category === 'all' ? ' selected' : ''; ?>><?php echo e(t('category_all')); ?></option>
          <?php foreach ($categories as $cat): ?>
            <?php $cat_name = $cat['name_' . $lang] ?? $cat['name_az'] ?? $cat['slug']; ?>
            <option value="<?php echo e($cat['slug']); ?>"<?php echo $category === $cat['slug'] ? ' selected' : ''; ?>>
              <?php echo e($cat_name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-group">
        <label><?php echo e(t('filters_badges')); ?></label>
        <select name="badge">
          <option value=""><?php echo e(t('badge_all')); ?></option>
          <option value="bestseller"<?php echo $badge === 'bestseller' ? ' selected' : ''; ?>><?php echo e(t('badge_bestseller')); ?></option>
          <option value="new"<?php echo $badge === 'new' ? ' selected' : ''; ?>><?php echo e(t('badge_new')); ?></option>
          <option value="discount"<?php echo $badge === 'discount' ? ' selected' : ''; ?>><?php echo e(t('badge_discount')); ?></option>
        </select>
      </div>
      <div class="filter-group">
        <label><?php echo e(t('filters_price')); ?></label>
        <div class="range">
          <input type="number" name="min" value="<?php echo e((string)$min); ?>" min="0" max="9999" />
          <span>-</span>
          <input type="number" name="max" value="<?php echo e((string)$max); ?>" min="0" max="9999" />
        </div>
      </div>
      <div class="filter-group">
        <label><?php echo e(t('filters_sort')); ?></label>
        <select name="sort">
          <option value="newest"<?php echo $sort === 'newest' ? ' selected' : ''; ?>><?php echo e(t('sort_newest')); ?></option>
          <option value="price_low"<?php echo $sort === 'price_low' ? ' selected' : ''; ?>><?php echo e(t('sort_price_low')); ?></option>
          <option value="price_high"<?php echo $sort === 'price_high' ? ' selected' : ''; ?>><?php echo e(t('sort_price_high')); ?></option>
          <option value="popular"<?php echo $sort === 'popular' ? ' selected' : ''; ?>><?php echo e(t('sort_popular')); ?></option>
        </select>
      </div>
      <div class="filter-actions">
        <button class="btn primary" type="submit"><?php echo e(t('filters_apply')); ?></button>
      </div>
    </form>

    <div class="grid-3 products">
      <?php foreach ($products as $p): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div class="pagination">
      <?php if ($pagination['prev']): ?>
        <a class="btn ghost" href="<?php echo e($pagination['prev']); ?>"><?php echo e(t('pagination_prev')); ?></a>
      <?php endif; ?>
      <span><?php echo e($pagination['label']); ?></span>
      <?php if ($pagination['next']): ?>
        <a class="btn ghost" href="<?php echo e($pagination['next']); ?>"><?php echo e(t('pagination_next')); ?></a>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="mini-bar">
  <a class="mini-link" href="https://wa.me/<?php echo e(clean_phone($settings['whatsapp_number'] ?? '')); ?>" target="_blank" data-track="whatsapp_click">WhatsApp</a>
  <a class="mini-link" href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank" data-track="instagram_click">Instagram</a>
  <a class="mini-link" href="contact.php"><?php echo e(t('order_form_cta')); ?></a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
