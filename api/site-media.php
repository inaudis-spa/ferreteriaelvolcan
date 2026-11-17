<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');
try {
    $slots = db()->query('SELECT slot_key,label,media_type,path FROM site_media_slots ORDER BY id')->fetchAll();
    echo json_encode(['ok'=>true,'slots'=>$slots], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch (Throwable) { http_response_code(503); echo json_encode(['ok'=>false]); }
