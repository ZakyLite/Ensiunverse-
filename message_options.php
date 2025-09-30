<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['user_id'] ?? 0;

// Get receiver info
$receiver = null;
if ($receiver_id > 0) {
    $stmt = $pdo->prepare("SELECT id, FirstName, LastName FROM users WHERE id = ?");
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$receiver) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Options - Ensiuniverse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .option-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .option-btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: #4a6baf;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }
        .option-btn:hover {
            background: #3a5a9f;
        }
        .option-btn.anonymous {
            background: #6c757d;
        }
        .option-btn.anonymous:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="option-container">
        <h2>Message <?= htmlspecialchars($receiver['FirstName'] . ' ' . $receiver['LastName']) ?></h2>
        <p>Choose how you want to message this user:</p>
        
        <a href="messages.php?user_id=<?= $receiver_id ?>&anonymous=0" class="option-btn">
            <i class="fas fa-user"></i> Reveal the identity <p style="font-size: 12px; color: lightgray;" >(if used , the identity will be revealed for both users)</p>
        </a>
        
        <a href="messages.php?user_id=<?= $receiver_id ?>&anonymous=1" class="option-btn anonymous">
            <i class="fas fa-mask"></i> Message anonymously
        </a>
    </div>
</body>
</html>