<?php
require_once __DIR__ . '/../config.php';
require_admin();

$id      = (int)($_GET['id'] ?? 0);
$post    = null;
$success = '';
$error   = '';

if ($id) {
    try {
        $stmt = db()->prepare("SELECT * FROM posts WHERE id=?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
    } catch (Throwable) {}
}

// Kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title       = trim($_POST['title']      ?? '');
    $content     = $_POST['content']         ?? '';
    $excerpt     = trim($_POST['excerpt']    ?? '');
    $category    = trim($_POST['category']   ?? 'Genel');
    $tags        = trim($_POST['tags']       ?? '');
    $cover_image = trim($_POST['cover_image']?? '');
    $published   = isset($_POST['published']) ? 1 : 0;

    if (!$title || !$content) {
        $error = 'Başlık ve içerik zorunludur.';
    } else {
        if (!$excerpt) {
            $excerpt = mb_substr(strip_tags($content), 0, 200);
        }
        try {
            if ($id) {
                $slug = $post['slug'];
                $stmt = db()->prepare("UPDATE posts SET title=?,slug=?,content=?,excerpt=?,category=?,cover_image=?,tags=?,published=?,updated_at=NOW() WHERE id=?");
                $stmt->execute([$title,$slug,$content,$excerpt,$category,$cover_image,$tags,$published,$id]);
                $success = 'Yazı güncellendi!';
                $stmt2 = db()->prepare("SELECT * FROM posts WHERE id=?");
                $stmt2->execute([$id]);
                $post = $stmt2->fetch();
            } else {
                $slug    = slugify($title);
                $base    = $slug;
                $counter = 1;
                $chk     = db()->prepare("SELECT id FROM posts WHERE slug=?");
                while (true) {
                    $chk->execute([$slug]);
                    if (!$chk->fetchColumn()) break;
                    $slug = "$base-$counter";
                    $counter++;
                }
                $stmt = db()->prepare("INSERT INTO posts (slug,title,content,excerpt,category,cover_image,tags,published) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$slug,$title,$content,$excerpt,$category,$cover_image,$tags,$published]);
                $id   = (int)db()->lastInsertId();
                $stmt2 = db()->prepare("SELECT * FROM posts WHERE id=?");
                $stmt2->execute([$id]);
                $post    = $stmt2->fetch();
                $success = 'Yazı oluşturuldu!';
                header("Location: /admin/edit.php?id=$id&saved=1");
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Hata: ' . $e->getMessage();
        }
    }
}

// Görsel yükleme (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    header('Content-Type: application/json');
    $file = $_FILES['image'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext  = ['jpg','jpeg','png','gif','webp'];
    $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($ext, $allowed_ext)) { echo json_encode(['error'=>'Desteklenmeyen format']); exit; }
    if ($file['size'] > 10 * 1024 * 1024) { echo json_encode(['error'=>'Dosya çok büyük (max 10MB)']); exit; }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed_mime)) { echo json_encode(['error'=>'Geçersiz dosya içeriği']); exit; }
    $name = uniqid('img_') . '.' . $ext;
    $dest = __DIR__ . '/../uploads/' . $name;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['url' => '/uploads/' . $name]);
    } else {
        echo json_encode(['error' => 'Yükleme başarısız']);
    }
    exit;
}

$categories = ['Web Geliştirme','Mobil','DevOps','SEO','Teknoloji','Genel'];
try {
    $extra = db()->query("SELECT DISTINCT category FROM posts ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    $categories = array_unique(array_merge($categories, $extra));
} catch (Throwable) {}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $id ? 'Yazı Düzenle' : 'Yeni Yazı' ?> — Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1><?= $id ? 'Yazı Düzenle' : 'Yeni Yazı' ?></h1>
      <a href="/admin/" class="btn btn-ghost btn-sm">← Geri</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Yazı başarıyla oluşturuldu!</div><?php endif; ?>

    <form method="POST" class="editor-wrap">
      <?= csrf_field() ?>
      <div class="editor-grid">
        <!-- Sol: İçerik -->
        <div>
          <div class="form-group">
            <label>Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?= e($post['title'] ?? '') ?>" placeholder="Yazı başlığı" required>
          </div>
          <div class="form-group">
            <label>İçerik *</label>
            <div id="editor-container" style="height:400px"><?= $post['content'] ?? '' ?></div>
            <input type="hidden" name="content" id="content-input">
          </div>
          <div class="form-group">
            <label>Özet (boş bırakılırsa otomatik oluşturulur)</label>
            <textarea name="excerpt" class="form-control" rows="3" placeholder="Yazının kısa özeti..."><?= e($post['excerpt'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Sağ: Meta -->
        <div>
          <div class="form-group">
            <label>Kapak Görseli</label>
            <div class="upload-area" id="drop-zone">
              <p>📸 Tıkla veya sürükle</p>
              <p style="font-size:.75rem;color:var(--dim);margin-top:.25rem">JPG, PNG, WebP (max 10MB)</p>
              <input type="file" id="img-file" accept="image/*" style="display:none">
            </div>
            <?php if ($post['cover_image'] ?? ''): ?>
              <div class="upload-preview"><img src="<?= e($post['cover_image']) ?>" id="img-preview"></div>
            <?php else: ?>
              <div class="upload-preview hidden"><img src="" id="img-preview" alt=""></div>
            <?php endif; ?>
            <input type="hidden" name="cover_image" id="cover-image-input" value="<?= e($post['cover_image'] ?? '') ?>">
            <div id="upload-status" style="font-size:.8rem;margin-top:.5rem;color:var(--muted)"></div>
          </div>

          <div class="form-group">
            <label>Kategori</label>
            <select name="category" class="form-control">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= ($post['category'] ?? 'Genel') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Etiketler (virgülle ayır)</label>
            <input type="text" name="tags" class="form-control" value="<?= e($post['tags'] ?? '') ?>" placeholder="php, mysql, web">
          </div>

          <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="checkbox" name="published" <?= ($post['published'] ?? 1) ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--accent)">
              Yayında
            </label>
          </div>

          <?php if ($id && $post): ?>
          <div style="padding:1rem;background:var(--bg2);border-radius:var(--radius);font-size:.8rem;color:var(--dim);margin-bottom:1rem">
            <div>Slug: <code style="color:var(--accent-l)">/blog/<?= e($post['slug']) ?></code></div>
            <div style="margin-top:.25rem">Görüntülenme: <?= (int)$post['views'] ?></div>
            <div style="margin-top:.25rem">Oluşturulma: <?= format_date($post['created_at']) ?></div>
          </div>
          <?php endif; ?>

          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
            💾 <?= $id ? 'Güncelle' : 'Yayınla' ?>
          </button>

          <?php if ($id): ?>
          <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;margin-top:.5rem">🌐 Önizle</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </main>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const quill = new Quill('#editor-container', {
  theme: 'snow',
  placeholder: 'Yazı içeriğini buraya yazın...',
  modules: {
    toolbar: [
      [{ header: [2, 3, false] }],
      ['bold','italic','underline','strike'],
      ['blockquote','code-block'],
      [{ list:'ordered' },{ list:'bullet' }],
      ['link','image'],
      ['clean']
    ]
  }
});

document.querySelector('form').addEventListener('submit', () => {
  document.getElementById('content-input').value = quill.root.innerHTML;
});

// Görsel yükleme
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('img-file');
const preview  = document.querySelector('.upload-preview');
const previewImg = document.getElementById('img-preview');
const coverInput = document.getElementById('cover-image-input');
const statusEl   = document.getElementById('upload-status');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor = 'var(--accent)'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = ''; });
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.style.borderColor = ''; uploadFile(e.dataTransfer.files[0]); });
fileInput.addEventListener('change', () => uploadFile(fileInput.files[0]));

async function uploadFile(file) {
  if (!file) return;
  statusEl.textContent = '⏳ Yükleniyor...';
  const fd = new FormData();
  fd.append('image', file);
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  try {
    const res  = await fetch('/admin/edit.php', { method:'POST', body: fd, headers:{'X-CSRF-Token': csrfToken} });
    const data = await res.json();
    if (data.url) {
      coverInput.value = data.url;
      previewImg.src   = data.url;
      preview.classList.remove('hidden');
      statusEl.textContent = '✅ Yüklendi';
    } else {
      statusEl.textContent = '❌ ' + (data.error || 'Hata');
    }
  } catch { statusEl.textContent = '❌ Yükleme başarısız'; }
}
</script>
</body>
</html>
