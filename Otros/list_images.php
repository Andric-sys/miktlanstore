<?php
header('Content-Type: application/json; charset=utf-8');

$folder = isset($_GET['folder']) ? trim($_GET['folder']) : 'imagenes';
$allowed_extensions = array('jpg', 'jpeg', 'png');
$current_dir = dirname(__FILE__);
$base_dir = $current_dir . DIRECTORY_SEPARATOR;
$requested_path = $base_dir . $folder;
$images_dir = realpath($requested_path);
$images = array();

if ($images_dir === false || strpos($images_dir, $base_dir) !== 0) {
    echo json_encode($images);
    exit;
}

try {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($images_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
            $ext = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions)) {
                $relative_path = str_replace('\\', '/', substr($fileinfo->getPathname(), strlen($images_dir) + 1));
                if ($relative_path === false) {
                    $relative_path = $fileinfo->getFilename();
                }
                $images[] = rtrim($folder, '/\\') . '/' . ltrim($relative_path, '/\\');
            }
        }
    }
} catch (Exception $e) {
    echo json_encode(array());
    exit;
}

sort($images, SORT_NATURAL | SORT_FLAG_CASE);
echo json_encode(array_values($images));
?>
