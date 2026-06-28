<?php
if (($_GET['k'] ?? '') !== 'setga2026') { http_response_code(403); exit; }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config.php';

$v = 'G-ND3MXNPMT9';
$stmt = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
$stmt->execute(['ga_id', $v, $v]);
echo "ga_id = $v\n";
echo "Done.\n";
