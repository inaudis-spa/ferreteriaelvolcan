# Ferretería El Volcán

Sitio catálogo con carrito de cotización por WhatsApp.

## Ejecutar

El proyecto no requiere compilación ni dependencias. Puede abrirse desde Laragon en la carpeta pública o con cualquier servidor HTTP local.

- Entrada: `index.html`
- Estilos: `styles.css`
- Catálogo y carrito: `app.js`
- Material del comercio: `ferreteriaelvolcan/`

## Panel administrativo

El proyecto utiliza PHP 8.3 y MySQL 8 para administrar el catálogo.

1. Inicia Apache y MySQL en Laragon.
2. Abre `/ferreteriaelvolcan/admin/setup.php`.
3. Crea el primer administrador. No existen credenciales predeterminadas.
4. Ingresa posteriormente desde `/ferreteriaelvolcan/admin/`.

La instalación crea la base `ferreteria_volcan` y sus tablas. En servidores con otras credenciales, utiliza variables `VOLCAN_DB_HOST`, `VOLCAN_DB_PORT`, `VOLCAN_DB_NAME`, `VOLCAN_DB_USER` y `VOLCAN_DB_PASS`, o copia `config.example.php` como `config.local.php` sin subir este último al repositorio.

El panel permite administrar productos, categorías, fotografías por producto, portada, precios, stock, publicación, ofertas y una galería independiente de fotos/videos. Los archivos cargados se almacenan en `uploads/`, con ejecución de scripts bloqueada mediante `.htaccess`.

Las fotografías nuevas se procesan automáticamente con GD: se corrige la orientación EXIF, se convierten a WebP con calidad 82 y se generan versiones de 480, 960 y 1600 px. En el panel se puede ajustar en línea el texto alternativo, orden y punto focal X/Y para controlar el encuadre responsive. Los medios de la galería permiten editar título, descripción, orden y visibilidad en el mismo bloque.

## Datos comerciales pendientes

Los productos visibles se derivan del material entregado. Antes de publicar deben confirmarse nombres comerciales, SKU, marcas, presentaciones, precios, stock, dirección, horario, cobertura y modalidades de entrega/retiro. El flujo actual solicita cotización y no registra, reserva ni cobra pedidos.
# Edición visual de fotos y videos

Al iniciar sesión como administrador, la opción **Fotos y videos** abre `index.html?edit=media`. Ese modo permite reemplazar directamente el video de introducción, el video principal, el video del proyecto terraza y la portada de ofertas. Las fotografías de productos y del catálogo se administran por separado desde **Productos**.
