<?php
// image_cache.php
// Genera y cachea miniaturas WebP de forma dinámica para optimizar el rendimiento.

define('SOURCE_DIR', __DIR__ . '/../img/productos/');
define('CACHE_DIR', __DIR__ . '/../uploads/cache/');

// Crear directorio de caché si no existe
if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0777, true);
}

$img = isset($_GET['img']) ? basename($_GET['img']) : '';
$width = isset($_GET['w']) ? (int)$_GET['w'] : 300;

// Validar que la anchura esté en rangos permitidos para evitar ataques de DOS
if ($width < 50 || $width > 1200) {
    $width = 300;
}

$source_file = SOURCE_DIR . $img;

// Fallback case-insensitive: probar variantes de extensión explícitamente (Linux es case-sensitive)
if (!file_exists($source_file) || is_dir($source_file)) {
    $name_no_ext = pathinfo($img, PATHINFO_FILENAME);
    $variants = [
        $name_no_ext . '.jpg',
        $name_no_ext . '.JPG',
        $name_no_ext . '.jpeg',
        $name_no_ext . '.JPEG',
        $name_no_ext . '.png',
        $name_no_ext . '.PNG',
        $name_no_ext . '.webp',
        $name_no_ext . '.WEBP',
    ];
    foreach ($variants as $variant) {
        $try = SOURCE_DIR . $variant;
        if (file_exists($try) && !is_dir($try)) {
            $source_file = $try;
            $img = $variant;
            break;
        }
    }
}

// Placeholder SVG en caso de que no exista la imagen o esté PENDIENTE
$placeholder_svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="' . $width . '" height="' . $width . '" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="100" height="100" fill="#f1f5f9"/>
  <path d="M50 30V70M30 50H70" stroke="#cbd5e1" stroke-width="6" stroke-linecap="round"/>
</svg>';

if (empty($img) || $img === 'PENDIENTE' || !file_exists($source_file) || is_dir($source_file)) {
    header('Content-Type: image/svg+xml');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    echo $placeholder_svg;
    exit;
}

$cache_file = CACHE_DIR . $width . '_' . pathinfo($img, PATHINFO_FILENAME) . '.webp';

// Si existe en caché y es más nuevo que el archivo origen, servirlo directamente
if (file_exists($cache_file) && filemtime($cache_file) >= filemtime($source_file)) {
    header('Content-Type: image/webp');
    header('Cache-Control: public, max-age=31536000');
    readfile($cache_file);
    exit;
}

// Intentar generar la miniatura optimizada WebP
try {
    $info = getimagesize($source_file);
    if (!$info) {
        throw new Exception("No es una imagen válida");
    }
    
    list($orig_w, $orig_h, $type) = $info;
    
    // Calcular altura proporcional
    $ratio = $orig_h / $orig_w;
    $height = round($width * $ratio);
    
    // Cargar imagen según tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($source_file);
            break;
        case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($source_file);
            break;
        case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($source_file);
            break;
        case IMAGETYPE_WEBP:
            $src_img = imagecreatefromwebp($source_file);
            break;
        default:
            throw new Exception("Formato no soportado");
    }
    
    if (!$src_img) {
        throw new Exception("Error al cargar imagen");
    }
    
    // Crear lienzo de destino
    $dst_img = imagecreatetruecolor($width, $height);
    
    // Preservar transparencias para PNG/WebP
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP || $type == IMAGETYPE_GIF) {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
        $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
        imagefilledrectangle($dst_img, 0, 0, $width, $height, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, $orig_w, $orig_h);
    
    // Guardar como WebP optimizada (calidad 80)
    imagewebp($dst_img, $cache_file, 80);
    
    // Liberar memoria
    imagedestroy($src_img);
    imagedestroy($dst_img);
    
    // Servir la imagen generada
    header('Content-Type: image/webp');
    header('Cache-Control: public, max-age=31536000');
    readfile($cache_file);
    exit;
    
} catch (Exception $e) {
    // Fallback a la imagen original si falla
    $mime = mime_content_type($source_file);
    header('Content-Type: ' . $mime);
    readfile($source_file);
}
