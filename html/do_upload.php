<?php
if (($_GET['key'] ?? '') !== 'ak_deploy_2026') { http_response_code(403); exit; }
require_once __DIR__ . '/config.php';

if (isset($_GET['ls'])) {
    $files = glob(__DIR__ . '/uploads/*') ?: [];
    echo json_encode(['dir'=>__DIR__.'/uploads', 'files'=>array_map('basename',$files), 'writable'=>is_writable(__DIR__.'/uploads')]);
    exit;
}
if (isset($_GET['set'])) {
    $s = db()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?");
    $s->execute([$_GET['set'], $_GET['val'], $_GET['val']]);
    echo json_encode(['ok'=>true]);
    exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['f'])) {
    $dest = __DIR__ . '/uploads/' . basename($_FILES['f']['name']);
    $ok = move_uploaded_file($_FILES['f']['tmp_name'], $dest);
    echo json_encode(['ok'=>$ok,'path'=>'/uploads/'.basename($_FILES['f']['name']),'dest'=>$dest]);
    exit;
}
echo 'ready';
