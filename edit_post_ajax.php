<?php
session_start();
require_once 'includes/database.inc.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? '';
    $content = $_POST['content'] ?? '';

    // Update post only if the user owns it
    $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $updated = $stmt->execute([$content, $post_id, $_SESSION['user_id']]);
    if ($updated) {
        echo json_encode(['success' => true, 'content' => htmlspecialchars($content)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
?>
