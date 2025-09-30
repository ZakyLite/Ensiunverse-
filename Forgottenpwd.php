<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="Forgottenpwd.css">
</head>
<body>
    <div class="forgot-container">
        <h1>Forgot Your Password?</h1>
        <p>Enter your ENSIA email to reset your password.</p>

        <!-- Display success/error messages -->
        <?php
        if(isset($_GET['error'])){
            echo '<p class="error">'.htmlspecialchars($_GET['error']).'</p>';
        }
        if(isset($_GET['success'])){
            echo '<p class="success">'.htmlspecialchars($_GET['success']).'</p>';
        }
        ?>

        <form method="post" action="includes/ForgottenpwdHandler.php">
            <div class="input-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="Email" placeholder="your @ensia.edu.dz" required>
            </div>
            <button type="submit" class="reset-btn">Send Reset Link</button>
            <p>(Check your spam section if necessary)</p>
        </form>

        <p><a href="index.php">Back to login</a></p>
    </div>
</body>
</html>
