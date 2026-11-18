<?php
declare(strict_types=1);
require __DIR__ . '/../includes/seo.php';
$whatsapp = 'https://wa.me/' . BUSINESS_WHATSAPP . '?text=' . rawurlencode('Hola Ferretería El Volcán, quisiera cotizar materiales y consultar despacho en la zona lacustre.');
?>
<!doctype html>
<html lang="es-CL">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php seo_head(['title'=>'Despachos de ferretería en la zona lacustre | Ferretería El Volcán','description'=>'Consulta despacho de materiales y herramientas desde Ferretería El Volcán, ruta Villarrica–Licán Ray km 19, hacia Villarrica, Licán Ray, Pucón, Coñaripe y Panguipulli. Costo según distancia y pedido.','path'=>'/despachos-zona-lacustre/']); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../video.css">
  <?php seo_jsonld(seo_business_schema()); ?>
</head>
<body class="delivery-page">
  <div class="topbar">Cotiza rápido · Confirmación directa por WhatsApp</div>
  <header class="site-header">
    <a class="brand" href="../index.html" aria-label="Ferretería El Volcán, inicio"><img src="../ferreteriaelvolcan/logotipo-ferreteria-volcan.png" alt="" width="64" height="64"><span>Ferretería<br><strong>El Volcán</strong></span></a>
    <nav aria-label="Navegación principal"><a href="../index.html">Inicio</a><a href="../catalogo.html">Catálogo completo</a><a href="../ofertas.html">Ofertas</a><a class="active" href="./">Despachos</a><a href="../index.html#proyectos">Proyectos</a><a href="../index.html#como-comprar">Cómo cotizar</a></nav>
    <button class="mobile-menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
    <a class="cart-button delivery-header-cta" href="<?=$whatsapp?>" target="_blank" rel="noopener">Consultar <span>↗</span></a>
  </header>

  <main>
    <section class="delivery-hero">
      <div class="delivery-hero-copy"><p class="eyebrow">Cobertura de despacho</p><h1>Tu proyecto,<br><em>más cerca.</em></h1><p>Materiales y herramientas desde nuestro local en la ruta Villarrica–Licán Ray. Cotiza por WhatsApp y confirma cobertura, disponibilidad y costo.</p><div><a class="button primary" href="<?=$whatsapp?>" target="_blank" rel="noopener">Cotizar despacho <span>↗</span></a><a class="button ghost" href="https://maps.app.goo.gl/s8ZHTcvyiuRh65aT6?g_st=iw" target="_blank" rel="noopener">Retiro en tienda</a></div></div>
      <img src="../ferreteriaelvolcan/banner-despacho-gratis.webp" alt="Despacho gratis en compras sobre 100.000 pesos dentro de un radio de 7 kilómetros" width="2172" height="724">
    </section>

    <section class="delivery-content">
      <div class="delivery-intro"><div><p class="eyebrow dark">Zona lacustre</p><h2>Despachamos donde construyes</h2></div><p>Coordinamos solicitudes según distancia, volumen y características del pedido. La factibilidad y el valor se confirman antes del despacho.</p></div>
      <div class="delivery-zones" aria-label="Localidades de cobertura">
        <?php foreach (BUSINESS_COVERAGE as $index => $zone): ?><article><b><?=str_pad((string)($index+1),2,'0',STR_PAD_LEFT)?></b><span><?=seo_escape($zone)?></span></article><?php endforeach; ?>
      </div>
      <div class="delivery-methods">
        <article><span aria-hidden="true">▰</span><div><p class="eyebrow dark">Opción 01</p><h3>Despacho coordinado</h3><p>Indica productos, cantidades y localidad. Revisamos distancia, volumen y disponibilidad antes de confirmar.</p><a href="<?=$whatsapp?>" target="_blank" rel="noopener">Consultar por WhatsApp →</a></div></article>
        <article><span aria-hidden="true">⌂</span><div><p class="eyebrow dark">Opción 02</p><h3>Retiro en tienda</h3><p>Compra o cotiza y coordina el retiro en <?=seo_escape(BUSINESS_ADDRESS)?>, dentro del horario de atención.</p><a href="<?=BUSINESS_MAPS?>" target="_blank" rel="noopener">Ver cómo llegar →</a></div></article>
      </div>
    </section>

    <section class="delivery-cta"><div><p class="eyebrow">Cotiza directamente</p><h2>Cuéntanos qué necesitas y dónde.</h2><p>Te confirmaremos productos, disponibilidad, modalidad y condiciones antes de coordinar.</p></div><a class="button primary" href="<?=$whatsapp?>" target="_blank" rel="noopener">Abrir WhatsApp <span>↗</span></a></section>
  </main>

  <section class="footer-location" aria-labelledby="delivery-location-title"><div><p class="eyebrow">Retiro en tienda</p><h2 id="delivery-location-title">Estamos en la ruta Villarrica–Licán Ray</h2><p>Km 19 · <?=seo_escape(BUSINESS_HOURS)?></p><a href="<?=BUSINESS_MAPS?>" target="_blank" rel="noopener">Abrir ruta en Google Maps →</a></div><div class="footer-map-frame"><iframe src="https://www.google.com/maps?q=Ruta%20Villarrica-Lican%20Ray%20km%2019%2C%20Chile&output=embed" title="Mapa de Ferretería El Volcán" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><a href="<?=BUSINESS_MAPS?>" target="_blank" rel="noopener"><span>Cómo llegar ↗</span></a></div></section>
  <footer><div class="footer-brand"><img src="../ferreteriaelvolcan/logotipo-ferreteria-volcan.png" alt="Ferretería El Volcán" width="92" height="92"><p>Materiales para construir<br>lo que imaginas.</p></div><div><h3>Explora</h3><a href="../index.html">Inicio</a><a href="../catalogo.html">Catálogo</a><a href="../ofertas.html">Ofertas</a></div><div><h3>Contacto</h3><a href="tel:<?=BUSINESS_PHONE?>"><?=BUSINESS_PHONE?></a><a href="<?=$whatsapp?>" target="_blank" rel="noopener">WhatsApp</a><a href="<?=BUSINESS_MAPS?>" target="_blank" rel="noopener">Cómo llegar</a></div><p class="legal">© <?=date('Y')?> <?=seo_escape(BUSINESS_NAME)?></p></footer>
  <a class="floating-whatsapp" href="<?=$whatsapp?>" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 3a13 13 0 0 0-11.1 19.7L3.2 29l6.5-1.7A13 13 0 1 0 16 3Zm0 23.6c-2 0-3.9-.5-5.5-1.5l-.4-.2-3.9 1 1-3.8-.2-.4A10.6 10.6 0 1 1 16 26.6Zm5.8-8c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2l-1 1.2c-.2.2-.4.2-.7.1-1.9-.9-3.2-1.7-4.5-3.9-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.5c.2.2 2.4 3.7 5.9 5.2 2.2.9 3.1 1 4.2.8.7-.1 1.9-.8 2.2-1.5.3-.8.3-1.4.2-1.5-.2-.1-.5-.2-.8-.4Z"/></svg><span>¿Necesitas ayuda?</span></a>
  <script src="../map.js" defer></script>
  <script>const menuToggle=document.querySelector('.mobile-menu-toggle');if(menuToggle){menuToggle.addEventListener('click',()=>{const header=document.querySelector('.site-header'),open=header.classList.toggle('menu-open');menuToggle.setAttribute('aria-expanded',String(open));menuToggle.textContent=open?'×':'☰'});document.querySelectorAll('.site-header nav a').forEach(link=>link.addEventListener('click',()=>{document.querySelector('.site-header').classList.remove('menu-open');menuToggle.setAttribute('aria-expanded','false');menuToggle.textContent='☰'}))}</script>
</body>
</html>
