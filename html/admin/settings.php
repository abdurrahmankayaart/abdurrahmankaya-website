<?php
require_once __DIR__ . '/../config.php';
require_admin();

$success = '';
$error   = '';

// Profil fotoğrafı yükleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_photo_file'])) {
    $file = $_FILES['hero_photo_file'];
    if ($file['size'] > 0) {
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $error = 'Desteklenmeyen görsel formatı';
        } elseif ($file['size'] > 10 * 1024 * 1024) {
            $error = 'Dosya çok büyük (max 10MB)';
        } else {
            $name = 'profile-' . time() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $name;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $url = '/uploads/' . $name;
                try {
                    $s = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES ('hero_photo',?) ON DUPLICATE KEY UPDATE `value`=?");
                    $s->execute([$url, $url]);
                } catch (Throwable) {}
            }
        }
    }
}

// Ayarları kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['hero_photo_file'])) {
    $fields = ['site_name','site_title','site_desc','site_email','linkedin','github','twitter','instagram','hero_title','hero_subtitle','about_text','skills','stat_years','stat_projects','stat_clients','phone','address','whatsapp','ga_id','smtp_host','smtp_port','smtp_user','smtp_pass'];
    try {
        $stmt = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
        foreach ($fields as $f) {
            $val = trim($_POST[$f] ?? '');
            $stmt->execute([$f, $val, $val]);
        }
        $success = 'Ayarlar kaydedildi!';
    } catch (Throwable $e) {
        $error = 'Hata: ' . $e->getMessage();
    }
}

$cfg = get_all_settings();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Ayarları — Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Site Ayarları</h1>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

    <!-- Profil Fotoğrafı -->
    <div class="editor-wrap" style="margin-bottom:1.5rem">
      <h2 style="margin-bottom:1.25rem">Profil / Hero Fotoğrafı</h2>
      <form method="POST" enctype="multipart/form-data" style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap">
        <?php if ($cfg['hero_photo'] ?? ''): ?>
          <img src="<?= e($cfg['hero_photo']) ?>" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
        <?php else: ?>
          <div style="width:100px;height:100px;border-radius:50%;background:var(--bg4);display:flex;align-items:center;justify-content:center;font-size:2.5rem">👤</div>
        <?php endif; ?>
        <div>
          <label class="btn btn-ghost btn-sm" style="cursor:pointer">
            📸 Fotoğraf Seç
            <input type="file" name="hero_photo_file" accept="image/*" style="display:none" onchange="this.form.submit()">
          </label>
          <p style="font-size:.8rem;color:var(--dim);margin-top:.5rem">JPG, PNG veya WebP. En fazla 10MB.</p>
        </div>
      </form>
    </div>

    <!-- Site Bilgileri -->
    <form method="POST">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

        <div class="editor-wrap" style="grid-column:1/-1">
          <h2 style="margin-bottom:1.25rem">Genel Bilgiler</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label>Ad Soyad</label>
              <input type="text" name="site_name" class="form-control" value="<?= e($cfg['site_name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Ünvan / Meslek</label>
              <input type="text" name="site_title" class="form-control" value="<?= e($cfg['site_title'] ?? '') ?>">
            </div>
            <div class="form-group" style="grid-column:1/-1">
              <label>Site Açıklaması (SEO)</label>
              <textarea name="site_desc" class="form-control" rows="2"><?= e($cfg['site_desc'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
              <label>E-posta</label>
              <input type="email" name="site_email" class="form-control" value="<?= e($cfg['site_email'] ?? '') ?>">
            </div>
          </div>
        </div>

        <div class="editor-wrap">
          <h2 style="margin-bottom:1.25rem">Hero Bölümü</h2>
          <div class="form-group">
            <label>Ana Başlık</label>
            <input type="text" name="hero_title" class="form-control" value="<?= e($cfg['hero_title'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Alt Başlık / Açıklama</label>
            <textarea name="hero_subtitle" class="form-control" rows="3"><?= e($cfg['hero_subtitle'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="editor-wrap">
          <h2 style="margin-bottom:1.25rem">Sosyal Medya</h2>
          <div class="form-group">
            <label>LinkedIn URL</label>
            <input type="url" name="linkedin" class="form-control" value="<?= e($cfg['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/kullanici">
          </div>
          <div class="form-group">
            <label>GitHub URL</label>
            <input type="url" name="github" class="form-control" value="<?= e($cfg['github'] ?? '') ?>" placeholder="https://github.com/kullanici">
          </div>
          <div class="form-group">
            <label>Twitter / X URL</label>
            <input type="url" name="twitter" class="form-control" value="<?= e($cfg['twitter'] ?? '') ?>" placeholder="https://x.com/kullanici">
          </div>
          <div class="form-group">
            <label>Instagram URL</label>
            <input type="url" name="instagram" class="form-control" value="<?= e($cfg['instagram'] ?? '') ?>" placeholder="https://instagram.com/kullanici">
          </div>
        </div>

        <div class="editor-wrap" style="grid-column:1/-1">
          <h2 style="margin-bottom:1.25rem">Hakkımda</h2>
          <div class="form-group">
            <label>Hakkımda Metni</label>
            <textarea name="about_text" class="form-control" rows="5"><?= e($cfg['about_text'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Yetenekler / Teknolojiler (virgülle ayır)</label>
            <input type="text" name="skills" class="form-control" value="<?= e($cfg['skills'] ?? '') ?>" placeholder="PHP, MySQL, Docker, React">
          </div>
        </div>

        <div class="editor-wrap">
          <h2 style="margin-bottom:1.25rem">Hero İstatistikler</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
            <div class="form-group">
              <label>Yıl Deneyim</label>
              <input type="number" name="stat_years" class="form-control" value="<?= e($cfg['stat_years'] ?? '5') ?>" min="0">
            </div>
            <div class="form-group">
              <label>Tamamlanan Proje</label>
              <input type="number" name="stat_projects" class="form-control" value="<?= e($cfg['stat_projects'] ?? '50') ?>" min="0">
            </div>
            <div class="form-group">
              <label>Mutlu Müşteri</label>
              <input type="number" name="stat_clients" class="form-control" value="<?= e($cfg['stat_clients'] ?? '30') ?>" min="0">
            </div>
          </div>
        </div>

        <div class="editor-wrap">
          <h2 style="margin-bottom:1.25rem">İletişim Bilgileri</h2>
          <div class="form-group">
            <label>Telefon</label>
            <input type="text" name="phone" class="form-control" value="<?= e($cfg['phone'] ?? '') ?>" placeholder="+90 5XX XXX XX XX">
          </div>
          <div class="form-group">
            <label>WhatsApp Numarası (başında + ile)</label>
            <input type="text" name="whatsapp" class="form-control" value="<?= e($cfg['whatsapp'] ?? '') ?>" placeholder="+905XXXXXXXXX">
          </div>
          <div class="form-group">
            <label>Adres</label>
            <input type="text" name="address" class="form-control" value="<?= e($cfg['address'] ?? '') ?>" placeholder="İstanbul, Türkiye">
          </div>
        </div>

        <div class="editor-wrap" style="grid-column:1/-1">
          <h2 style="margin-bottom:1.25rem">Analitik & E-posta</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group" style="grid-column:1/-1">
              <label>Google Analytics 4 Measurement ID</label>
              <input type="text" name="ga_id" class="form-control" value="<?= e($cfg['ga_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
              <small style="color:var(--dim)">Google Analytics konsolundan alınan Measurement ID. Boş bırakırsanız GA devre dışı.</small>
            </div>
            <div class="form-group">
              <label>SMTP Host</label>
              <input type="text" name="smtp_host" class="form-control" value="<?= e($cfg['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
            </div>
            <div class="form-group">
              <label>SMTP Port</label>
              <input type="number" name="smtp_port" class="form-control" value="<?= e($cfg['smtp_port'] ?? '587') ?>" placeholder="587">
            </div>
            <div class="form-group">
              <label>SMTP Kullanıcı</label>
              <input type="email" name="smtp_user" class="form-control" value="<?= e($cfg['smtp_user'] ?? '') ?>" placeholder="ornek@gmail.com">
            </div>
            <div class="form-group">
              <label>SMTP Şifre / Uygulama Şifresi</label>
              <input type="password" name="smtp_pass" class="form-control" value="<?= e($cfg['smtp_pass'] ?? '') ?>" placeholder="••••••••">
            </div>
          </div>
        </div>

      </div>
      <div style="margin-top:1.5rem;text-align:right">
        <button type="submit" class="btn btn-primary">💾 Ayarları Kaydet</button>
      </div>
    </form>
  </main>
</div>

<script src="/js/main.js"></script>
</body>
</html>
