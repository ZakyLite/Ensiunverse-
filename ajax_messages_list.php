<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$conversations = [];

try {
    // Check if messages table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'messages'");
    
    if ($tableCheck->rowCount() > 0) {
        // Get conversations with the latest message - Handle anonymous messages
       $sql = "
SELECT 
    u.id AS user_id,
    u.FirstName,
    u.LastName,
    u.profile_picture,
    m.message AS last_message,
    m.created_at AS last_message_time,
    m.is_anonymous,
    (SELECT COUNT(*) FROM messages m2 
     WHERE m2.sender_id = u.id AND m2.receiver_id = ? AND m2.is_read = 0) AS unread_count
FROM users u
INNER JOIN messages m ON m.id = (
    SELECT m3.id 
    FROM messages m3
    WHERE (m3.sender_id = u.id AND m3.receiver_id = ?) 
       OR (m3.receiver_id = u.id AND m3.sender_id = ?)
    ORDER BY m3.created_at DESC
    LIMIT 1
)
WHERE u.id != ?
ORDER BY m.created_at DESC
";


        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$currentUserId, $currentUserId, $currentUserId, $currentUserId]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process the results to handle anonymity
        foreach ($conversations as &$conv) {
    // If the last message was anonymous AND the sender is not the current user
    if ($conv['is_anonymous'] == 1 && $conv['user_id'] != $currentUserId) {
        $conv['FirstName'] = 'Anonymous';
        $conv['LastName'] = 'User';
        $conv['profile_picture'] = null; // No profile picture for anonymous users
    }
}
    }
} catch (PDOException $e) {
    error_log("Error fetching conversations: " . $e->getMessage());
    $conversations = [];
}

header('Content-Type: application/json');
echo json_encode($conversations);