<?php
require_once __DIR__ . '/../config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try { db()->prepare("DELETE FROM posts WHERE id=?")->execute([(int)$_POST['delete_id']]); }
    catch (Throwable) {}
    redirect('/admin/posts.php');
}

if (isset($_POST['toggle_id'])) {
    try {
        $s = db()->prepare("UPDATE posts SET published = 1 - published WHERE id=?");
        $s->execute([(int)$_POST['toggle_id']]);
    } catch (Throwable) {}
    redirect('/admin/posts.php');
}

try {
    $posts = db()->query("SELECT id,slug,title,category,published,views,created_at FROM posts ORDER BY created_at DESC")->fetchAll();
} catch (Throwable) { $posts = []; }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yazılar — Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#080810">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Tüm Yazılar</h1>
      <a href="/admin/edit.php" class="btn btn-primary btn-sm">+ Yeni Yazı</a>
    </div>

    <div class="posts-table-wrap">
      <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Başlık</th>
            <th>Kategori</th>
            <th>Durum</th>
            <th>Görüntülenme</th>
            <th>Tarih</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $p): ?>
          <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="badge badge-muted" style="font-size:.7rem"><?= e($p['category']) ?></span></td>
            <td>
              <form method="POST" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="toggle_id" value="<?= $p['id'] ?>">
                <button type="submit" class="badge <?= $p['published'] ? 'badge-success' : 'badge-warn' ?>" style="cursor:pointer;border:none;font-size:.75rem">
                  <?= $p['published'] ? 'Yayında' : 'Taslak' ?>
                </button>
              </form>
            </td>
            <td><?= (int)$p['views'] ?></td>
            <td style="color:var(--dim);font-size:.8rem"><?= date('d.m.Y', strtotime($p['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                <a href="/admin/edit.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Düzenle</a>
                <a href="/blog/<?= e($p['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm">Gör</a>
                <form method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.15);color:#fca5a5">Sil</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$posts): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--dim);padding:2rem">
            Henüz yazı yok. <a href="/admin/edit.php" style="color:var(--accent-l)">İlk yazıyı oluştur →</a>
          </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </main>
</div>
<script src="/js/main.js"></script>
</body>
</html>
