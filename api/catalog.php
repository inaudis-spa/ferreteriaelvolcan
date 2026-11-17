<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
try {
    $stmt = db()->query("SELECT p.id,p.category_id,p.name,p.slug,p.sku,p.brand,p.short_description,p.description,p.price,p.compare_price,p.stock,p.stock_mode,p.featured,p.on_offer,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id AND c.active=1 WHERE p.active=1 ORDER BY p.sort_order,p.id DESC");
    $products = $stmt->fetchAll();
    $imageStmt = db()->prepare('SELECT path,alt_text,is_cover,focal_x,focal_y FROM product_images WHERE product_id=? ORDER BY is_cover DESC,sort_order,id');
    foreach ($products as &$product) {
        $imageStmt->execute([$product['id']]);
        $product['images'] = $imageStmt->fetchAll();
        $product['price'] = $product['price'] === null ? null : (float)$product['price'];
        $product['compare_price'] = $product['compare_price'] === null ? null : (float)$product['compare_price'];
        $product['stock'] = (int)$product['stock'];
        $product['featured'] = (bool)$product['featured'];
        $product['on_offer'] = (bool)$product['on_offer'];
    }
    echo json_encode(['ok' => true, 'products' => $products], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'catalog_unavailable']);
}
