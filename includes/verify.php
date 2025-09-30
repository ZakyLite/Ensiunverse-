<?php
require_once 'database.inc.php';
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = "SELECT * FROM users WHERE verification_token = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$token]);
    if ($stmt->rowCount() == 1) {
        $update = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?";
        $stmt = $pdo->prepare($update);
        $stmt->execute([$token]);
        echo "Your account has been verified! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid or expired verification link.";
    }
} else {
    echo "No verification token provided.";
}
?>
