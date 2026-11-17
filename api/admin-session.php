<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$user = admin_user();
if (!$user) { echo json_encode(['authenticated' => false]); exit; }
$categories = db()->query('SELECT id,name FROM categories WHERE active=1 ORDER BY sort_order,name')->fetchAll();
echo json_encode(['authenticated' => true, 'user' => $user, 'csrf' => csrf_token(), 'categories' => $categories], JSON_UNESCAPED_UNICODE);
