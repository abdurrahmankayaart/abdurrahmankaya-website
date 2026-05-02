SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (`key`, `value`) VALUES
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
  ('skills',       'JavaScript,TypeScript,React / Next.js,Node.js,PHP,MySQL,Docker,Linux');

-- Örnek blog yazıları
INSERT IGNORE INTO posts (slug, title, content, excerpt, category, published) VALUES
(
  'php-mysql-ile-modern-web-gelistirme',
  'PHP ve MySQL ile Modern Web Geliştirme',
  '<h2>Giriş</h2><p>PHP, 2024 yılında hâlâ web geliştirmenin güçlü bir seçeneği olmaya devam ediyor. Bu yazıda modern PHP pratiklerini inceliyoruz.</p><h2>PDO ile Güvenli Sorgular</h2><p>PDO kullanarak SQL injection saldırılarından korunabilir, prepared statement ile güvenli veritabanı işlemleri yapabilirsiniz.</p><pre><code>$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");\n$stmt->execute([$slug]);\n$post = $stmt->fetch();</code></pre><h2>Sonuç</h2><p>Modern PHP ile güvenli, hızlı ve ölçeklenebilir uygulamalar geliştirmek mümkün.</p>',
  'PHP ve MySQL ile modern, güvenli ve ölçeklenebilir web uygulamaları geliştirme rehberi.',
  'Web Geliştirme',
  1
),
(
  'docker-ile-php-deploy',
  'Docker ile PHP Uygulaması Deploy Etme',
  '<h2>Neden Docker?</h2><p>Docker, uygulamanızı her ortamda aynı şekilde çalıştırmanızı sağlar. Geliştirme, test ve üretim ortamları arasındaki farkları ortadan kaldırır.</p><h2>Coolify ile Kolay Deploy</h2><p>Coolify, Docker uygulamalarını kendi sunucunuzda kolayca yönetmenizi sağlayan açık kaynaklı bir platformdur.</p><h2>Volume Yönetimi</h2><p>Kalıcı veriler için Docker volume kullanmak kritik öneme sahiptir. Özellikle dosya yüklemeleri ve veritabanı için volume tanımlamalısınız.</p>',
  'Docker ve Coolify kullanarak PHP uygulamanızı kendi sunucunuzda nasıl deploy edeceğinizi öğrenin.',
  'DevOps',
  1
),
(
  'seo-optimizasyonu-temelleri',
  'SEO Optimizasyonu: Temel Teknikler',
  '<h2>Teknik SEO</h2><p>İyi bir SEO için önce teknik altyapıyı sağlamalısınız. Sayfa hızı, mobil uyumluluk ve yapılandırılmış veri bunların başında gelir.</p><h2>Meta Etiketleri</h2><p>Her sayfa için benzersiz title ve description meta etiketleri yazın. OpenGraph etiketleri sosyal medya paylaşımları için kritiktir.</p><h2>İçerik Stratejisi</h2><p>Kaliteli, özgün içerik üretmek SEO'nun temelidir. Anahtar kelimeleri doğal bir şekilde kullanın.</p>',
  'Web sitenizi arama motorlarında üst sıralara taşıyacak temel SEO tekniklerini öğrenin.',
  'SEO',
  1
);
