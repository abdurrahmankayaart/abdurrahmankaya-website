<?php
require_once __DIR__ . '/config.php';
$cfg = get_all_settings();
$site_name  = $cfg['site_name']    ?? 'Abdurrahman Kaya';
$site_title = $cfg['site_title']   ?? 'Yazılım Geliştirici & Teknoloji Danışmanı';
$site_desc  = $cfg['site_desc']    ?? '';
$hero_photo = $cfg['hero_photo']   ?? '';

// Son 3 yazı
try {
    $posts = db()->query("SELECT id,slug,title,excerpt,category,cover_image,created_at FROM posts WHERE published=1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
} catch (Throwable) { $posts = []; }

$skills = array_filter(array_map('trim', explode(',', $cfg['skills'] ?? '')));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($site_name) ?> — <?= e($site_title) ?></title>
  <meta name="description" content="<?= e($site_desc) ?>">
  <meta name="author" content="<?= e($site_name) ?>">
  <link rel="canonical" href="<?= SITE_URL ?>/">
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="<?= SITE_URL ?>/">
  <meta property="og:title"       content="<?= e($site_name) ?> — <?= e($site_title) ?>">
  <meta property="og:description" content="<?= e($site_desc) ?>">
  <?php if ($hero_photo): ?><meta property="og:image" content="<?= SITE_URL . e($hero_photo) ?>"><?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <?php
  $ldPerson = [
    '@context' => 'https://schema.org',
    '@type'    => 'Person',
    'name'     => $site_name,
    'url'      => SITE_URL,
    'jobTitle' => $site_title,
    'email'    => $cfg['site_email'] ?? '',
  ];
  $sameAs = array_filter([$cfg['linkedin'] ?? '', $cfg['github'] ?? '', $cfg['twitter'] ?? '', $cfg['instagram'] ?? '']);
  if ($sameAs) $ldPerson['sameAs'] = array_values($sameAs);
  if ($hero_photo) $ldPerson['image'] = SITE_URL . $hero_photo;
  ?>
  <script type="application/ld+json"><?= json_encode($ldPerson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#080810">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <?= ga_snippet() ?>
</head>
<body>

<div class="mobile-menu" role="dialog" aria-label="Navigasyon menüsü">
  <button class="mobile-close" aria-label="Kapat">✕</button>
  <a href="#anasayfa">Anasayfa</a>
  <a href="#hakkimda">Hakkımda</a>
  <a href="#hizmetler">Hizmetler</a>
  <a href="/blog.php">Blog</a>
  <a href="#iletisim" class="btn btn-primary" style="margin-top:.5rem;justify-content:center">İletişime Geç</a>
</div>

<header id="header" role="banner">
  <div class="container">
    <nav class="nav-inner" aria-label="Ana navigasyon">
      <a href="/" class="nav-logo">AK<span>.</span></a>
      <ul class="nav-links">
        <li><a href="#anasayfa">Anasayfa</a></li>
        <li><a href="#hakkimda">Hakkımda</a></li>
        <li><a href="#hizmetler">Hizmetler</a></li>
        <li><a href="/blog.php">Blog</a></li>
      </ul>
      <a href="#iletisim" class="btn btn-primary btn-sm nav-cta">İletişime Geç</a>
      <button class="hamburger" aria-label="Menüyü aç"><span></span><span></span><span></span></button>
    </nav>
  </div>
</header>

<main>
<!-- ── Hero ── -->
<section id="anasayfa" class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content">
        <div class="hero-badge animate-up">
          <span class="hero-badge-dot"></span>
          <?= e($site_title) ?>
        </div>
        <h1 class="animate-up delay-1"><?= e($cfg['hero_title'] ?? 'Dijital Dönüşümde Güvenilir Partneriniz') ?></h1>
        <p class="hero-desc animate-up delay-2"><?= e($cfg['hero_subtitle'] ?? '') ?></p>
        <div class="hero-actions animate-up delay-3">
          <a href="#hizmetler" class="btn btn-primary btn-lg">Hizmetleri Keşfet</a>
          <a href="#hakkimda"  class="btn btn-outline btn-lg">Hakkımda</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <div class="hero-stat-num" data-counter data-target="<?= (int)($cfg['stat_years'] ?? 5) ?>" data-suffix="+">0</div>
            <div class="hero-stat-lbl">Yıl Deneyim</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num" data-counter data-target="<?= (int)($cfg['stat_projects'] ?? 50) ?>" data-suffix="+">0</div>
            <div class="hero-stat-lbl">Tamamlanan Proje</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num" data-counter data-target="<?= (int)($cfg['stat_clients'] ?? 30) ?>" data-suffix="+">0</div>
            <div class="hero-stat-lbl">Mutlu Müşteri</div>
          </div>
        </div>
      </div>
      <div class="hero-img-wrap animate-up delay-2">
        <div class="hero-img-ring">
          <div class="hero-img-inner">
            <?php if ($hero_photo): ?>
              <img src="<?= e($hero_photo) ?>" alt="<?= e($site_name) ?>">
            <?php else: ?>
              <div class="hero-placeholder">👨‍💻</div>
            <?php endif; ?>
          </div>
        </div>
        <div class="hero-float-badge top-right">✅ <?= (int)($cfg['stat_years'] ?? 5) ?>+ Yıl Deneyim</div>
        <div class="hero-float-badge bottom-left">⭐ <?= (int)($cfg['stat_projects'] ?? 50) ?>+ Proje</div>
      </div>
    </div>
  </div>
</section>

<!-- ── Hizmetler ── -->
<section id="hizmetler" class="section">
  <div class="container">
    <div class="section-header">
      <span class="badge badge-accent" data-reveal>Hizmetler</span>
      <h2 data-reveal>Size Nasıl Yardımcı Olabilirim?</h2>
      <p data-reveal>Facebook ve Instagram reklamlarınızı profesyonel bir şekilde yöneterek işletmenizi büyütüyorum.</p>
    </div>
    <div class="services-grid">
      <div class="service-card" data-reveal>
        <div class="service-icon">🎯</div>
        <h3>Meta Reklam Yönetimi</h3>
        <p>Facebook ve Instagram reklamlarınızı hedef kitleye özel stratejilerle yönetir, bütçenizi en verimli şekilde kullanırım.</p>
        <a href="#iletisim" class="learn-more">Detaylı İncele →</a>
      </div>
      <div class="service-card" data-reveal>
        <div class="service-icon">📊</div>
        <h3>Reklam Optimizasyonu</h3>
        <p>Mevcut kampanyalarınızı analiz ederek dönüşüm oranlarını artırır, reklam maliyetlerini düşürürüm.</p>
        <a href="#iletisim" class="learn-more">Detaylı İncele →</a>
      </div>
      <div class="service-card" data-reveal>
        <div class="service-icon">🛍️</div>
        <h3>E-Ticaret Reklamları</h3>
        <p>Meta Catalog, Dynamic Ads ve Advantage+ alışveriş kampanyalarıyla e-ticaret satışlarınızı büyütürüm.</p>
        <a href="#iletisim" class="learn-more">Detaylı İncele →</a>
      </div>
      <div class="service-card" data-reveal>
        <div class="service-icon">📈</div>
        <h3>Strateji & Danışmanlık</h3>
        <p>İşletmenize özel Meta reklam stratejisi oluşturur, ekibinizi eğitir ve büyüme yol haritanızı çizerim.</p>
        <a href="#iletisim" class="learn-more">Detaylı İncele →</a>
      </div>
    </div>
  </div>
</section>

<!-- ── Hakkımda ── -->
<section id="hakkimda" class="section" style="background:var(--bg2)">
  <div class="container">
    <div class="about-grid">
      <div class="about-image" data-reveal>
        <?php if ($hero_photo): ?>
          <img src="<?= e($hero_photo) ?>" alt="<?= e($site_name) ?>">
        <?php else: ?>
          <div class="about-placeholder">👨‍💻</div>
        <?php endif; ?>
      </div>
      <div class="about-content">
        <span class="badge badge-accent" data-reveal>Hakkımda</span>
        <h2 data-reveal>Merhaba, Ben <?= e($site_name) ?></h2>
        <p data-reveal><?= nl2br(e($cfg['about_text'] ?? '')) ?></p>
        <?php if ($skills): ?>
        <div class="skills-list" data-reveal>
          <?php foreach ($skills as $skill): ?>
            <span class="skill-tag"><?= e($skill) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <a href="#iletisim" class="btn btn-primary" data-reveal>İletişime Geç</a>
      </div>
    </div>
  </div>
</section>

<!-- ── Blog ── -->
<?php if ($posts): ?>
<section id="blog" class="section">
  <div class="container">
    <div class="section-header">
      <span class="badge badge-accent" data-reveal>Blog</span>
      <h2 data-reveal>Son Yazılar</h2>
      <p data-reveal>Teknoloji, yazılım ve dijital dönüşüm hakkında içgörüler.</p>
    </div>
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
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem" data-reveal>
      <a href="/blog.php" class="btn btn-outline">Tüm Yazıları Gör</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── İletişim ── -->
<section id="iletisim" class="section" style="background:var(--bg2)">
  <div class="container">
    <div class="section-header">
      <span class="badge badge-accent" data-reveal>İletişim</span>
      <h2 data-reveal>Projenizi Hayata Geçirelim</h2>
      <p data-reveal>Fikrinizi güçlü bir dijital ürüne dönüştürmek için hemen iletişime geçin.</p>
    </div>
    <div class="contact-grid" data-reveal>
      <div class="contact-info">
        <?php if ($cfg['site_email'] ?? ''): ?>
        <div class="contact-item">
          <div class="contact-icon">✉️</div>
          <div><strong>E-posta</strong><br><a href="mailto:<?= e($cfg['site_email']) ?>"><?= e($cfg['site_email']) ?></a></div>
        </div>
        <?php endif; ?>
        <?php if ($cfg['phone'] ?? ''): ?>
        <div class="contact-item">
          <div class="contact-icon">📞</div>
          <div><strong>Telefon</strong><br><a href="tel:<?= e(preg_replace('/\s/', '', $cfg['phone'])) ?>"><?= e($cfg['phone']) ?></a></div>
        </div>
        <?php endif; ?>
        <?php if ($cfg['whatsapp'] ?? ''): ?>
        <div class="contact-item">
          <div class="contact-icon">💬</div>
          <div><strong>WhatsApp</strong><br><a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $cfg['whatsapp'])) ?>" target="_blank" rel="noopener">Mesaj Gönder</a></div>
        </div>
        <?php endif; ?>
        <?php if ($cfg['address'] ?? ''): ?>
        <div class="contact-item">
          <div class="contact-icon">📍</div>
          <div><strong>Konum</strong><br><?= e($cfg['address']) ?></div>
        </div>
        <?php endif; ?>
        <div class="contact-social">
          <?php if ($cfg['linkedin']  ?? ''): ?><a href="<?= e($cfg['linkedin'])  ?>" class="social-link" target="_blank" rel="noopener" aria-label="LinkedIn">in</a><?php endif; ?>
          <?php if ($cfg['github']    ?? ''): ?><a href="<?= e($cfg['github'])    ?>" class="social-link" target="_blank" rel="noopener" aria-label="GitHub">gh</a><?php endif; ?>
          <?php if ($cfg['twitter']   ?? ''): ?><a href="<?= e($cfg['twitter'])   ?>" class="social-link" target="_blank" rel="noopener" aria-label="Twitter">𝕏</a><?php endif; ?>
          <?php if ($cfg['instagram'] ?? ''): ?><a href="<?= e($cfg['instagram']) ?>" class="social-link" target="_blank" rel="noopener" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a><?php endif; ?>
        </div>
      </div>

      <form class="contact-form" id="contactForm" novalidate>
        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
        <div class="form-row">
          <div class="form-group">
            <label for="cf-name">Ad Soyad *</label>
            <input type="text" id="cf-name" name="name" class="form-control" required placeholder="Adınız Soyadınız">
          </div>
          <div class="form-group">
            <label for="cf-email">E-posta *</label>
            <input type="email" id="cf-email" name="email" class="form-control" required placeholder="ornek@email.com">
          </div>
        </div>
        <div class="form-group">
          <label for="cf-subject">Konu</label>
          <input type="text" id="cf-subject" name="subject" class="form-control" placeholder="Proje danışmanlığı, iş birliği...">
        </div>
        <div class="form-group">
          <label for="cf-message">Mesajınız *</label>
          <textarea id="cf-message" name="message" class="form-control" rows="5" required placeholder="Projenizi ve ihtiyaçlarınızı anlatın..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" id="cf-submit">Mesaj Gönder ✉️</button>
        <div id="cf-result" style="margin-top:1rem;display:none"></div>
      </form>
    </div>
  </div>
</section>
</main>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="/" class="nav-logo" style="font-size:1.25rem">AK<span>.</span></a>
        <p><?= e($site_desc) ?></p>
      </div>
      <div class="footer-col">
        <h4>Hızlı Linkler</h4>
        <ul>
          <li><a href="#anasayfa">Anasayfa</a></li>
          <li><a href="#hakkimda">Hakkımda</a></li>
          <li><a href="#hizmetler">Hizmetler</a></li>
          <li><a href="/blog.php">Blog</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>İletişim</h4>
        <ul>
          <li><a href="mailto:<?= e($cfg['site_email'] ?? '') ?>"><?= e($cfg['site_email'] ?? '') ?></a></li>
          <?php if ($cfg['linkedin'] ?? ''): ?><li><a href="<?= e($cfg['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a></li><?php endif; ?>
          <?php if ($cfg['github']   ?? ''): ?><li><a href="<?= e($cfg['github'])   ?>" target="_blank" rel="noopener">GitHub</a></li><?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e($site_name) ?>. Tüm hakları saklıdır.</p>
      <div class="social-links">
        <?php if ($cfg['linkedin']  ?? ''): ?><a href="<?= e($cfg['linkedin'])  ?>" class="social-link" aria-label="LinkedIn"  target="_blank" rel="noopener">in</a><?php endif; ?>
        <?php if ($cfg['github']    ?? ''): ?><a href="<?= e($cfg['github'])    ?>" class="social-link" aria-label="GitHub"    target="_blank" rel="noopener">gh</a><?php endif; ?>
        <?php if ($cfg['twitter']   ?? ''): ?><a href="<?= e($cfg['twitter'])   ?>" class="social-link" aria-label="Twitter"   target="_blank" rel="noopener">𝕏</a><?php endif; ?>
        <?php if ($cfg['instagram'] ?? ''): ?><a href="<?= e($cfg['instagram']) ?>" class="social-link" aria-label="Instagram" target="_blank" rel="noopener"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a><?php endif; ?>
      </div>
    </div>
  </div>
</footer>

<script src="/js/main.js"></script>
</body>
</html>
