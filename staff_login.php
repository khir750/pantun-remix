<?php
session_start();

/* =============================
   AUTO REDIRECT IF ALREADY LOGGED IN
============================= */
if (isset($_SESSION['staff_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'manager') {
        header("Location: manager_dashboard.php");
        exit;
    }

    if ($_SESSION['role'] === 'clerk') {
        header("Location: clerk_dashboard.php");
        exit;
    }

    header("Location: staff_logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Login | PantunLetter</title>
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
  margin:110px auto;
  background:rgba(255,255,255,.88);
  border-radius:22px;
  padding:45px 40px;
  box-shadow:0 20px 45px rgba(0,0,0,.15);
  text-align:center;
}

/* LOGO */
.logo-wrap{
  margin-bottom:18px;
}

.logo-wrap img{
  max-width:140px;
  width:100%;
  height:auto;
}

/* TITLE */
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

/* FORM */
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

/* PASSWORD FIELD */
.password-wrap{
  position:relative;
  width:100%;
}

.password-wrap input{
  padding-right:52px;
}

.toggle-password{
  position:absolute;
  right:16px;
  top:50%;
  transform:translateY(-50%);
  font-size:20px;
  cursor:pointer;
  user-select:none;
}

/* BUTTON */
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
}

button:hover{opacity:.9}

/* ERROR */
.error{
  background:#ffeaea;
  color:#a94442;
  padding:10px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:15px;
}
</style>
</head>

<body>

<div class="auth-card">

  <!-- LOGO -->
  <div class="logo-wrap">
    <img src="assets/logo_pantun.png" alt="PantunLetter Logo">
  </div>

  <h1>Staff <span class="gold">Login</span></h1>
  <div class="subtitle">Access manager & clerk dashboard</div>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="error">
      <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="staff_auth_process.php">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <div class="password-wrap">
      <input type="password" name="password" id="password" required>
      <span class="toggle-password" id="toggleIcon" onclick="togglePassword()">😎</span>
    </div>

    <button type="submit">Login</button>
  </form>

</div>

<script>
function togglePassword(){
  const pwd = document.getElementById("password");
  const icon = document.getElementById("toggleIcon");

  if(pwd.type === "password"){
    pwd.type = "text";
    icon.textContent = "😳";
  }else{
    pwd.type = "password";
    icon.textContent = "😎";
  }
}
</script>

</body>
</html>
