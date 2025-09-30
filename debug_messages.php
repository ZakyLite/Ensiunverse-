<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in first");
}

$currentUserId = $_SESSION['user_id'];

echo "<h2>Debug: Messages for user ID $currentUserId</h2>";

// Check if messages table exists
$tableCheck = $pdo->query("SHOW TABLES LIKE 'messages'");
if ($tableCheck->rowCount() === 0) {
    die("Messages table doesn't exist!");
}

// Show all messages
$allMessages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>All messages in database:</h3>";
echo "<pre>" . print_r($allMessages, true) . "</pre>";

// Show messages involving current user
$userMessages = $pdo->prepare("
    SELECT * FROM messages 
    WHERE sender_id = ? OR receiver_id = ? 
    ORDER BY created_at DESC
");
$userMessages->execute([$currentUserId, $currentUserId]);
$userMessagesData = $userMessages->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Messages involving current user:</h3>";
echo "<pre>" . print_r($userMessagesData, true) . "</pre>";

// Test the conversation query
$testQuery = $pdo->prepare("
    SELECT 
        u.id AS user_id,
        u.FirstName,
        u.LastName,
        u.profile_picture,
        m.content AS last_message,
        m.created_at AS last_message_time,
        (SELECT COUNT(*) FROM messages m2 
         WHERE m2.sender_id = u.id AND m2.receiver_id = ? AND m2.is_read = 0) AS unread_count
    FROM users u
    INNER JOIN messages m ON (
        (m.sender_id = u.id AND m.receiver_id = ?) OR 
        (m.receiver_id = u.id AND m.sender_id = ?)
    )
    WHERE u.id != ?
    GROUP BY u.id
    ORDER BY m.created_at DESC
");

$testQuery->execute([$currentUserId, $currentUserId, $currentUserId, $currentUserId]);
$testResults = $testQuery->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Test query results:</h3>";
echo "<pre>" . print_r($testResults, true) . "</pre>";