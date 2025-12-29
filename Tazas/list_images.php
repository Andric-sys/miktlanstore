<?php
// Obtener la carpeta solicitada
$folder = isset($_GET['folder']) ? $_GET['folder'] : 'imagenes';
$current_dir = dirname(__FILE__);
$images_dir = $current_dir . '/' . $folder;

// Extensiones de imagen permitidas
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Verificar si la carpeta existe
if (is_dir($images_dir)) {
    // Obtener todos los archivos de la carpeta
    $files = scandir($images_dir);
    
    // Filtrar solo imágenes
    $images = array_filter($files, function($file) use ($allowed_extensions, $images_dir) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $full_path = $images_dir . '/' . $file;
        return in_array($extension, $allowed_extensions) && is_file($full_path);
    });
    
    // Ordenar alfabéticamente
    sort($images);
} else {
    $images = [];
}

// Devolver como JSON
header('Content-Type: application/json');
echo json_encode(array_values($images));
?>
