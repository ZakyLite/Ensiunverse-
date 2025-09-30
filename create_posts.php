<?php
session_start();
require_once 'includes/database.inc.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get current user info
    $userId = $_SESSION['user_id'];
    $author = $_SESSION['user_name'];

    // Get form data
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1';

    // Override author if anonymous
    if ($isAnonymous) {
        $author = "Anonymous User";
    }

    // Validate content
    if (!empty($content)) {
        // Insert post into database
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, author, content, category, is_anonymous, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $author, $content, $category, $isAnonymous ? 1 : 0]);
    }
}

header("Location: dashboard.php");
exit;
?>
