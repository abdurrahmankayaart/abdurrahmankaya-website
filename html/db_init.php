<?php
// One-time DB initialization — DELETE THIS FILE AFTER USE
if (!isset($_GET['token']) || $_GET['token'] !== 'AkInit2025Secret') {
    http_response_code(403); exit('Forbidden');
}
require_once __DIR__ . '/config.php';
$pdo = db();
$errors = [];
$ok = [];

$sqls = [
"CREATE TABLE IF NOT EXISTS posts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(255) NOT NULL UNIQUE,
  title       VARCHAR(500) NOT NULL,
  content     LONGTEXT,
  excerpt     TEXT,
  category    VARCHAR(100) DEFAULT 'Genel',
  cover_image VARCHAR(500),
  tags        VARCHAR(500),
  published   TINYINT(1) DEFAULT 1,
  views       INT DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_published (published),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"INSERT IGNORE INTO settings (`key`, `value`) VALUES
  ('site_name',    'Abdurrahman Kaya'),
  ('site_title',   'Yazılım Geliştirici & Teknoloji Danışmanı'),
  ('site_desc',    'Modern teknolojilerle güçlü dijital çözümler üretiyorum.'),
  ('site_email',   'info@abdurrahmankaya.com'),
  ('linkedin',     ''),
  ('github',       ''),
  ('twitter',      ''),
  ('instagram',    ''),
  ('hero_title',   'Dijital Dönüşümde Güvenilir Partneriniz'),
  ('hero_subtitle','Modern teknolojilerle işletmenizi bir üst seviyeye taşıyan, ölçeklenebilir ve yüksek performanslı dijital çözümler üretiyorum.'),
  ('hero_photo',   ''),
  ('about_text',   '5+ yıldır yazılım geliştirme ve teknoloji danışmanlığı alanında çalışıyorum. Modern web teknolojileri, bulut altyapısı ve dijital dönüşüm projelerinde işletmelere değer katıyorum.'),
  ('skills',       'JavaScript,TypeScript,React / Next.js,Node.js,PHP,MySQL,Docker,Linux')",

"INSERT IGNORE INTO posts (slug, title, content, excerpt, category, published) VALUES
(
  'php-mysql-ile-modern-web-gelistirme',
  'PHP ve MySQL ile Modern Web Geliştirme',
  '<h2>Giriş</h2><p>PHP, 2024 yılında hâlâ web geliştirmenin güçlü bir seçeneği olmaya devam ediyor. Bu yazıda modern PHP pratiklerini inceliyoruz.</p><h2>PDO ile Güvenli Sorgular</h2><p>PDO kullanarak SQL injection saldırılarından korunabilir, prepared statement ile güvenli veritabanı işlemleri yapabilirsiniz.</p>',
  'PHP ve MySQL ile modern, güvenli ve ölçeklenebilir web uygulamaları geliştirme rehberi.',
  'Web Geliştirme',
  1
),
(
  'docker-ile-php-deploy',
  'Docker ile PHP Uygulaması Deploy Etme',
  '<h2>Neden Docker?</h2><p>Docker, uygulamanızı her ortamda aynı şekilde çalıştırmanızı sağlar. Geliştirme, test ve üretim ortamları arasındaki farkları ortadan kaldırır.</p><h2>Coolify ile Kolay Deploy</h2><p>Coolify, Docker uygulamalarını kendi sunucunuzda kolayca yönetmenizi sağlayan açık kaynaklı bir platformdur.</p>',
  'Docker ve Coolify kullanarak PHP uygulamanızı kendi sunucunuzda nasıl deploy edeceğinizi öğrenin.',
  'DevOps',
  1
),
(
  'seo-optimizasyonu-temelleri',
  'SEO Optimizasyonu: Temel Teknikler',
  '<h2>Teknik SEO</h2><p>İyi bir SEO için önce teknik altyapıyı sağlamalısınız. Sayfa hızı, mobil uyumluluk ve yapılandırılmış veri bunların başında gelir.</p><h2>İçerik Stratejisi</h2><p>Kaliteli, özgün içerik üretmek SEO\'nun temelidir.</p>',
  'Web sitenizi arama motorlarında üst sıralara taşıyacak temel SEO tekniklerini öğrenin.',
  'SEO',
  1
)"
];

foreach ($sqls as $i => $sql) {
    try {
        $pdo->exec($sql);
        $ok[] = "SQL #$i OK";
    } catch (Throwable $e) {
        $errors[] = "SQL #$i FAILED: " . $e->getMessage();
    }
}

header('Content-Type: text/plain');
echo "=== DB INIT RESULT ===\n";
foreach ($ok as $msg) echo "✓ $msg\n";
foreach ($errors as $msg) echo "✗ $msg\n";
echo "\nDone. DELETE this file now!\n";
