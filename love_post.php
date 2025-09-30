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
    $post_id = $_POST['post_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$post_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing post_id']);
        exit;
    }

    // Check if the user has already loved the post to toggle the action
    $stmt = $pdo->prepare("SELECT 1 FROM post_loves WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $alreadyLoved = $stmt->fetch();

    if (!$alreadyLoved) {
        // User has not loved it yet, so insert a new love and increment
        $stmt_insert = $pdo->prepare("INSERT INTO post_loves (post_id, user_id) VALUES (?, ?)");
        $stmt_insert->execute([$post_id, $user_id]);
        $pdo->prepare("UPDATE posts SET loves = loves + 1 WHERE id = ?")->execute([$post_id]);
        $action = 'loved';

        // Add notification logic (only if a new love was added)
        $stmt_owner = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt_owner->execute([$post_id]);
        $targetUserId = $stmt_owner->fetchColumn();

        if ($targetUserId && $targetUserId != $user_id) {
            // Get the name of the user who performed the action
            $stmt_sender_name = $pdo->prepare("SELECT FirstName, LastName FROM users WHERE id = ?");
            $stmt_sender_name->execute([$user_id]);
            $sender_name_data = $stmt_sender_name->fetch(PDO::FETCH_ASSOC);
            $sender_name = $sender_name_data ? trim($sender_name_data['FirstName'] . ' ' . $sender_name_data['LastName']) : 'A user';
            
            // Construct the message with the user's name
            $message = "{$sender_name} loved your post";
            
            $insert = $pdo->prepare(
                "INSERT INTO notifications (user_id, type, reference_id, message, created_at, seen)
                 VALUES (?, 'love', ?, ?, NOW(), 0)"
            );
            $insert->execute([$targetUserId, $user_id, $message]);
        }
    } else {
        // User has loved it, so delete the love and decrement
        $stmt_delete = $pdo->prepare("DELETE FROM post_loves WHERE post_id = ? AND user_id = ?");
        $stmt_delete->execute([$post_id, $user_id]);
        $pdo->prepare("UPDATE posts SET loves = loves - 1 WHERE id = ?")->execute([$post_id]);
        $action = 'unloved';
    }

    // Fetch the new love count
    $stmt_loves = $pdo->prepare("SELECT loves FROM posts WHERE id = ?");
    $stmt_loves->execute([$post_id]);
    $loves = $stmt_loves->fetchColumn();

    // Return a JSON success response with the updated count
    echo json_encode([
        'success' => true,
        'loves' => (int)$loves,
        'action' => $action
    ]);
    exit;
}
?>