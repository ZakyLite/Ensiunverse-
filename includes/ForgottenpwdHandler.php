<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'database.inc.php'; // Database connection
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Email = $_POST['Email'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->execute([$Email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: ../Forgottenpwd.php?error=Email not registered");
        exit();
    }

    if ($user['is_verified'] != 1) {
        header("Location: ../Forgottenpwd.php?error=Email not verified");
        exit();
    }

    // Generate reset token and expiry (1 hour)
    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save token and expiry in database
    $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE Email = ?");
    $update->execute([$token, $expires, $Email]);

    // Send reset email with PHPMailer
    // Change the URL to your live domain
    $resetLink = "https://ensiuniverse.lovestoblog.com/reset_password.php?token=$token";
    $subject = "Reset Your ENSIA Password";
    $body = "Hello " . $user['FirstName'] . ",<br><br>Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a><br><br>This link will expire in 1 hour.";

    $mail = new PHPMailer(true);
    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ensiuniverse@gmail.com';
        $mail->Password   = 'gbomzslniokcymqa';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('ensiuniverse@gmail.com', 'Ensiuniverse');
        $mail->addAddress($Email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        header("Location: ../index.php?success=Password reset link sent to your email!");
        exit();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        header("Location: ../Forgottenpwd.php?error=Failed to send email. Please try again later.");
        exit();
    }
}
?>