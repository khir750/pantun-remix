<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    
    if (!empty($email) && !empty($new_password)) {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hash, $email);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['pw_success'] = "✅ Password for $email updated successfully!";
        } else {
            $_SESSION['pw_error'] = "❌ User not found or password unchanged.";
        }
        header("Location: update_user_password.php");
        exit;
    } else {
        $_SESSION['pw_error'] = "❌ Please fill in all fields.";
        header("Location: update_user_password.php");
        exit;
    }
}

$success = $_SESSION['pw_success'] ?? '';
$error = $_SESSION['pw_error'] ?? '';
unset($_SESSION['pw_success'], $_SESSION['pw_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}

body{
  font-family:'Poppins',sans-serif;
  background:#faf7f2;
  background-image:url("assets/bgfloral.jpeg");
  background-size:420px auto;
  color:#2e2e2e;
}

/* CARD */
.auth-card{
  max-width:420px;
  margin:100px auto;
  background:rgba(255,255,255,.88);
  border-radius:22px;
  padding:45px 40px;
  box-shadow:0 20px 45px rgba(0,0,0,.15);
  text-align:center;
}

h1{
  font-family:'Playfair Display',serif;
  font-size:32px;
  font-style:italic;
  margin-bottom:10px;
}

.subtitle{
  font-size:14px;
  color:#666;
  margin-bottom:30px;
}

.gold{color:#c9a24d}

label{
  display:block;
  text-align:left;
  font-size:13px;
  margin-bottom:6px;
  margin-top:18px;
}

input{
  width:100%;
  height:52px;
  padding:0 16px;
  border-radius:12px;
  border:1px solid #ddd;
  font-family:'Poppins',sans-serif;
  font-size:14px;
}

button{
  margin-top:30px;
  width:100%;
  height:52px;
  border:none;
  border-radius:25px;
  background:#1f1f1f;
  color:#fff;
  font-size:14px;
  cursor:pointer;
  transition: background 0.2s;
}

button:hover{
  background:#333;
}

.link{
  margin-top:20px;
  font-size:13px;
}

.link a{
  color:#333;
  text-decoration:none;
}
.link a:hover{text-decoration:underline}

.error{
  background:#ffeaea;
  color:#a94442;
  padding:10px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:15px;
}

.success{
  background:#eafaf1;
  color:#2e7d32;
  padding:10px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:15px;
}
</style>
</head>
<body>

<div class="auth-card">

  <h1>Reset <span class="gold">Password</span></h1>
  <div class="subtitle">Change any user's password securely</div>

  <?php if($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>User Email</label>
    <input type="email" name="email" required placeholder="user@example.com">

    <label>New Password</label>
    <input type="password" name="new_password" required placeholder="Enter new password">

    <button type="submit">Reset Password</button>
  </form>

  <div class="link">
    Remembered your password? <a href="user_login.php">Log in here</a>
  </div>

</div>

</body>
</html>
