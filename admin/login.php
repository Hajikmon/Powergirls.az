<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/i18n.php';

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) {
    $error = 'CSRF error';
  } elseif (login_locked($ip)) {
    $error = 'Too many attempts. Try later.';
  } else {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM admins WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();

    $ok = false;
    if ($admin) {
      if (!empty($admin['password_hash']) && password_verify($password, $admin['password_hash'])) {
        $ok = true;
      } elseif (empty($admin['password_hash']) && $password === 'Saffron@12345') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = db()->prepare('UPDATE admins SET password_hash = :h WHERE id = :id');
        $upd->execute([':h' => $hash, ':id' => $admin['id']]);
        $ok = true;
      }
    }

    record_login_attempt($ip, $ok);
    if ($ok) {
      $_SESSION['admin_id'] = $admin['id'];
      header('Location: index.php');
      exit;
    }
    $error = 'Invalid credentials.';
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin Giriş</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <section class="section">
    <div class="container">
      <h1>Admin Giriş</h1>
      <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
      <form method="post" class="order-form">
        <?php echo csrf_field(); ?>
        <label>Email <input type="email" name="email" required /></label>
        <label>Şifrə <input type="password" name="password" required /></label>
        <button class="btn primary" type="submit">Daxil ol</button>
      </form>
    </div>
  </section>
</body>
</html>
