<?php
    require_once 'database.inc.php' ; 
    if($_SERVER["REQUEST_METHOD"] == 'POST'){
        $Email=$_POST['Email'];
        $Password=$_POST['Pwd'];

        //Does the email exist ?
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ?");
        $stmt->execute([$Email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$user){
            die("Email non registred");
        }
        if($user['is_verified']!=1){
            die("Email non verified");
        }
         // Verify password
        if (password_verify($Password, $user['pwd'])) {
        // Password correct, start session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['FirstName'] . " " . $user['LastName'];
        header("Location: ../dashboard.php");
        exit(); }
        else {
        die("Incorrect password.");
    }
} else {
    header("Location: ../index.php"); // redirect if accessed directly
    exit();
}
?>