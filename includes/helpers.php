<?php
function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_lang(): string {
  return $_SESSION['lang'] ?? 'az';
}

function product_title(array $p): string {
  $key = 'title_' . current_lang();
  return $p[$key] ?? '';
}

function product_desc(array $p, string $type): string {
  $key = ($type === 'short' ? 'short_desc_' : 'full_desc_') . current_lang();
  return $p[$key] ?? '';
}

function format_price(float $price): string {
  return number_format($price, 2, '.', ' ') . ' AZN';
}

function price_html(array $p): string {
  $price = (float)$p['price'];
  $discount = (int)$p['discount_percent'];
  if ($discount > 0) {
    $new = $price * (1 - $discount / 100);
    return '<span>' . e(format_price($new)) . '</span><span class="old">' . e(format_price($price)) . '</span>';
  }
  return '<span>' . e(format_price($price)) . '</span>';
}

function get_settings(): array {
  $stmt = db()->query('SELECT * FROM settings WHERE id = 1');
  return $stmt->fetch() ?: [];
}

function get_categories(): array {
  $stmt = db()->query('SELECT * FROM categories ORDER BY sort_order ASC, name_az ASC');
  return $stmt->fetchAll();
}

function get_products(array $opts = []): array {
  $where = ['status = "active"'];
  $params = [];

  if (!empty($opts['q'])) {
    $where[] = '(title_az LIKE :q OR title_ru LIKE :q OR title_en LIKE :q OR short_desc_az LIKE :q OR short_desc_ru LIKE :q OR short_desc_en LIKE :q OR full_desc_az LIKE :q OR full_desc_ru LIKE :q OR full_desc_en LIKE :q)';
    $params[':q'] = '%' . $opts['q'] . '%';
  }

  if (!empty($opts['category']) && $opts['category'] !== 'all') {
    $where[] = 'category = :category';
    $params[':category'] = $opts['category'];
  }
  if (!empty($opts['badge'])) {
    if ($opts['badge'] === 'bestseller') $where[] = 'is_bestseller = 1';
    if ($opts['badge'] === 'new') $where[] = 'is_new = 1';
    if ($opts['badge'] === 'discount') $where[] = 'discount_percent > 0';
  }
  if (!empty($opts['discounted'])) $where[] = 'discount_percent > 0';

  if (isset($opts['min'])) {
    $where[] = 'price >= :min';
    $params[':min'] = (float)$opts['min'];
  }
  if (isset($opts['max'])) {
    $where[] = 'price <= :max';
    $params[':max'] = (float)$opts['max'];
  }

  $order = 'created_at DESC';
  if (!empty($opts['sort'])) {
    if ($opts['sort'] === 'price_low') $order = 'price ASC';
    if ($opts['sort'] === 'price_high') $order = 'price DESC';
    if ($opts['sort'] === 'popular') $order = 'is_bestseller DESC, created_at DESC';
  }

  $limit = (int)($opts['limit'] ?? 12);
  $offset = 0;
  if (!empty($opts['page'])) {
    $offset = ((int)$opts['page'] - 1) * $limit;
  }

  $sql = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll();
}

function count_products(array $opts = []): int {
  $where = ['status = "active"'];
  $params = [];

  if (!empty($opts['q'])) {
    $where[] = '(title_az LIKE :q OR title_ru LIKE :q OR title_en LIKE :q OR short_desc_az LIKE :q OR short_desc_ru LIKE :q OR short_desc_en LIKE :q OR full_desc_az LIKE :q OR full_desc_ru LIKE :q OR full_desc_en LIKE :q)';
    $params[':q'] = '%' . $opts['q'] . '%';
  }

  if (!empty($opts['category']) && $opts['category'] !== 'all') {
    $where[] = 'category = :category';
    $params[':category'] = $opts['category'];
  }
  if (!empty($opts['badge'])) {
    if ($opts['badge'] === 'bestseller') $where[] = 'is_bestseller = 1';
    if ($opts['badge'] === 'new') $where[] = 'is_new = 1';
    if ($opts['badge'] === 'discount') $where[] = 'discount_percent > 0';
  }

  if (isset($opts['min'])) {
    $where[] = 'price >= :min';
    $params[':min'] = (float)$opts['min'];
  }
  if (isset($opts['max'])) {
    $where[] = 'price <= :max';
    $params[':max'] = (float)$opts['max'];
  }

  $sql = 'SELECT COUNT(*) as c FROM products WHERE ' . implode(' AND ', $where);
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return (int)($stmt->fetch()['c'] ?? 0);
}

function get_product(int $id): ?array {
  $stmt = db()->prepare('SELECT * FROM products WHERE id = :id AND status = "active"');
  $stmt->execute([':id' => $id]);
  $p = $stmt->fetch();
  return $p ?: null;
}

function get_product_images(int $product_id): array {
  $stmt = db()->prepare('SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC');
  $stmt->execute([':id' => $product_id]);
  return $stmt->fetchAll();
}

function get_related_products(array $product): array {
  $stmt = db()->prepare('SELECT * FROM products WHERE category = :cat AND id != :id AND status = "active" LIMIT 6');
  $stmt->execute([':cat' => $product['category'], ':id' => $product['id']]);
  return $stmt->fetchAll();
}

function paginate(int $total, int $per_page, int $page): array {
  $pages = max(1, (int)ceil($total / $per_page));
  $prev = $page > 1 ? update_query(['page' => $page - 1]) : null;
  $next = $page < $pages ? update_query(['page' => $page + 1]) : null;
  return [
    'prev' => $prev,
    'next' => $next,
    'label' => $page . ' / ' . $pages
  ];
}

function update_query(array $params): string {
  $query = array_merge($_GET, $params);
  return '?' . http_build_query($query);
}

function clean_phone(string $phone): string {
  return preg_replace('/\D+/', '', $phone);
}

function build_wa_link(string $phone, string $text): string {
  $num = clean_phone($phone);
  return 'https://wa.me/' . $num . '?text=' . urlencode($text);
}

function build_order_text(array $data, string $lang): string {
  return "powergirls.az Sifarisi
"
    . "Mehsul: " . $data['product'] . "
"
    . "Miqdar: " . $data['quantity'] . "
"
    . "Ad Soyad: " . $data['name'] . "
"
    . "Telefon: " . $data['phone'] . "
"
    . "Seher: " . $data['city'] . "
"
    . "Unvan: " . ($data['address'] ?: '-') . "
"
    . "Catdirilma: " . $data['delivery'] . "
"
    . "Qeyd: " . ($data['note'] ?: '-') . "
"
    . "Dil: " . strtoupper($lang);
}
function save_order(array $data): void {
  $stmt = db()->prepare('INSERT INTO orders (created_at, status, customer_name, customer_phone, city, address, delivery_type, note, quantity, product_id, lang, order_text_snapshot)
    VALUES (NOW(), "new", :name, :phone, :city, :address, :delivery, :note, :qty, :pid, :lang, :text)');
  $stmt->execute([
    ':name' => $data['customer_name'],
    ':phone' => $data['customer_phone'],
    ':city' => $data['city'],
    ':address' => $data['address'],
    ':delivery' => $data['delivery_type'],
    ':note' => $data['note'],
    ':qty' => $data['quantity'],
    ':pid' => $data['product_id'],
    ':lang' => $data['lang'],
    ':text' => $data['order_text_snapshot']
  ]);
}

function upload_product_image(array $file): ?string {
  if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return null;
  if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) return null;
  if ($file['size'] > 2 * 1024 * 1024) return null;

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/svg+xml' => 'svg'
  ];
  if (!isset($allowed[$mime])) return null;

  $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
  $dest_dir = __DIR__ . '/../uploads/products';
  if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0755, true);
  }
  $dest = $dest_dir . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
  return 'uploads/products/' . $name;
}

function delete_product_image_file(string $path): void {
  $path = str_replace(['..', '\\'], ['', '/'], $path);
  if (strpos($path, 'uploads/products/') !== 0) return;
  $full = __DIR__ . '/../' . $path;
  if (is_file($full)) {
    unlink($full);
  }
}

function upload_image(array $file, string $folder): ?string {
  if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return null;
  if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) return null;
  if ($file['size'] > 2 * 1024 * 1024) return null;

  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
  ];

  $mime = '';
  if (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
  } else {
    $info = getimagesize($file['tmp_name']);
    $mime = $info['mime'] ?? '';
  }
  if (!isset($allowed[$mime])) return null;

  $folder = preg_replace('/[^a-z0-9_-]+/i', '', $folder) ?: 'misc';
  $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
  $dest_dir = __DIR__ . '/../uploads/' . $folder;
  if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0755, true);
  }
  $dest = $dest_dir . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
  return 'uploads/' . $folder . '/' . $name;
}

function delete_uploaded_file(string $path, string $folder): void {
  $path = str_replace(['..', '\\'], ['', '/'], $path);
  $folder = preg_replace('/[^a-z0-9_-]+/i', '', $folder) ?: 'misc';
  $prefix = 'uploads/' . $folder . '/';
  if (strpos($path, $prefix) !== 0) return;
  $full = __DIR__ . '/../' . $path;
  if (is_file($full)) {
    unlink($full);
  }
}
