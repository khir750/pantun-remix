<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit;
}

$userId   = $_SESSION['user_id'];
$pantunId = $_POST['pantun_id'] ?? 0;

if ($pantunId <= 0) {
    echo "error";
    exit;
}

$stmt = $conn->prepare("
    INSERT IGNORE INTO favourite_pantun (user_id, pantun_id, created_at)
    VALUES (?, ?, NOW())
");
$stmt->bind_param("ii", $userId, $pantunId);
$stmt->execute();

echo "success";
exit;
