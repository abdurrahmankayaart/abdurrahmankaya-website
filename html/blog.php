<?php
require_once __DIR__ . '/config.php';
$cfg = get_all_settings();

$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 9;
$category = trim($_GET['category'] ?? '');
$tag      = trim($_GET['tag'] ?? '');

try {
    if ($tag) {
        $where  = 'WHERE published=1 AND FIND_IN_SET(?, REPLACE(tags, ", ", ","))';
        $params = [$tag];
    } elseif ($category) {
        $where  = 'WHERE published=1 AND category=?';
        $params = [$category];
    } else {
        $where  = 'WHERE published=1';
        $params = [];
    }

    $total  = db()->prepare("SELECT COUNT(*) FROM posts $where");
    $total->execute($params);
    $total  = (int)$total->fetchColumn();

    $pages  = max(1, (int)ceil($total / $per_page));
    $offset = ($page - 1) * $per_page;

    $stmt = db()->prepare("SELECT id,slug,title,excerpt,category,cover_image,created_at,content FROM posts $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    $cats = db()->query("SELECT DISTINCT category FROM posts WHERE published=1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) { $posts = []; $cats = []; $pages = 1; }

$site_name = $cfg['site_name'] ?? 'Abdurrahman Kaya';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog — <?= e($site_name) ?></title>
  <meta name="description" content="<?= e($site_name) ?> blog — teknoloji, yazılım ve dijital dönüşüm yazıları.">
  <link rel="canonical" href="<?= SITE_URL ?>/blog.php">
  <meta property="og:type"        content="website">
  <meta property="og:site_name"   content="<?= e($site_name) ?>">
  <meta property="og:title"       content="Blog — <?= e($site_name) ?>">
  <meta property="og:description" content="Teknoloji ve yazılım geliştirme hakkında içgörüler.">
  <meta property="og:url"         content="<?= SITE_URL ?>/blog.php">
  <?php if ($cfg['hero_photo'] ?? ''): ?><meta property="og:image" content="<?= SITE_URL . e($cfg['hero_photo']) ?>"><?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <?= ga_snippet() ?>
</head>
<body>

<div class="mobile-menu">
  <button class="mobile-close">✕</button>
  <a href="/">Anasayfa</a>
  <a href="/#hakkimda">Hakkımda</a>
  <a href="/#hizmetler">Hizmetler</a>
  <a href="/blog.php" class="active">Blog</a>
</div>

<header id="header" role="banner">
  <div class="container">
    <nav class="nav-inner">
      <a href="/" class="nav-logo">AK<span>.</span></a>
      <ul class="nav-links">
        <li><a href="/">Anasayfa</a></li>
        <li><a href="/#hakkimda">Hakkımda</a></li>
        <li><a href="/#hizmetler">Hizmetler</a></li>
        <li><a href="/blog.php" class="active">Blog</a></li>
      </ul>
      <a href="/#iletisim" class="btn btn-primary btn-sm nav-cta">İletişime Geç</a>
      <button class="hamburger"><span></span><span></span><span></span></button>
    </nav>
  </div>
</header>

<main>
  <div class="blog-hero">
    <div class="container">
      <span class="badge badge-accent">Blog</span>
      <h1 style="margin-top:.75rem">Dijital İçgörüler</h1>
      <p>Teknoloji, yazılım geliştirme ve dijital dönüşüm üzerine yazılar.</p>
    </div>
  </div>

  <section class="section-sm">
    <div class="container">
      <?php if (count($cats) > 1): ?>
      <div class="blog-filters">
        <a href="/blog.php" class="filter-btn <?= !$category ? 'active' : '' ?>">Tümü</a>
        <?php foreach ($cats as $cat): ?>
          <a href="/blog.php?category=<?= urlencode($cat) ?>" class="filter-btn <?= $category === $cat ? 'active' : '' ?>"><?= e($cat) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($posts): ?>
      <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
        <article class="blog-card" data-reveal>
          <a href="/blog/<?= e($p['slug']) ?>">
            <div class="blog-card-img">
              <?php if ($p['cover_image']): ?>
                <img src="<?= e($p['cover_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="blog-card-img-placeholder">📝</div>
              <?php endif; ?>
            </div>
          </a>
          <div class="blog-card-body">
            <div class="blog-card-meta">
              <span class="badge badge-accent"><?= e($p['category']) ?></span>
              <time><?= format_date($p['created_at']) ?></time>
            </div>
            <a href="/blog/<?= e($p['slug']) ?>"><h3><?= e($p['title']) ?></h3></a>
            <p><?= e($p['excerpt'] ?? '') ?></p>
            <div class="blog-card-footer">
              <a href="/blog/<?= e($p['slug']) ?>" class="read-more">Okumaya Devam →</a>
              <span class="read-time"><?= read_time($p['content'] ?? '') ?> dk</span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php
            $qs = 'page=' . $i;
            if ($category) $qs .= '&category=' . urlencode($category);
            if ($tag)      $qs .= '&tag=' . urlencode($tag);
          ?>
          <a href="?<?= $qs ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>

      <?php else: ?>
      <p style="text-align:center;color:var(--dim);padding:4rem 0">Henüz yazı yok.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<footer>
  <div class="container">
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e($site_name) ?>. Tüm hakları saklıdır.</p>
      <a href="/" style="color:var(--dim);font-size:.875rem">← Ana Sayfaya Dön</a>
    </div>
  </div>
</footer>

<script src="/js/main.js"></script>
</body>
</html>
