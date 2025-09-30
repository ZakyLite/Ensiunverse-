<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id      = $_SESSION['user_id'];
$receiver_id  = (int)($_POST['receiver_id'] ?? 0);
$is_anonymous = (int)($_POST['is_anonymous'] ?? 0);

if ($receiver_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid receiver']);
    exit;
}

if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    // Make sure the uploads directory exists
    $uploadDir  = 'uploads/messages/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Create a safe, unique filename
    $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $path = $uploadDir . $name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
        // Insert a new message record
        $stmt = $pdo->prepare(
            "INSERT INTO messages
                (sender_id, receiver_id, message_type, media_url, is_anonymous, created_at)
             VALUES
                (?, ?, 'image', ?, ?, NOW())"
        );
        $stmt->execute([$user_id, $receiver_id, $path, $is_anonymous]);

        echo json_encode([
            'success'    => true,
            'message_id' => $pdo->lastInsertId(),
            'url'        => $path,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Upload failed']);
