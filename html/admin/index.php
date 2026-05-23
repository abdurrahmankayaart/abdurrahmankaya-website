<?php
require_once __DIR__ . '/../config.php';
require_admin();

// Hızlı istatistikler
try {
    $total_posts   = (int)db()->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $pub_posts     = (int)db()->query("SELECT COUNT(*) FROM posts WHERE published=1")->fetchColumn();
    $total_views   = (int)db()->query("SELECT COALESCE(SUM(views),0) FROM posts")->fetchColumn();
    $recent_posts  = db()->query("SELECT id,slug,title,published,views,created_at FROM posts ORDER BY created_at DESC LIMIT 8")->fetchAll();
} catch (Throwable) { $total_posts = $pub_posts = $total_views = 0; $recent_posts = []; }

// Yazı sil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_verify();
    try {
        $s = db()->prepare("DELETE FROM posts WHERE id=?");
        $s->execute([(int)$_POST['delete_id']]);
    } catch (Throwable) {}
    redirect('/admin/');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Dashboard</title>
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
      <h1>Dashboard</h1>
      <a href="/admin/edit.php" class="btn btn-primary btn-sm">+ Yeni Yazı</a>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-num"><?= $total_posts ?></div>
        <div class="stat-lbl">Toplam Yazı</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-num"><?= $pub_posts ?></div>
        <div class="stat-lbl">Yayında</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👁️</div>
        <div class="stat-num"><?= number_format($total_views) ?></div>
        <div class="stat-lbl">Toplam Görüntülenme</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📑</div>
        <div class="stat-num"><?= $total_posts - $pub_posts ?></div>
        <div class="stat-lbl">Taslak</div>
      </div>
    </div>

    <div class="posts-table-wrap">
      <div class="posts-table-header">
        <h2>Son Yazılar</h2>
        <a href="/admin/posts.php" style="font-size:.875rem;color:var(--accent-l)">Tümünü Gör →</a>
      </div>
      <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Başlık</th>
            <th>Kategori</th>
            <th>Durum</th>
            <th>Görüntülenme</th>
            <th>Tarih</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_posts as $p): ?>
          <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="badge badge-muted" style="font-size:.7rem"><?= e($p['category'] ?? 'Genel') ?></span></td>
            <td><?= $p['published'] ? '<span class="badge badge-success">Yayında</span>' : '<span class="badge badge-warn">Taslak</span>' ?></td>
            <td><?= (int)$p['views'] ?></td>
            <td style="color:var(--dim);font-size:.8rem"><?= date('d.m.Y', strtotime($p['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:.5rem">
                <a href="/admin/edit.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Düzenle</a>
                <a href="/blog/<?= e($p['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm">Gör</a>
                <form method="POST" onsubmit="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.15);color:#fca5a5">Sil</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$recent_posts): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--dim);padding:2rem">Henüz yazı yok. <a href="/admin/edit.php" style="color:var(--accent-l)">İlk yazıyı oluştur →</a></td></tr>
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
