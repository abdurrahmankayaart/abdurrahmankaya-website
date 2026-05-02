<?php
$current = basename($_SERVER['PHP_SELF']);
function nav_item(string $href, string $icon, string $label, string $current): string {
    $active = basename($href) === $current ? 'active' : '';
    return "<a href='$href' class='admin-nav-item $active'>$icon $label</a>";
}
?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-logo">
    <div class="logo-main">AK Admin</div>
    <div class="logo-sub">abdurrahmankaya.com</div>
  </div>
  <?= nav_item('/admin/', '📊', 'Dashboard', $current) ?>
  <?= nav_item('/admin/posts.php', '📝', 'Yazılar', $current) ?>
  <?= nav_item('/admin/edit.php', '✏️', 'Yeni Yazı', $current) ?>
  <?= nav_item('/admin/settings.php', '⚙️', 'Site Ayarları', $current) ?>
  <div class="admin-sidebar-spacer"></div>
  <a href="/" target="_blank" class="admin-nav-item">🌐 Siteyi Gör</a>
  <a href="/admin/logout.php" class="admin-nav-item" style="color:var(--danger)">🚪 Çıkış</a>
</aside>
