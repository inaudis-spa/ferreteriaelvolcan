<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (!admin_user()) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit; }
try {
    verify_csrf();
    $key = (string)($_POST['slot_key'] ?? '');
    $stmt = db()->prepare('SELECT * FROM site_media_slots WHERE slot_key=?');
    $stmt->execute([$key]);
    $slot = $stmt->fetch();
    if (!$slot) throw new RuntimeException('Sección visual no encontrada.');

    $action = (string)($_POST['action'] ?? 'replace');
    $focalX = max(0, min(100, (int)($_POST['focal_x'] ?? $slot['focal_x'])));
    $focalY = max(0, min(100, (int)($_POST['focal_y'] ?? $slot['focal_y'])));

    if ($action === 'restore') {
        $historyId = (int)($_POST['history_id'] ?? 0);
        $historyStmt = db()->prepare('SELECT * FROM site_media_history WHERE id=? AND slot_id=?');
        $historyStmt->execute([$historyId, $slot['id']]);
        $history = $historyStmt->fetch();
        if (!$history) throw new RuntimeException('No se encontró la versión que deseas restaurar.');
        db()->beginTransaction();
        db()->prepare('INSERT INTO site_media_history (slot_id,path,focal_x,focal_y) VALUES (?,?,?,?)')
            ->execute([$slot['id'], $slot['path'], $slot['focal_x'], $slot['focal_y']]);
        db()->prepare('UPDATE site_media_slots SET path=?,focal_x=?,focal_y=? WHERE id=?')
            ->execute([$history['path'], $history['focal_x'], $history['focal_y'], $slot['id']]);
        db()->commit();
        echo json_encode(['ok'=>true,'path'=>$history['path'],'focal_x'=>(int)$history['focal_x'],'focal_y'=>(int)$history['focal_y'],'slot'=>$key], JSON_UNESCAPED_SLASHES);
        exit;
    }

    if (empty($_FILES['media']) || !is_array($_FILES['media'])) throw new RuntimeException('Selecciona una fotografía o video.');
    if ($slot['media_type'] === 'video') { $focalX = 50; $focalY = 50; }
    $uploaded = upload_file($_FILES['media'], $slot['media_type']);
    db()->beginTransaction();
    db()->prepare('INSERT INTO site_media_history (slot_id,path,focal_x,focal_y) VALUES (?,?,?,?)')
        ->execute([$slot['id'], $slot['path'], $slot['focal_x'], $slot['focal_y']]);
    db()->prepare('UPDATE site_media_slots SET path=?,focal_x=?,focal_y=? WHERE id=?')
        ->execute([$uploaded['path'], $focalX, $focalY, $slot['id']]);
    db()->commit();
    echo json_encode(['ok'=>true,'path'=>$uploaded['path'],'focal_x'=>$focalX,'focal_y'=>$focalY,'slot'=>$key], JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) {
    if (db()->inTransaction()) db()->rollBack();
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
