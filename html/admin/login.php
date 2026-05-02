<?php
session_start();
require_once __DIR__ . '/../config.php';

if (is_admin()) {
    redirect('/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_time'] = time();
        redirect('/admin/');
    } else {
        $error = 'Hatalı şifre. Tekrar deneyin.';
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
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
