<?php
// Production configuration (commented out)
// $host = "sql309.infinityfree.com";  // NOT localhost !
// $user = "if0_42299665";
// $pass = "your_infinityfree_login_password";
// $db   = "if0_42299665_pantun";

// Local development configuration (updated to InfinityFree credentials)
$host = "sql309.infinityfree.com";
$user = "if0_42299665";
$pass = "Pantun2026";
$db   = "if0_42299665_pantun";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
