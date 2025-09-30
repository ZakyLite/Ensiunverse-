<?php
    session_start();
    require_once 'includes/database.inc.php' ; 
    if(!isset ($_SESSION['user_id'])){
        header("Location: index.php") ;
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $post_id = $_GET['id'];
     $stmt = $pdo->prepare("SELECT content, user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post=$stmt->fetch(PDO::FETCH_ASSOC);

     // Check ownership
    if (!$post || $post['user_id'] != $_SESSION['user_id']) {
        header("Location: dashboard.php");
        exit;
    }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit your post</title>
        <link rel="stylesheet" href="dashboard.css">
    </head>
    <body>
        <h2>Edit Your Post</h2>
    <form method="POST" action="edit_post.php?id=<?php echo $post_id; ?>">
    <!-- Hidden input to keep track of post ID -->
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <!-- Submit button -->
    <button type="submit">Update Post</button>
    </form>
    </body>
    </html>

