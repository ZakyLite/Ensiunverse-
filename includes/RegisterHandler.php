<?php
session_start(); // to store error messages

// Use the reliable root path to autoload the PHPMailer library
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input and trim spaces
    $FirstName = trim($_POST['FirstName']); 
    $LastName = trim($_POST['LastName']); 
    $Email = trim($_POST['Email']);
    $Password = $_POST['Pwd']; 
    $ConfirmedPassword = $_POST['ConfirmPwd'];

    // Initialize an array to store errors
    $errors = [];

    // Check if passwords match
    if ($Password !== $ConfirmedPassword) {
        $errors[] = "Passwords do not match!";
    }

    // Validate email format
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    // Ensure email ends with @ensia.edu.dz
    if (!str_ends_with($Email, "@ensia.edu.dz")) {
        $errors[] = "Email must be an @ensia.edu.dz address!";
    }

    require_once __DIR__ . '/database.inc.php';
    // Check if email already exists
    $checkQuery = "SELECT * FROM users WHERE Email = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$Email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email already registered!";
    }

    // If there are errors, store them in session and redirect back to the form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Email' => $Email
        ];
        header("Location: ../index.php"); // or wherever your form is
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($Password , PASSWORD_DEFAULT);

    // Generate verification token
    $token = bin2hex(random_bytes(16));

    // Insert user as inactive
    $insertQuery = "INSERT INTO users (FirstName, LastName, Email, pwd, is_verified, verification_token) 
                    VALUES (?, ?, ?, ?, 0, ?)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute([$FirstName, $LastName, $Email, $hashedPassword, $token]);


    // Send verification email with PHPMailer
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
        $verificationLink = "https://ensiuniverse.lovestoblog.com/includes/verify.php?token=$token";
        $mail->isHTML(true);
        $mail->Subject = "Verify your ENSIA account";
        $mail->Body    = "Hello $FirstName,\n\nClick the link below to verify your account:\n$verificationLink\n\nThank you!";

        $mail->send();
        $_SESSION['success'] = "Registration successful! Check your email for a verification link.";
    } catch (Exception $e) {
        $_SESSION['errors'] = ["Failed to send verification email. Mailer Error: {$mail->ErrorInfo}"];
    }

    header("Location: ../index.php"); // redirect back to form
    exit();
}
?>