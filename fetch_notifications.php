<?php
session_start();
require_once 'includes/database.inc.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Select all notifications for the user
    $sql = "SELECT id, message, created_at, seen FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the unread count
    $sql_unread = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND seen = 0";
    $stmt_unread = $pdo->prepare($sql_unread);
    $stmt_unread->execute([$user_id]);
    $unread_count = $stmt_unread->fetchColumn();

    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Database error.']);
}
?>