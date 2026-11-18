<?php
declare(strict_types=1);
require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/includes/business-data.php';
header('Content-Type: application/xml; charset=utf-8');
$urls=[['loc'=>BUSINESS_DOMAIN.'/','change'=>'weekly'],['loc'=>BUSINESS_DOMAIN.'/catalogo.html','change'=>'weekly'],['loc'=>BUSINESS_DOMAIN.'/ofertas.html','change'=>'weekly']];
$urls[]=['loc'=>BUSINESS_DOMAIN.'/despachos-zona-lacustre/','change'=>'monthly'];
try{$rows=db()->query('SELECT slug,updated_at FROM products WHERE active=1')->fetchAll();foreach($rows as $r)$urls[]=['loc'=>BUSINESS_DOMAIN.'/producto/'.rawurlencode($r['slug']).'/','change'=>'weekly','date'=>substr((string)$r['updated_at'],0,10)];$rows=db()->query('SELECT slug,updated_at FROM categories WHERE active=1')->fetchAll();foreach($rows as $r)$urls[]=['loc'=>BUSINESS_DOMAIN.'/categoria/'.rawurlencode($r['slug']).'/','change'=>'weekly','date'=>substr((string)$r['updated_at'],0,10)];}catch(Throwable $e){}
echo '<?xml version="1.0" encoding="UTF-8"?>';echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';foreach($urls as $u){echo '<url><loc>'.htmlspecialchars($u['loc'],ENT_XML1,'UTF-8').'</loc><changefreq>'.$u['change'].'</changefreq>';if(!empty($u['date']))echo '<lastmod>'.$u['date'].'</lastmod>';echo '</url>';}echo '</urlset>';
