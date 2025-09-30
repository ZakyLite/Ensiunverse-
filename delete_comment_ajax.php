<?php
session_start();
require_once 'includes/database.inc.php';
header('Content-Type: application/json; charset=utf-8');

// Always prepare a default response
$response = ['success' => false];

try {
    if (isset($_SESSION['user_id'], $_POST['comment_id'])) {
        $comment_id = (int) $_POST['comment_id'];

        if ($comment_id > 0) {
            // Verify ownership
            $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($comment && (int)$comment['user_id'] === (int)$_SESSION['user_id']) {
                $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
                if ($stmt->execute([$comment_id])) {
                    $response['success'] = true;
                }
            }
        }
    }
} catch (Exception $e) {
    // Stay silent
}

echo json_encode($response);
exit;
