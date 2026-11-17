# Prompt maestro INAUDIS — Catálogo administrable, fichas PDF y carrito

Referencia técnica: TACONI, rama `main`, commit `7f3b29b` del 28 de octubre de 2026.
Uso: copia desde «Prompt para Codex o Cursor». La revisión previa explica qué se conserva y qué se mejora.
Alcance de la revisión: lectura estática del repositorio. No se modificó el código, no se ejecutaron pruebas y no se verificó el despliegue.

## Qué reutilizar de TACONI

| Módulo | Qué está implementado en el código |
| --- | --- |
| Catálogo público | MySQL es la fuente de verdad. La API entrega productos activos. La página aplica búsqueda por nombre, SKU, marca y descripción, filtros por categoría y marca, y orden por precio o potencia. El frontend guarda una copia local para contingencias. [API](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/api/catalog.php#L32-L55) · [Catálogo](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/index.html#L811-L856) |
| Ficha de producto | Galería, datos comerciales y especificaciones, relacionados, PDF para ver o descargar y acceso a «Mi pedido». Con stock agrega al carrito. Sin stock ofrece consultar por WhatsApp. La ficha actual trae el catálogo y busca el producto en el navegador. [Ficha](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/producto.js#L81-L159) |
| Carrito y WhatsApp | Carrito local por navegador con imagen, SKU, nombre, precio, cantidad y total. Permite agregar, cambiar cantidades, quitar y retener los ítems al recargar. El formulario pide nombre y comuna, con dirección opcional. Prepara un mensaje con referencia, productos, subtotales, total y despacho por confirmar. Abrir WhatsApp no equivale a enviar el mensaje. No hay orden persistida, reserva de stock, confirmación de pago ni pasarela en este flujo. [Carrito](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/app-core.js#L59-L82) · [Cierre](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/index.html#L964-L1043) |
| Mantenedor | Ruta separada `/admin/`, acceso con sesión, listado de activos e inactivos, creación, edición, cambio de visibilidad, eliminación y cambio de contraseña. Formulario con selector de marca, categorías predefinidas, precios, stock, descripción, orden y campos técnicos. No se observó un CRUD independiente de marcas o categorías en el panel. [Panel](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/admin/index.php) · [Lógica](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/app-core.js#L178-L255) |
| Archivos | Fotos por producto con recorte en navegador, conversión a WebP en servidor e imagen principal. Un PDF asociado por producto con carga, reemplazo y eliminación. El servidor valida el tipo MIME, la firma PDF y un máximo de 15 MB para documentos. Galería pública independiente para fotos y videos, con título, descripción, visibilidad y orden. [Fotos](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/api/catalog.php#L81-L92) · [PDF](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/api/documents.php) · [Galería](https://github.com/inaudis-spa/taconi/blob/7f3b29b64d21d5d704d469c26504c7954f9847b4/api/gallery.php#L126-L207) |

### Qué mejorar antes de copiar el patrón

- **Carrito desactualizado.** El precio queda guardado al agregar y la cantidad no se contrasta con el stock disponible al modificarla o preparar el mensaje. El nuevo sitio debe revalidar productos activos, precios y cantidades contra el servidor y mostrar cualquier cambio antes de continuar.
- **Catálogo público y panel comparten caché.** El listado administrativo guarda activos e inactivos en la misma caché que lee el catálogo público. Además, la consulta pública de imágenes no comprueba si el producto está activo. Separar ambos conjuntos de datos y limitar la galería pública a productos publicados.
- **Imágenes y PDF requieren un flujo de recuperación.** Borrar la imagen principal no promueve otra ni corrige la portada. Guardar el producto y subir el PDF son pasos separados, así que un fallo en la carga no significa que el producto no se haya guardado. La nueva UI debe indicarlo y permitir reintentar solo el archivo.
- **No copiar acoplamientos.** El diseño público, parte del cierre y el catálogo comparten HTML y scripts globales. La nueva referencia debe separar reglas, persistencia y presentación. No heredará nombre, teléfono, plantillas, categorías, datos ni campos hidráulicos de TACONI. Tampoco tomará la referencia horaria del mensaje como identificador único de una orden.

---

## Prompt para Codex o Cursor

### Rol y objetivo

Actúa como desarrollador full stack de INAUDIS. Construye o integra una web para **[NEGOCIO]** con catálogo autoadministrable, ficha de producto y **[MODO COMERCIAL]**. Conserva la lógica funcional de este instructivo, pero implementa un diseño propio para la marca.

Antes de editar, lee las instrucciones del repositorio y revisa sus rutas, stack, datos y componentes. Si ya existe un diseño aprobado, respétalo. Reutiliza lo que ya funciona, entrega primero un plan breve con archivos a tocar y no hagas refactors fuera del alcance. En un proyecto nuevo sin stack definido, propón PHP 8.3, MySQL 8 y JavaScript modular, con estilos independientes. No se exige Bootstrap ni una plantilla visual concreta.

### Datos que debes solicitar si faltan

Agrupa en una sola consulta lo que bloquee el trabajo. No pidas datos ya entregados ni inventes datos comerciales.

- Negocio, público y localidad. Logo, colores, referencias o lineamientos visuales.
- Modo **catálogo con consultas**, **carrito con WhatsApp** o **checkout con pago**. Solicita teléfono comercial para WhatsApp y proveedor para pago solo si aplican. Aclara si el negocio necesita historial interno.
- Categorías, marcas, campos técnicos del rubro y muestra de productos. Confirma si cada producto necesita adjuntar un PDF, varios o ninguno. Predeterminado del patrón: un PDF opcional.
- Moneda, precios con/sin IVA según lo que defina el proyecto, reglas de stock, política sin stock, retiro/despacho y qué se puede cotizar. Si el destino aún no está definido, no lo publiques ni lo simules como funcional.
- Funciones extra solo si corresponden: galería pública de fotos/videos, importación CSV/Excel, mantenedor de marcas/categorías, variantes y permisos adicionales.

### 1. Catálogo, ficha y medios

- Implementa un catálogo responsive alimentado por BD, sin editar código para publicar un producto. Incluye categorías, búsqueda por nombre/SKU/marca, filtros y orden que sirvan al rubro. Maneja carga, vacío, error y falta de resultados por separado.
- Da una URL estable a cada ficha, preferiblemente con slug. Obtén solo el producto pedido y sus relacionados relevantes. Incluye nombre, SKU, marca/categoría cuando corresponda, descripción, fotos, precios/consulta, disponibilidad y especificaciones del rubro. No fijes campos hidráulicos. Define título y descripción propios y evita indexar el panel.
- La ficha y, si el diseño lo requiere, la tarjeta deben ofrecer **ver PDF** y **descargar PDF** cuando exista. Al no haber PDF, no muestres botones rotos. Permite miniaturas y portada principal con texto alternativo. Si se elimina la portada, promociona otra o muestra un placeholder.

### 2. Carrito y cierre comercial

- **Consultas.** No agregues carrito ni checkout si solo se requiere catálogo. Configura un CTA general o por producto con nombre y SKU.
- **WhatsApp.** Permite agregar desde catálogo y ficha, agrupar cantidades por producto, quitar, vaciar y mostrar unidades, subtotales y total estimado. Persiste IDs y cantidades por navegador, sin datos personales. El acceso a «Mi pedido» debe funcionar desde cualquier ficha. La UI puede ser página, panel lateral o modal sin cambiar la lógica.
- Antes de continuar, revalida actividad, stock y precio vigente desde el servidor. Usa cantidades enteras positivas, bloquea o ajusta según la regla acordada e informa cualquier cambio para que se confirme el resumen. Permite consulta por producto sin stock solo si el negocio la habilita. No presentes un precio de caché como confirmado.
- Al continuar por WhatsApp, recoge los campos mínimos acordados. Muestra el resumen y arma el mensaje con ID de referencia, ítems, SKU, cantidades, precios, subtotales, total y condiciones de despacho. El número y la plantilla deben ser configurables y el texto debe viajar codificado. Etiqueta la acción «Abrir WhatsApp» o «Enviar por WhatsApp» con una aclaración de que el cliente debe confirmar el envío en la app. No limpies el carrito ni declares que el pedido se envió, pagó o reservó solo por abrir el chat. Si el negocio necesita seguimiento, define por separado el registro persistente y sus estados.
- **Checkout con pago.** Implementa solo si fue definido. Crea orden e ítems en BD, guarda importes calculados en servidor, define impuestos y despacho, y confirma pago solo desde el proveedor con notificación autenticada e idempotente. Modela expiración, cancelación y stock según regla aprobada. No marques pagado por la pantalla de retorno. Mantén esta modalidad separada de WhatsApp.

### 3. Panel administrativo

- Panel independiente con inicio/cierre de sesión y cambio de contraseña. Autorización en servidor para cada operación privada. Usa contraseñas con hash, sesiones seguras, protección CSRF en cambios, métodos HTTP adecuados y consultas parametrizadas. No dejes credenciales semilla reutilizables ni secretos en Git.
- Lista activos e inactivos con acciones de crear, editar, publicar/despublicar y, si se autoriza, eliminar. Prefiere archivar cuando existan dependencias. Valida en servidor SKU único, nombre, categoría/marca válidas cuando apliquen, precios y stock coherentes. Usa formularios para atributos del rubro, no JSON crudo para el cliente.
- Permite cargar fotos, seleccionar portada y cargar/reemplazar/eliminar el PDF sin editar rutas. Verifica MIME real, tamaño y formato, genera nombres seguros y sirve archivos desde una ubicación no ejecutable. Si reemplazas un archivo, conserva el anterior hasta confirmar el nuevo. Si falla una carga después de guardar el producto, indica el guardado parcial y permite reintentar la carga sin duplicar el producto.
- Si se confirma la galería general, separa su mantenedor del catálogo de productos. Incluye foto/video, título, descripción, orden y visibilidad. Si se confirma carga masiva, ofrece vista previa, validación por fila y confirmación antes de aplicar cambios.

### 4. Separación, entrega y verificación

- Mantén separados: **vistas y estilos de la marca**, **componentes públicos del catálogo**, **estado compartido del carrito**, **servicio de cotización/cierre**, **API y datos**, **administración** y **almacenamiento de archivos**. No uses datos administrativos como caché pública. Los datos públicos que provengan del panel deben renderizarse como texto seguro. Centraliza nombre comercial, moneda, teléfono, mensajes y modalidades. Cambiar diseño no debe cambiar APIs, tablas ni reglas comerciales.
- Modelo mínimo según alcance: usuarios administrativos, productos, categorías, marcas, imágenes y referencia a PDF. Agrega galería general u órdenes con ítems solo cuando corresponda. Entrega migraciones versionadas, configuración de ejemplo sin secretos, instrucciones de instalación y despliegue, y guía corta para el cliente. Al desplegar, preserva la BD, los archivos subidos y la configuración local.
- Incluye criterios de aceptación y qué falta validar. No ejecutes pruebas, publiques, fusiones ni despliegues sin autorización. No declares operativa una integración que no se verificó.

### Criterios mínimos para aprobar la entrega

1. Crear un producto en el panel, adjuntar fotos y PDF, publicarlo y encontrarlo con búsqueda y filtros. Su ficha muestra portada y ofrece ver/descargar el documento.
2. Editar precio y stock y comprobar que una selección anterior del carrito se revalida. Despublicar y comprobar que desaparece del catálogo, la ficha y los datos públicos de medios.
3. Agregar desde catálogo y ficha, recargar, modificar cantidades, quitar, vaciar y comprobar el estado sin stock y el carrito vacío. Probar navegación móvil y de escritorio.
4. En modalidad WhatsApp, el detalle y total del mensaje coinciden con el resumen revalidado. Abrir el chat no aparece como pedido enviado ni descontado. En modalidad pago, confirmar escenarios de pago aceptado, fallido y notificación duplicada. Marcar lo que no aplique.
5. Verificar autorización del panel, rechazo de archivos inválidos, comportamiento al fallar una carga y recuperación de portada al retirarla. Un cambio de tema visual no requiere cambios en la lógica del carrito ni en la persistencia.

Fin del prompt.
