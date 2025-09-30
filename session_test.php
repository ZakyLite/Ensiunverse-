<?php
session_start();
echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "User ID in Session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "All Session Data:\n";
print_r($_SESSION);
echo "</pre>";