<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$user = admin_user();
if (!$user) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'method']); exit; }
try {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    if (!$id || $name === '') throw new RuntimeException('Faltan datos obligatorios.');
    $price = ($_POST['price'] ?? '') === '' ? null : max(0, (float)$_POST['price']);
    $stmt = db()->prepare('UPDATE products SET name=?,category_id=?,short_description=?,description=?,price=?,stock=?,active=?,updated_at=CURRENT_TIMESTAMP WHERE id=?');
    $stmt->execute([$name,(int)($_POST['category_id']??0)?:null,trim((string)($_POST['short_description']??'')),trim((string)($_POST['description']??'')),$price,max(0,(int)($_POST['stock']??0)),isset($_POST['active'])?1:0,$id]);
    echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
