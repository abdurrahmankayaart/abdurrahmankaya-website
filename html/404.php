<?php
require_once __DIR__ . '/config.php';
http_response_code(404);
$site_name = get_setting('site_name', 'Abdurrahman Kaya');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sayfa Bulunamadı — <?= e($site_name) ?></title>
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <?= ga_snippet() ?>
</head>
<body>
<header id="header">
  <div class="container">
    <nav class="nav-inner">
      <a href="/" class="nav-logo">AK<span>.</span></a>
      <ul class="nav-links">
        <li><a href="/">Anasayfa</a></li>
        <li><a href="/blog.php">Blog</a></li>
      </ul>
    </nav>
  </div>
</header>
<main>
  <div style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:4rem 1rem">
    <div>
      <div style="font-size:5rem;margin-bottom:1rem">🔍</div>
      <h1 style="font-size:3rem;color:var(--accent);margin-bottom:.5rem">404</h1>
      <h2 style="margin-bottom:1rem">Sayfa Bulunamadı</h2>
      <p style="color:var(--dim);margin-bottom:2rem">Aradığınız sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <a href="/" class="btn btn-primary">Ana Sayfaya Dön</a>
        <a href="/blog.php" class="btn btn-outline">Blog'a Git</a>
      </div>
    </div>
  </div>
</main>
<footer>
  <div class="container">
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e($site_name) ?>. Tüm hakları saklıdır.</p>
    </div>
  </div>
</footer>
<script src="/js/main.js"></script>
</body>
</html>
