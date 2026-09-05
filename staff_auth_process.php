<?php
session_start();

/* =============================
   DATABASE CONNECTION (PORT 3308)
============================= */
$conn = new mysqli("sql309.infinityfree.com", "if0_42299665", "Pantun2026", "if0_42299665_pantun");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* =============================
   VALID REQUEST CHECK
============================= */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: staff_login.php");
    exit;
}

/* =============================
   INPUT VALIDATION
============================= */
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === "" || $password === "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: staff_login.php");
    exit;
}

/* =============================
   CHECK STAFF ACCOUNT
============================= */
$stmt = $conn->prepare("
    SELECT staff_id, name, password, role, status
    FROM staff
    WHERE email = ?
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: staff_login.php");
    exit;
}

$staff = $result->fetch_assoc();

/* =============================
   STATUS CHECK
============================= */
if ($staff['status'] !== 'active') {
    $_SESSION['error'] = "Account is inactive. Contact admin.";
    header("Location: staff_login.php");
    exit;
}

/* =============================
   PASSWORD CHECK (SHA-256)
============================= */
if (hash('sha256', $password) !== $staff['password']) {
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: staff_login.php");
    exit;
}

/* =============================
   LOGIN SUCCESS
============================= */
$_SESSION['staff_id']   = $staff['staff_id'];
$_SESSION['staff_name'] = $staff['name'];
$_SESSION['role']       = $staff['role'];

/* =============================
   ROLE REDIRECT
============================= */
if ($staff['role'] === 'manager') {
    header("Location: manager_dashboard.php");
    exit;
}

if ($staff['role'] === 'clerk') {
    header("Location: clerk_dashboard.php");
    exit;
}

/* =============================
   SAFETY FALLBACK
============================= */
session_destroy();
header("Location: staff_login.php");
exit;

