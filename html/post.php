<?php
require_once __DIR__ . '/config.php';
$cfg  = get_all_settings();
$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? ''));
if (!$slug) { http_response_code(404); die('Sayfa bulunamadı'); }

try {
    $stmt = db()->prepare("SELECT * FROM posts WHERE slug=? AND published=1");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    if (!$post) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

    // Görüntülenme sayısını artır
    db()->prepare("UPDATE posts SET views=views+1 WHERE id=?")->execute([$post['id']]);

    // Benzer yazılar
    $related = db()->prepare("SELECT slug,title,category,cover_image,created_at FROM posts WHERE published=1 AND category=? AND id!=? ORDER BY created_at DESC LIMIT 3");
    $related->execute([$post['category'], $post['id']]);
    $related = $related->fetchAll();
} catch (Throwable) { http_response_code(500); die('Bir hata oluştu'); }

$site_name = $cfg['site_name'] ?? 'Abdurrahman Kaya';
$tags      = array_filter(array_map('trim', explode(',', $post['tags'] ?? '')));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($post['title']) ?> — <?= e($site_name) ?></title>
  <meta name="description" content="<?= e($post['excerpt'] ?? '') ?>">
  <link rel="canonical" href="<?= SITE_URL ?>/blog/<?= e($slug) ?>">
  <meta property="og:type"        content="article">
  <meta property="og:url"         content="<?= SITE_URL ?>/blog/<?= e($slug) ?>">
  <meta property="og:title"       content="<?= e($post['title']) ?>">
  <meta property="og:description" content="<?= e($post['excerpt'] ?? '') ?>">
  <?php if ($post['cover_image']): ?><meta property="og:image" content="<?= SITE_URL . e($post['cover_image']) ?>"><?php endif; ?>
  <meta property="article:published_time" content="<?= e($post['created_at']) ?>">
  <meta property="article:author"         content="<?= e($site_name) ?>">
  <meta name="twitter:card"  content="summary_large_image">
  <meta name="twitter:title" content="<?= e($post['title']) ?>">
  <meta name="twitter:description" content="<?= e($post['excerpt'] ?? '') ?>">
  <?php if ($post['cover_image']): ?><meta name="twitter:image" content="<?= SITE_URL . e($post['cover_image']) ?>"><?php endif; ?>
  <?php
  $ldJson = [
    '@context'      => 'https://schema.org',
    '@type'         => 'BlogPosting',
    'headline'      => $post['title'],
    'url'           => SITE_URL . '/blog/' . $slug,
    'datePublished' => $post['created_at'],
    'dateModified'  => $post['updated_at'],
    'description'   => $post['excerpt'] ?? '',
    'author'        => ['@type' => 'Person', 'name' => $site_name, 'url' => SITE_URL],
    'publisher'     => ['@type' => 'Person', 'name' => $site_name, 'url' => SITE_URL],
  ];
  if ($post['cover_image']) $ldJson['image'] = SITE_URL . $post['cover_image'];
  if ($tags) $ldJson['keywords'] = implode(', ', $tags);
  ?>
  <script type="application/ld+json"><?= json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#080810">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css?v=2">
  <?= ga_snippet() ?>
</head>
<body>

<div class="mobile-menu">
  <button class="mobile-close">✕</button>
  <a href="/">Anasayfa</a>
  <a href="/blog.php">Blog</a>
</div>

<header id="header">
  <div class="container">
    <nav class="nav-inner">
      <a href="/" class="nav-logo">AK<span>.</span></a>
      <ul class="nav-links">
        <li><a href="/">Anasayfa</a></li>
        <li><a href="/blog.php" class="active">Blog</a></li>
      </ul>
      <a href="/#iletisim" class="btn btn-primary btn-sm nav-cta">İletişime Geç</a>
      <button class="hamburger"><span></span><span></span><span></span></button>
    </nav>
  </div>
</header>

<main>
  <div class="post-hero">
    <div class="container" style="max-width:860px">
      <div class="post-meta">
        <a href="/blog.php?category=<?= urlencode($post['category']) ?>" class="badge badge-accent"><?= e($post['category']) ?></a>
        <time style="font-size:.875rem;color:var(--dim)"><?= format_date($post['created_at']) ?></time>
        <span style="font-size:.875rem;color:var(--dim)"><?= read_time($post['content']) ?> dk okuma</span>
        <span style="font-size:.875rem;color:var(--dim)"><?= (int)$post['views'] + 1 ?> görüntülenme</span>
      </div>
      <h1 class="post-title"><?= e($post['title']) ?></h1>
      <?php if ($post['excerpt']): ?>
        <p class="post-excerpt"><?= e($post['excerpt']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="container" style="max-width:860px;padding-bottom:4rem">
    <?php if ($post['cover_image']): ?>
    <div class="post-cover">
      <img src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title']) ?>">
    </div>
    <?php endif; ?>

    <div class="post-content">
      <?= $post['content'] /* İçerik güvenilir kaynak (admin) tarafından girilmiş */ ?>
    </div>

    <?php if ($tags): ?>
    <div class="post-tags">
      <?php foreach ($tags as $tag): ?>
        <a href="/blog.php?tag=<?= urlencode($tag) ?>" class="post-tag">#<?= e($tag) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border)">
      <a href="/blog.php" class="btn btn-ghost">← Blog'a Dön</a>
    </div>

    <?php if ($related): ?>
    <div style="margin-top:4rem">
      <h2 style="margin-bottom:1.5rem;font-size:1.25rem">İlgili Yazılar</h2>
      <div class="blog-grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
        <?php foreach ($related as $r): ?>
        <article class="blog-card">
          <a href="/blog/<?= e($r['slug']) ?>">
            <div class="blog-card-img">
              <?php if ($r['cover_image']): ?>
                <img src="<?= e($r['cover_image']) ?>" alt="<?= e($r['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="blog-card-img-placeholder" style="min-height:120px">📝</div>
              <?php endif; ?>
            </div>
          </a>
          <div class="blog-card-body">
            <span class="badge badge-muted"><?= e($r['category']) ?></span>
            <a href="/blog/<?= e($r['slug']) ?>"><h3 style="margin-top:.5rem"><?= e($r['title']) ?></h3></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</main>

<footer>
  <div class="container">
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e($site_name) ?></p>
      <a href="/blog.php" style="color:var(--dim);font-size:.875rem">← Blog'a Dön</a>
    </div>
  </div>
</footer>

<script src="/js/main.js"></script>
</body>
</html>
