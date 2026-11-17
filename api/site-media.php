<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $slots = db()->query('SELECT slot_key,label,media_type,path,focal_x,focal_y FROM site_media_slots ORDER BY id')->fetchAll();
    if (admin_user()) {
        foreach ($slots as &$slot) {
            $stmt = db()->prepare('SELECT id,path,focal_x,focal_y,created_at FROM site_media_history WHERE slot_id=(SELECT id FROM site_media_slots WHERE slot_key=?) ORDER BY created_at DESC,id DESC');
            $stmt->execute([$slot['slot_key']]);
            $slot['history'] = $stmt->fetchAll();
        }
        unset($slot);
    }
    echo json_encode(['ok'=>true,'slots'=>$slots], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch (Throwable) { http_response_code(503); echo json_encode(['ok'=>false]); }
