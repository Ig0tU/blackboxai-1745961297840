<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Voila\Mappers\Dictionary;

header('Content-Type: application/octet-stream');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    http_response_code(400);
    echo 'No file uploaded';
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo 'File upload error';
    exit;
}

$content = file_get_contents($file['tmp_name']);
if ($content === false) {
    http_response_code(500);
    echo 'Failed to read uploaded file';
    exit;
}

$dictionary = new Dictionary();

// Simple example translation: replace element names in content based on dictionary
$elements = $dictionary->getAllElements(); // We need to add this method to Dictionary

foreach ($elements as $element) {
    $name = $element['name'] ?? '';
    $title = $element['title'] ?? '';
    if ($name && $title) {
        // Replace occurrences of name with title as an example
        $content = str_replace($name, $title, $content);
    }
}

$filename = 'translated_' . basename($file['name']);
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $content;
exit;
