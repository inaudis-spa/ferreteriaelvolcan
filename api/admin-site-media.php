<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (!admin_user()) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit; }
try {
    verify_csrf();
    $key = (string)($_POST['slot_key'] ?? '');
    $stmt = db()->prepare('SELECT * FROM site_media_slots WHERE slot_key=?'); $stmt->execute([$key]); $slot=$stmt->fetch();
    if (!$slot) throw new RuntimeException('Sección visual no encontrada.');
    $uploaded = upload_file($_FILES['media'], $slot['media_type']);
    db()->prepare('UPDATE site_media_slots SET path=? WHERE id=?')->execute([$uploaded['path'],$slot['id']]);
    $old = realpath(PROJECT_ROOT.'/'.$slot['path']); $root=realpath(UPLOAD_ROOT);
    if($old&&$root&&str_starts_with($old,$root.DIRECTORY_SEPARATOR)&&is_file($old)) unlink($old);
    echo json_encode(['ok'=>true,'path'=>$uploaded['path'],'slot'=>$key], JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) { http_response_code(422);echo json_encode(['ok'=>false,'error'=>$e->getMessage()],JSON_UNESCAPED_UNICODE); }
