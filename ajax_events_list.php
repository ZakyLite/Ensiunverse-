<?php
session_start();
require_once 'includes/database.inc.php';

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($events);
