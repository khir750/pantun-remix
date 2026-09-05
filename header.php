<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
/* === GLOBAL HEADER STYLE (FOR ALL PAGES) === */
header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:12px 40px;
  border-bottom:1px solid #e5e0d8;
  background:#faf7f2;
}

header .logo {
  font-family:'Playfair Display',serif;
  font-size:22px;
  font-style:italic;
}

header nav a {
  margin-left:25px;
  text-decoration:none;
  color:#333;
  font-size:14px;
}

header nav a:hover {
  text-decoration:underline;
}

header .greeting {
  margin-left:20px;
  font-size:13px;
  color:#555;
}
</style>

<header>
  <div class="logo">
    <a href="index.php" style="text-decoration:none;color:inherit;">
      PantunLetter
    </a>
  </div>

  <nav>
    <a href="send.php">Send Pantun</a>
    <a href="browsepantun.php">Browse</a>

    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="user_profile.php">Profile</a>
      <span class="greeting">
        Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>
      </span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="user_login.php">Login</a>
      <a href="signup.php">Sign Up</a>
    <?php endif; ?>
  </nav>
</header>
