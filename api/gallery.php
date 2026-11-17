<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=120, stale-while-revalidate=600');
try {
    $media = db()->query('SELECT id,title,description,media_type,path,focal_x,focal_y FROM gallery_media WHERE active=1 ORDER BY sort_order,id DESC')->fetchAll();
    echo json_encode(['ok' => true, 'media' => $media], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'gallery_unavailable']);
}
