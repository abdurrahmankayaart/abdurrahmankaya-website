<?php
if (($_GET['k'] ?? '') !== 'setsmtp2026') { http_response_code(403); exit; }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config.php';

$vals = [
    'smtp_host' => 'smtp-relay.brevo.com',
    'smtp_port' => '587',
    'smtp_user' => 'aa8777001@smtp-brevo.com',
    'smtp_from' => 'hello@abdurrahmankaya.com',
];

$stmt = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
foreach ($vals as $k => $v) {
    $stmt->execute([$k, $v, $v]);
    echo "set $k = $v\n";
}
echo "----\n";
echo "smtp_pass durumu: " . (get_setting('smtp_pass') ? '(DOLU)' : '(BOŞ — admin panelden SMTP Sifre gir)') . "\n";
echo "Done.\n";
