<?php
require_once 'includes/database.inc.php';
session_start();

// Check if token exists
if(!isset($_GET['token'])) {
    die("No reset token provided.");
}
$token = $_GET['token'];

// Check token validity
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    die("Invalid or expired reset link.");
}

$resetSuccess = false;

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPwd = $_POST['newPwd'];
    $confirmPwd = $_POST['confirmPwd'];

    if($newPwd !== $confirmPwd) {
        die("Passwords do not match.");
    }

    $hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);

    // Update password and clear token
    $update = $pdo->prepare("UPDATE users SET pwd = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $update->execute([$hashedPwd, $user['id']]);

    $resetSuccess = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="Forgottenpwd.css">
</head>
<body>
    <div class="forgot-container">
        <h1>Reset Your Password</h1>
        <form method="post">
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="newPwd" placeholder="New password" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="confirmPwd" placeholder="Confirm password" required>
            </div>
            <button type="submit" class="reset-btn">Reset Password</button>
        </form>
    </div>

    <!-- Success Popup -->
    <div id="successPopup" class="popup" style="display:none;">
        <div class="popup-content">
            <h2>Password Reset Successfully!</h2>
            <p>You can now <a href="index.php">login</a> with your new password.</p>
            <button id="closePopup">Close</button>
        </div>
    </div>

    <style>
    .popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popup-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .popup-content h2 {
        margin-bottom: 15px;
    }

    .popup-content button {
        padding: 10px 20px;
        margin-top: 10px;
        cursor: pointer;
        border: none;
        border-radius: 8px;
         background:linear-gradient(135deg, #1e3a8a 0%, #4338ca 50%, #6d28d9 100%);
        color: white;
        font-size: 16px;
    }
    .popup-content button:hover{
        filter:saturate(3);
        transition: 0.3 ease-in-out;
    }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const success = <?php echo json_encode($resetSuccess); ?>;
            if (success) {
                document.getElementById("successPopup").style.display = "flex";

                // Auto redirect after 5 seconds
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 5000);

                // Close popup manually
                document.getElementById("closePopup").addEventListener("click", () => {
                    document.getElementById("successPopup").style.display = "none";
                    window.location.href = "index.php";
                });
            }
        });
    </script>
</body>
</html>
