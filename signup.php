<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up | PantunLetter</title>
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
  margin:50px auto;
  background:rgba(255,255,255,.85);
  border-radius:22px;
  padding:45px 40px;
  box-shadow:0 20px 45px rgba(0,0,0,.15);
  text-align:center;
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
  padding:14px;
  border-radius:12px;
  border:1px solid #ddd;
  font-family:'Poppins',sans-serif;
}

button{
  margin-top:30px;
  width:100%;
  padding:14px;
  border:none;
  border-radius:25px;
  background:#1f1f1f;
  color:#fff;
  font-size:14px;
  cursor:pointer;
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

.password-wrap {
  position: relative;
  display: flex;
  align-items: center;       /* real vertical centering */
  width: 100%;
}

.password-wrap input {
  width: 100%;
  height: 52px;              /* 🔥 CRITICAL */
  padding: 0 54px 0 16px;    /* vertical padding removed */
  border-radius: 12px;
  border: 1px solid #ddd;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  line-height: 52px;         /* 🔥 aligns emoji visually */
}

.toggle-password {
  position: absolute;
  right: 16px;
  height: 52px;
  display: flex;
  align-items: center;       /* emoji dead-center */
  background: none;
  border: none;
  cursor: pointer;
  font-size: 20px;
  user-select: none;
}


</style>
</head>

<script>
function togglePassword() {
  const pwd = document.getElementById("password");
  const icon = document.getElementById("toggleIcon");

  if (pwd.type === "password") {
    pwd.type = "text";
    icon.textContent = "😳"; // password shown
  } else {
    pwd.type = "password";
    icon.textContent = "😎"; // password hidden
  }
}
</script>


<body>

<div class="auth-card">
  <h1>Register <span class="gold">Account</span></h1>
  <div class="subtitle">Create account to save your Pantun</div>

  <form method="POST" action="auth_process.php">
    <input type="hidden" name="action" value="signup">

    <label>Name: </label>
    <input type="text" name="name" required>

    <label>Email: </label>
    <input type="email" name="email" required>
    
    <label>Password:</label>
<div class="password-wrap">
  <input type="password" name="password" id="password" required>
  <span class="toggle-password" id="toggleIcon" onclick="togglePassword()">😎</span>
</div>

    <button type="submit">Register Account</button>
  </form>

  <div class="link">
    Already have an account? <a href="user_login.php">Log In Here</a>
  </div>
</div>

</body>
</html>
