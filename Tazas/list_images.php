<?php
// Configurar encoding UTF-8
header('Content-Type: application/json; charset=utf-8');

// Obtener la carpeta solicitada (por defecto 'imagenes')
$folder = isset($_GET['folder']) ? trim($_GET['folder']) : 'imagenes';

// Extensiones de imagen permitidas (solo jpg/jpeg/png según petición)
$allowed_extensions = array('jpg', 'jpeg', 'png');

$current_dir = dirname(__FILE__);
$base_dir = $current_dir . DIRECTORY_SEPARATOR;

// Resolver ruta y prevenir traversal
$requested_path = $base_dir . $folder;
$images_dir = realpath($requested_path);

$images = array();

if ($images_dir === false || strpos($images_dir, $base_dir) !== 0) {
    // Ruta inválida o intento de traversal -> devolver array vacío
    echo json_encode($images);
    exit;
}

// Escaneo recursivo para archivos con extensiones permitidas
try {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($images_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
            $ext = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions)) {
                // Ruta relativa dentro de la carpeta solicitada
                $relative_path = str_replace('\\', '/', substr($fileinfo->getPathname(), strlen($images_dir) + 1));
                // Normalizar para archivos en la raíz (substr puede devolver false si coincide exactamente)
                if ($relative_path === false) {
                    $relative_path = $fileinfo->getFilename();
                }
                // Devolver con el prefijo de la carpeta solicitada para uso directo en HTML
                $images[] = rtrim($folder, '/\\') . '/' . ltrim($relative_path, '/\\');
            }
        }
    }
} catch (Exception $e) {
    // En caso de error, devolver lista vacía
    echo json_encode(array());
    exit;
}

// Ordenar y devolver JSON
sort($images, SORT_NATURAL | SORT_FLAG_CASE);
echo json_encode(array_values($images));
?>

