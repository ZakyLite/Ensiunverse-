<?php
session_start();
require_once 'includes/database.inc.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "UPDATE notifications SET seen = 1 WHERE user_id = ? AND seen = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>