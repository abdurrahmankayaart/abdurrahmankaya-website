<?php
// ── Veritabanı & site ayarları ─────────────────────────
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'aksite');
define('DB_USER', getenv('DB_USER') ?: 'akuser');
define('DB_PASS', getenv('DB_PASS') ?: 'guclu-sifre-degistir');
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'admin123');
define('SITE_URL', rtrim(getenv('SITE_URL') ?: 'https://abdurrahmankaya.com', '/'));

// ── PDO bağlantısı ─────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

// ── Ayar okuma/yazma ───────────────────────────────────
function get_setting(string $key, string $default = ''): string {
    try {
        $s = db()->prepare('SELECT `value` FROM settings WHERE `key` = ?');
        $s->execute([$key]);
        $r = $s->fetchColumn();
        return $r !== false ? $r : $default;
    } catch (Throwable) { return $default; }
}

function get_all_settings(): array {
    try {
        $rows = db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        return array_column($rows, 'value', 'key');
    } catch (Throwable) { return []; }
}

// ── Yardımcı fonksiyonlar ──────────────────────────────
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function slugify(string $text): string {
    $tr = ['ş'=>'s','Ş'=>'S','ı'=>'i','İ'=>'I','ğ'=>'g','Ğ'=>'G','ü'=>'u','Ü'=>'U','ö'=>'o','Ö'=>'O','ç'=>'c','Ç'=>'C'];
    $text = strtr($text, $tr);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', trim($text));
    return $text;
}

function format_date(string $date): string {
    $months = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $months[(int)date('n', $t)] . ' ' . date('Y', $t);
}

function read_time(string $content): int {
    $words = str_word_count(strip_tags($content));
    return max(1, (int)ceil($words / 200));
}

function ga_snippet(): string {
    $id = get_setting('ga_id', '');
    if (!$id || !preg_match('/^G-[A-Z0-9]+$/', $id)) return '';
    $id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
    return "<script async src=\"https://www.googletagmanager.com/gtag/js?id=$id\"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','$id');</script>";
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function is_admin(): bool {
    return !empty($_SESSION['admin']);
}

function require_admin(): void {
    session_start();
    if (!is_admin()) redirect('/admin/login.php');
}
