<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';

if ((int)db()->query('SELECT COUNT(*) FROM products')->fetchColumn() > 0) {
    exit("El catálogo ya contiene productos; no se aplicó la carga inicial.\n");
}

$categories = [
    ['Construcción', 'construccion', 'Cementos y materiales para obra', 10],
    ['Gasfitería', 'gasfiteria', 'Grifería, tuberías y conexiones', 20],
    ['Electricidad', 'electricidad', 'Iluminación y materiales eléctricos', 30],
    ['Maderas', 'maderas', 'Tableros y materiales de madera', 40],
];
$products = [
    ['Construcción','Cemento para construcción','cemento-construccion','Material esencial para obras, reparaciones y proyectos de construcción. Consulta la presentación apropiada y disponibilidad para tu proyecto.',['cemento.jpg','cemento2.jpg']],
    ['Construcción','Cemento multipropósito','cemento-multiproposito','Una solución versátil para distintos trabajos en obra. Podemos orientarte según el tipo de faena y la cantidad que necesites.',['cemento2.jpg','cemento.jpg']],
    ['Gasfitería','Grifería de cocina','griferia-cocina','Renueva tus espacios con una solución funcional para cocina. Revisa diferentes modelos y consulta compatibilidad antes de confirmar.',['llaves-griferia.jpg','llaves-griferia-2.jpg','llaves-griferia-3.jpg']],
    ['Gasfitería','Llave monoblock','llave-monoblock','Grifería práctica para instalaciones del hogar. Cotiza el modelo y los accesorios requeridos para una instalación completa.',['llaves-griferia-2.jpg','llaves-griferia.jpg','llaves-griferia-3.jpg']],
    ['Gasfitería','Grifería cuello alto','griferia-cuello-alto','Mayor comodidad y alcance para tu lavaplatos. Consulta dimensiones y tipo de conexión disponible.',['llaves-griferia-3.jpg','llaves-griferia.jpg','llaves-griferia-2.jpg']],
    ['Electricidad','Iluminación LED','iluminacion-led','Iluminación eficiente para renovar cada ambiente. Te ayudamos a seleccionar formato, potencia y tono de luz.',['luces.jpg','luces2.jpg','luces3.jpg']],
    ['Electricidad','Foco LED','foco-led','Luz focal para interiores y zonas de trabajo. Consulta opciones y características disponibles.',['luces2.jpg','luces.jpg','luces3.jpg']],
    ['Electricidad','Panel de iluminación','panel-iluminacion','Luz uniforme y moderna para tus espacios. Cotiza según el tamaño y tipo de instalación de tu proyecto.',['luces3.jpg','luces2.jpg','luces.jpg']],
    ['Maderas','Tablero OSB','tablero-osb','Tablero versátil para estructuras, revestimientos y proyectos. Consulta medidas, espesor y unidades necesarias.',['osb.jpg']],
    ['Gasfitería','Tubería PVC','tuberia-pvc','Soluciones para instalaciones y conducción. Confirma diámetro, largo y aplicación para preparar una cotización correcta.',['tubos-pvc.jpg']],
];

db()->beginTransaction();
try {
    $categoryInsert = db()->prepare('INSERT INTO categories(name,slug,description,sort_order,active) VALUES(?,?,?,?,1)');
    $categoryIds = [];
    foreach ($categories as $category) { $categoryInsert->execute($category); $categoryIds[$category[0]] = (int)db()->lastInsertId(); }
    $productInsert = db()->prepare("INSERT INTO products(category_id,name,slug,short_description,description,stock_mode,active,sort_order) VALUES(?,?,?,?,?,'consult',1,?)");
    $imageInsert = db()->prepare('INSERT INTO product_images(product_id,path,alt_text,is_cover,sort_order) VALUES(?,?,?,?,?)');
    foreach ($products as $order => [$category,$name,$slug,$description,$images]) {
        $productInsert->execute([$categoryIds[$category],$name,$slug,$description,$description,$order * 10]);
        $productId = (int)db()->lastInsertId();
        foreach ($images as $imageOrder => $image) $imageInsert->execute([$productId,'ferreteriaelvolcan/'.$image,$name,$imageOrder===0?1:0,$imageOrder]);
    }
    $galleryInsert = db()->prepare('INSERT INTO gallery_media(title,description,media_type,path,sort_order,active) VALUES(?,?,?,?,?,1)');
    $galleryInsert->execute(['Un pequeño gran proyecto','Video principal de Ferretería El Volcán','video','ferreteriaelvolcan/ferreteria-volcan-un-pequeno-gran-proyecto.mp4',10]);
    $galleryInsert->execute(['Proyecto terraza','Proyecto realizado con materiales de ferretería','video','ferreteriaelvolcan/proyecto-terraza.mp4',20]);
    foreach ([
        ['Materiales para construcción','Construcción','ferreteriaelvolcan/cemento.jpg',100],
        ['Grifería para tu proyecto','Gasfitería','ferreteriaelvolcan/llaves-griferia.jpg',110],
        ['Tableros para construir','Maderas','ferreteriaelvolcan/osb.jpg',120],
        ['Iluminación eficiente','Electricidad','ferreteriaelvolcan/luces.jpg',130],
        ['Soluciones en PVC','Gasfitería','ferreteriaelvolcan/tubos-pvc.jpg',140],
        ['Renueva tus espacios','Hogar','ferreteriaelvolcan/llaves-griferia-3.jpg',150],
    ] as $offer) $galleryInsert->execute([$offer[0],$offer[1],'image',$offer[2],$offer[3]]);
    db()->commit();
    echo "Carga inicial completada: 4 categorías, 10 productos y medios para web y ofertas.\n";
} catch (Throwable $e) {
    db()->rollBack();
    throw $e;
}
