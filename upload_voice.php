<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

if (!isset($_FILES['voice']) || $_FILES['voice']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('No voice file uploaded');
}

// Only accept webm or mp3 for this example
$allowed = ['webm','mp3','wav','ogg'];
$ext = strtolower(pathinfo($_FILES['voice']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    http_response_code(400);
    exit('Invalid audio type');
}

$filename = uniqid('voice_', true) . '.' . $ext;
$destDir = __DIR__ . '/uploads/messages/voice/';   // voice subfolder
if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

$dest = $destDir . $filename;

if (!move_uploaded_file($_FILES['voice']['tmp_name'], $dest)) {
    http_response_code(500);
    exit('Upload failed');
}

echo json_encode([
    'success' => true,
    'url' => 'uploads/messages/voice/' . $filename
]);
