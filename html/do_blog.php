<?php
if (($_GET['key'] ?? '') !== 'ak_blog_2026') { http_response_code(403); exit('forbidden'); }
require_once __DIR__ . '/config.php';

if ($_GET['action'] === 'list') {
    $posts = db()->query("SELECT id, slug, title, LENGTH(content) as len FROM posts WHERE published=1")->fetchAll();
    echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_GET['action'] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $s = db()->prepare("UPDATE posts SET content=?, excerpt=?, updated_at=NOW() WHERE slug=?");
    $s->execute([$data['content'], $data['excerpt'], $data['slug']]);
    echo json_encode(['ok' => true, 'affected' => $s->rowCount()]);
    exit;
}

echo json_encode(['error' => 'invalid action']);
