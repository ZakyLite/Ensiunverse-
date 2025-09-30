<?php
session_start();
require_once 'includes/database.inc.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$postId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing post_id']);
        exit;
    }

    // 1. Increment likes count directly without checking for a previous like
    $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?")->execute([$postId]);

    // 2. Fetch and return the new count
    $stmt_count = $pdo->prepare("SELECT likes FROM posts WHERE id = ?");
    $stmt_count->execute([$postId]);
    $newCount = $stmt_count->fetchColumn();

    // 3. Add notification logic (if the user is not supporting their own post)
    $stmt_owner = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt_owner->execute([$postId]);
    $postOwnerId = $stmt_owner->fetchColumn();

    if ($postOwnerId && $postOwnerId != $userId) {
        // Get the name of the user who performed the action
        $stmt_sender_name = $pdo->prepare("SELECT FirstName, LastName FROM users WHERE id = ?");
        $stmt_sender_name->execute([$userId]);
        $sender_name_data = $stmt_sender_name->fetch(PDO::FETCH_ASSOC);
        $sender_name = $sender_name_data ? trim($sender_name_data['FirstName'] . ' ' . $sender_name_data['LastName']) : 'A user';
        
        // Construct the message with the user's name
        $message = "{$sender_name} supported your post";
        
        $insert = $pdo->prepare("INSERT INTO notifications (user_id, type, reference_id, message, created_at, seen) VALUES (?, 'support', ?, ?, NOW(), 0)");
        $insert->execute([$postOwnerId, $userId, $message]);
    }

    echo json_encode(['success' => true, 'likes' => (int)$newCount, 'action' => 'liked']);
    exit;
}
?>