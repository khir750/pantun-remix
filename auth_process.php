<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: index.php");
  exit;
}

$action = $_POST['action'] ?? '';

/* ===============================
   SIGNUP PROCESS
=============================== */
if ($action === "signup") {

  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  // Redirect target (if any)
  $redirect = $_POST['redirect'] ?? 'index.php';

  if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['signup_error'] = "All fields are required.";
    header("Location: signup.php");
    exit;
  }

  // Check existing email
  $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
    $_SESSION['signup_error'] = "Email already registered.";
    header("Location: signup.php");
    exit;
  }

  // Hash password
  $hashed = password_hash($password, PASSWORD_DEFAULT);

  // Insert user
  $stmt = $conn->prepare("
    INSERT INTO users (name, email, password, created_at)
    VALUES (?, ?, ?, NOW())
  ");
  $stmt->bind_param("sss", $name, $email, $hashed);
  $stmt->execute();

  // Set session
  $_SESSION['user_id']   = $conn->insert_id;
  $_SESSION['user_name'] = $name;

  /* ✅ SAFE REDIRECT */
  $allowed = ['send.php', 'browsepantun.php', 'index.php'];

  if (!in_array($redirect, $allowed)) {
    $redirect = 'index.php';
  }

  header("Location: $redirect");
  exit;
}


/* ===============================
   LOGIN PROCESS
=============================== */
if ($action === "login") {

  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  // Redirect target (if any)
  $redirect = $_POST['redirect'] ?? 'index.php';

  if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Email and password required.";
    header("Location: user_login.php");
    exit;
  }

  // Fetch user
  $stmt = $conn->prepare("
    SELECT user_id, name, password
    FROM users
    WHERE email = ?
    LIMIT 1
  ");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 0) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: user_login.php");
    exit;
  }

  $user = $res->fetch_assoc();

  if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: user_login.php");
    exit;
  }

  // Set session
  $_SESSION['user_id']   = $user['user_id'];
  $_SESSION['user_name'] = $user['name'];

  /* ✅ SAFE REDIRECT */
  $allowed = ['send.php', 'browsepantun.php', 'index.php'];

  if (!in_array($redirect, $allowed)) {
    $redirect = 'index.php';
  }

  header("Location: $redirect");
  exit;
}


/* ===============================
   FALLBACK
=============================== */
header("Location: index.php");
exit;
