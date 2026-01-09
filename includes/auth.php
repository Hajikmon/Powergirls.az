<?php
function admin_logged_in(): bool {
  return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
  if (!admin_logged_in()) {
    header('Location: login.php');
    exit;
  }
}

function login_locked(string $ip): bool {
  $stmt = db()->prepare('SELECT * FROM login_attempts WHERE ip = :ip');
  $stmt->execute([':ip' => $ip]);
  $row = $stmt->fetch();
  if (!$row) return false;
  if ((int)$row['attempts'] < 5) return false;
  return strtotime($row['last_attempt_at']) > strtotime('-15 minutes');
}

function record_login_attempt(string $ip, bool $success): void {
  $stmt = db()->prepare('SELECT * FROM login_attempts WHERE ip = :ip');
  $stmt->execute([':ip' => $ip]);
  $row = $stmt->fetch();

  if ($success) {
    if ($row) {
      $del = db()->prepare('DELETE FROM login_attempts WHERE ip = :ip');
      $del->execute([':ip' => $ip]);
    }
    return;
  }

  if ($row) {
    $upd = db()->prepare('UPDATE login_attempts SET attempts = attempts + 1, last_attempt_at = NOW() WHERE ip = :ip');
    $upd->execute([':ip' => $ip]);
  } else {
    $ins = db()->prepare('INSERT INTO login_attempts (ip, attempts, last_attempt_at) VALUES (:ip, 1, NOW())');
    $ins->execute([':ip' => $ip]);
  }
}
