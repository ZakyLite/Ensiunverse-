<?php
session_start();
require_once 'includes/database.inc.php';
header('Content-Type: application/json; charset=utf-8');

// Default response
$response = ['success' => false];

try {
    if (isset($_SESSION['user_id'], $_POST['comment_id'], $_POST['content'])) {
        $comment_id = (int) $_POST['comment_id'];
        $newContent = trim($_POST['content']);

        if ($comment_id > 0 && $newContent !== '') {
            // Verify ownership
            $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($comment && (int)$comment['user_id'] === (int)$_SESSION['user_id']) {
                $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
                if ($stmt->execute([$newContent, $comment_id])) {
                    $response['success'] = true;
                }
            }
        }
    }
} catch (Exception $e) {
    // Silent fail
}

echo json_encode($response);
exit;
