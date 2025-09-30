<?php
session_start();
require_once 'includes/database.inc.php';

$event_id = $_POST['event_id'] ?? 0;
if (!$event_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
if ($stmt->execute([$event_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}
