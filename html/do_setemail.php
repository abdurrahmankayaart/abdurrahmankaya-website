<?php
if (($_GET['k'] ?? '') !== 'setemail2026') { http_response_code(403); exit; }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config.php';

$stmt = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
$v = 'hello@abdurrahmankaya.com';
$stmt->execute(['site_email', $v, $v]);
echo "site_email = $v\n";
echo "Done.\n";
