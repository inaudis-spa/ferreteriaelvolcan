<?php
declare(strict_types=1);
require_once __DIR__ . '/business-data.php';
function seo_escape(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function seo_head(array $data): void {
 $title=trim((string)($data['title']??BUSINESS_NAME)); $description=trim((string)($data['description']??'')); $url=BUSINESS_DOMAIN.(string)($data['path']??'/'); $image=(string)($data['image']??(BUSINESS_DOMAIN.'/ferreteriaelvolcan/logotipo-ferreteria-volcan.png'));
 echo '<title>'.seo_escape($title).'</title><meta name="description" content="'.seo_escape($description).'"><link rel="canonical" href="'.seo_escape($url).'"><meta property="og:type" content="'.seo_escape($data['type']??'website').'"><meta property="og:site_name" content="'.seo_escape(BUSINESS_NAME).'"><meta property="og:title" content="'.seo_escape($title).'"><meta property="og:description" content="'.seo_escape($description).'"><meta property="og:url" content="'.seo_escape($url).'"><meta property="og:image" content="'.seo_escape($image).'"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="'.seo_escape($title).'"><meta name="twitter:description" content="'.seo_escape($description).'">';
 if (!empty($data['noindex'])) echo '<meta name="robots" content="noindex,follow">';
}
function seo_business_schema(): array {
 return ['@context'=>'https://schema.org','@type'=>'HardwareStore','@id'=>BUSINESS_DOMAIN.'/#ferreteria','name'=>BUSINESS_NAME,'url'=>BUSINESS_DOMAIN,'telephone'=>BUSINESS_PHONE,'address'=>['@type'=>'PostalAddress','streetAddress'=>BUSINESS_ADDRESS,'addressLocality'=>BUSINESS_CITY,'addressRegion'=>BUSINESS_REGION,'addressCountry'=>'CL'],'openingHoursSpecification'=>[['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],'opens'=>'08:30','closes'=>'18:30']],'sameAs'=>[BUSINESS_FACEBOOK,BUSINESS_INSTAGRAM,BUSINESS_MAPS]];
}
function seo_jsonld(array $schema): void { echo '<script type="application/ld+json">'.json_encode($schema,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'; }
