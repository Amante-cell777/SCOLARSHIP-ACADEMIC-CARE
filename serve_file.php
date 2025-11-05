<?php
if(!isset($_SESSION['user_id'])) exit;
$f = $_GET['f'] ?? '';
$path = 'uploads/' . basename($f);
if(is_file($path) && preg_match('/^[a-z0-9]+\.(pdf|jpe?g|png)$/i',$f)){
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($f).'"');
    readfile($path);
    exit;
}
http_response_code(404);
echo "File not found.";
?>

<?php
session_start();
if (empty($_SESSION['is_admin']) && empty($_SESSION['user_id'])) {
    die("Access denied.");
}

$file = $_GET['f'] ?? '';
if (!$file || !file_exists($file)) {
    die("File not found.");
}

header('Content-Type: ' . mime_content_type($file));
header('Content-Disposition: inline; filename="' . basename($file) . '"');
readfile($file);
exit;