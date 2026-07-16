<?php
session_start();
require_once __DIR__ . '/../config.php';

if (is_admin()) {
    redirect('/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    // Rate limiting: 10 dakikada 5 başarısız deneme
    $now     = time();
    $attempts = $_SESSION['login_attempts'] ?? [];
    $attempts = array_filter($attempts, fn($t) => $now - $t < 600);

    if (count($attempts) >= 5) {
        $error = 'Çok fazla başarısız deneme. 10 dakika bekleyin.';
    } else {
        $password = $_POST['password'] ?? '';
        if (hash_equals(ADMIN_PASSWORD, $password)) {
            session_regenerate_id(true);
            $_SESSION['admin']      = true;
            $_SESSION['admin_time'] = $now;
            unset($_SESSION['login_attempts']);
            redirect('/admin/');
        } else {
            $attempts[] = $now;
            $_SESSION['login_attempts'] = $attempts;
            $error = 'Hatalı şifre. Tekrar deneyin. (' . count($attempts) . '/5)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Girişi</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#080810">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=2">
</head>
<body class="admin-body">
<div class="admin-login">
  <div class="login-box">
    <div style="font-size:2.5rem;margin-bottom:1rem">🔐</div>
    <h1>Admin Paneli</h1>
    <p class="login-sub">Devam etmek için şifrenizi girin.</p>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" class="form-control <?= $error ? 'error' : '' ?>" placeholder="••••••••" autofocus required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:.5rem">Giriş Yap</button>
    </form>
    <p style="margin-top:1.5rem;font-size:.8rem"><a href="/">← Siteye Dön</a></p>
  </div>
</div>
</body>
</html>
