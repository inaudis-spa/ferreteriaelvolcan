<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=900');
try {
 $rows=db()->query('SELECT name,slug,description FROM categories WHERE active=1 ORDER BY sort_order,name')->fetchAll();
 echo json_encode(['ok'=>true,'categories'=>$rows],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) {
 http_response_code(503); echo json_encode(['ok'=>false,'error'=>'categories_unavailable']);
}
