<?php
$host = "sql308.infinityfree.com";
$port = 3306; 
$dbname = "if0_39935832_ensia_connect";
$user = "if0_39935832";
$pass = "f8CV0PDkyvUSvP";

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // ✅ Set MySQL session time zone to Algeria (UTC+1)
    $pdo->exec("SET time_zone = '+01:00'");

    // ✅ Make sure PHP uses the same time zone
    date_default_timezone_set('Africa/Algiers');

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
