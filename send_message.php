<?php
session_start();
require_once 'includes/database.inc.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'] ?? 0;
    $message = trim($_POST['message'] ?? '');
    $is_anonymous = $_POST['is_anonymous'] ?? 0; // GET THE ANONYMOUS FLAG
    
    // Prevent sending messages to yourself
    if ($sender_id == $receiver_id) {
        $response['error'] = 'Cannot send messages to yourself';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($message) && $receiver_id > 0) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO messages (sender_id, receiver_id, message, message_type, is_anonymous, created_at)
                VALUES (?, ?, ?, 'text', ?, NOW())
            ");
            
            // Include is_anonymous in execute()
            if ($stmt->execute([$sender_id, $receiver_id, $message, $is_anonymous])) {
                $response['success'] = true;
                $response['message_id'] = $pdo->lastInsertId();
                $response['created_at'] = date('H:i');
            } else {
                $error_info = $stmt->errorInfo();
                $response['error'] = 'Database error: ' . $error_info[2];
            }
        } catch (PDOException $e) {
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Invalid message or recipient';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);