<?php
session_start();
require_once 'includes/database.inc.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'Not logged in']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$post_id = (int)($_POST['post_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$is_anonymous = (isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1') ? 1 : 0;

if ($post_id <= 0 || $comment === '') {
    echo json_encode(['success'=>false,'message'=>'Missing data']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare(
        "INSERT INTO comments (post_id, user_id, content, is_anonymous, created_at)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$post_id, $user_id, $comment, $is_anonymous]);
    $comment_id = (int)$pdo->lastInsertId();

    if ($is_anonymous) {
        $author = 'Anonymous User';
    } else {
        $stmtU = $pdo->prepare("SELECT FirstName, LastName FROM users WHERE id = ?");
        $stmtU->execute([$user_id]);
        $u = $stmtU->fetch(PDO::FETCH_ASSOC);
        $author = trim(($u['FirstName'] ?? '') . ' ' . ($u['LastName'] ?? ''));
    }

    echo json_encode([
        'success'        => true,
        'comment_id'     => $comment_id,
        'comment_text'   => $comment,
        'comment_author' => $author,
        'comment_user_id'=> $user_id , // Always return the user_id, even for anonymous comments
        'is_anonymous' => $is_anonymous
    ]);
    exit;
} catch (Exception $ex) {
    error_log("add_comment_ajax error: " . $ex->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}