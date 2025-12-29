<?php
// Configurar encoding UTF-8
header('Content-Type: application/json; charset=utf-8');

// Obtener la carpeta solicitada
$folder = isset($_GET['folder']) ? trim($_GET['folder']) : 'imagenes';

// Limpiar el nombre de la carpeta para seguridad
$folder = str_replace(['../', '..\\', '\\', '/', '..'], '', $folder);

// Extensiones de imagen permitidas
$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

// Obtener la ruta completa
$current_dir = dirname(__FILE__);
$images_dir = $current_dir . DIRECTORY_SEPARATOR . $folder;

$images = array();

// Verificar si la carpeta existe
if (is_dir($images_dir)) {
    // Obtener todos los archivos de la carpeta
    $files = scandir($images_dir);
    
    if ($files !== false) {
        foreach ($files as $file) {
            // Saltar archivos especiales
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $full_path = $images_dir . DIRECTORY_SEPARATOR . $file;
            
            // Verificar si es un archivo
            if (!is_file($full_path)) {
                continue;
            }
            
            // Obtener la extensión del archivo
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            // Verificar si la extensión está permitida
            if (in_array($extension, $allowed_extensions)) {
                $images[] = $file;
            }
        }
    }
    
    // Ordenar alfabéticamente
    sort($images);
}

// Devolver como JSON
echo json_encode(array_values($images));
?>

