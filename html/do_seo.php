<?php
if (($_GET['key'] ?? '') !== 'ak_seo_2026') { http_response_code(403); exit('forbidden'); }
require_once __DIR__ . '/config.php';

$updates = [
    'site_title'  => 'Meta Reklam Uzmanı & Dijital Pazarlama Danışmanı',
    'site_desc'   => 'Facebook ve Instagram reklam uzmanı Abdurrahman Kaya. Meta Ads kampanyalarınızı yönetir, dönüşümlerinizi artırır, reklam bütçenizi en verimli şekilde kullanırım. Türkiye geneli hizmet.',
    'hero_title'  => 'Facebook & Meta Reklam Uzmanı',
    'hero_subtitle' => 'İşletmenizin Facebook ve Instagram reklamlarını yönetiyorum. Hedef kitleye özel kampanyalar, dönüşüm odaklı stratejiler ve sürekli optimizasyon ile satışlarınızı artırıyorum.',
];

$s = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
$results = [];
foreach ($updates as $k => $v) {
    $s->execute([$k, $v, $v]);
    $results[$k] = $v;
}
echo json_encode(['ok' => true, 'updated' => $results], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
