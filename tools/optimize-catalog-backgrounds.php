<?php
declare(strict_types=1);

foreach (glob(__DIR__ . '/../ferreteriaelvolcan/fondo-catalogo*.png') ?: [] as $sourcePath) {
    $source = imagecreatefrompng($sourcePath);
    if (!$source) continue;
    $width = imagesx($source);
    $height = imagesy($source);
    $ratio = min(1, 1920 / max(1, $width));
    $targetWidth = (int) round($width * $ratio);
    $targetHeight = (int) round($height * $ratio);
    $target = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
    $outputPath = preg_replace('/\.png$/i', '.webp', $sourcePath);
    imagewebp($target, $outputPath, 82);
    printf("%s: %dx%d -> %dx%d\n", basename($sourcePath), $width, $height, $targetWidth, $targetHeight);
    imagedestroy($target);
    imagedestroy($source);
}

$bannerPath = __DIR__ . '/../ferreteriaelvolcan/banner-despacho-gratis.png';
if (is_file($bannerPath)) {
    $source = imagecreatefrompng($bannerPath);
    if ($source) {
        $width = imagesx($source);
        $height = imagesy($source);
        $target = imagecreatetruecolor($width, $height);
        imagecopy($target, $source, 0, 0, 0, 0, $width, $height);
        imagewebp($target, __DIR__ . '/../ferreteriaelvolcan/banner-despacho-gratis.webp', 84);
        imagedestroy($target);
        imagedestroy($source);
        printf("Banner despacho: %dx%d -> WebP\n", $width, $height);
    }
}
