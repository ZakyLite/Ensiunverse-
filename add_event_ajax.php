<?php
session_start();
require_once 'includes/database.inc.php';

$title = $_POST['title'] ?? '';
$date = $_POST['event_date'] ?? '';

if (!$title || !$date) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO events (title, event_date) VALUES (?, ?)");
if ($stmt->execute([$title, $date])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed']);
}
