<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/xml; charset=utf-8');
$posts = [];
try {
    $posts = db()->query("SELECT slug, updated_at FROM posts WHERE published=1")->fetchAll();
} catch (Throwable) {}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc><?= SITE_URL ?>/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>
  <url><loc><?= SITE_URL ?>/blog.php</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
  <?php foreach ($posts as $p): ?>
  <url><loc><?= SITE_URL ?>/blog/<?= e($p['slug']) ?></loc><lastmod><?= substr($p['updated_at'],0,10) ?></lastmod><changefreq>monthly</changefreq><priority>0.8</priority></url>
  <?php endforeach; ?>
</urlset>
