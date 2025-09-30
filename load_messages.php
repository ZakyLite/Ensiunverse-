<?php
session_start();
require_once 'includes/database.inc.php';

$user_id = $_SESSION['user_id'];

// Fetch all users you have messages with, along with the last message
$sql = "SELECT u.id AS user_id, u.FirstName, u.LastName, u.profile_picture,
               SUBSTRING_INDEX(GROUP_CONCAT(m.message ORDER BY m.created_at DESC), ',', 1) AS last_message
        FROM messages m
        JOIN users u ON (u.id = m.sender_id OR u.id = m.receiver_id)
        WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?
        GROUP BY u.id
        ORDER BY MAX(m.created_at) DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($conversations as $c) {
    $result[] = [
        'user_id' => $c['user_id'],
        'name' => $c['FirstName'] . ' ' . $c['LastName'],
        'profile_picture' => $c['profile_picture'],
        'last_message' => $c['last_message'],
        'initials' => strtoupper($c['FirstName'][0] ?? 'U'),
        'color' => '#' . substr(md5($c['user_id']), 0, 6)
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
?>
