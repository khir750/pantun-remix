<?php
$host = "sql309.infinityfree.com";  // NOT localhost !
$user = "if0_42299665";
$pass = "your_infinityfree_login_password";
$db   = "if0_42299665_pantun";

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
