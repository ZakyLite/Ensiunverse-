<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$userId = $_SESSION['user_id'];

// Make sure these come from the request
$targetUserId = $_POST['targetUserId']; 
$postId = $_POST['postId'];
$message = "User {$_SESSION['user_id']} liked your post";
// Insert a new notification
$sql = "INSERT INTO notifications (user_id, type, reference_id, message, created_at, seen)
        VALUES (?, ?, ?, ?, NOW(), 0)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$targetUserId, 'like', $postId, $message]);


echo json_encode(['success' => true]);
