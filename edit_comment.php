<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$comment = null;
$comment_id = $_GET['id'] ?? null;

// --- Step 1: Load comment for editing ---
if ($_SERVER["REQUEST_METHOD"] === "GET" && $comment_id) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Only allow owner to edit
    if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
        header("Location: dashboard.php");
        exit;
    }
}

// --- Step 2: Handle form submission ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comment_id = $_POST['comment_id'];
    $content = trim($_POST['content']);

    // Check ownership again for safety
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $check = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($check && $check['user_id'] == $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
        $stmt->execute([$content, $comment_id]);
    }

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <h2>Edit Your Comment</h2>

    <?php if ($comment): ?>
        <form method="POST" action="edit_comment.php">
            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
            <textarea name="content" required><?php echo htmlspecialchars($comment['content']); ?></textarea>
            <button type="submit">Update Comment</button>
        </form>
    <?php else: ?>
        <p>Comment not found or you cannot edit it.</p>
    <?php endif; ?>
</body>
</html>
