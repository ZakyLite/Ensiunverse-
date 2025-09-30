<?php
session_start();
require_once 'includes/database.inc.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? 0;
$last_id = $_GET['last_id'] ?? 0;

try {
    // Only fetch messages newer than the last known message
    $stmt = $pdo->prepare("
        SELECT m.*,
               CONCAT(u.FirstName,' ',u.LastName) AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE ((sender_id = :me AND receiver_id = :them)
           OR (sender_id = :them AND receiver_id = :me))
           AND m.id > :last_id
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([
        'me' => $user_id, 
        'them' => $receiver_id,
        'last_id' => $last_id
    ]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($messages);
} catch (PDOException $e) {
    // Return empty array on error
    echo json_encode([]);
}