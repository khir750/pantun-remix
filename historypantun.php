<?php
session_start();
include "db.php";

$res = $conn->query("
  SELECT share_token, created_at
  FROM sent_pantun
  ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>History | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
* { box-sizing:border-box; margin:0; padding:0; }

body {
  font-family:'Poppins',sans-serif;
  background-color:#faf7f2;
  background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d6c9b2' fill-opacity='0.15'%3E%3Cpath d='M40 0c22.091 0 40 17.909 40 40S62.091 80 40 80 0 62.091 0 40 17.909 0 40 0zm0 6C21.222 6 6 21.222 6 40s15.222 34 34 34 34-15.222 34-34S58.778 6 40 6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  color:#2e2e2e;
}

/* HEADER */
header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 60px;
  border-bottom:1px solid #ffffffff;
  background:#faf7f2;
}

.logo {
  font-family:'Playfair Display',serif;
  font-size:22px;
  font-style:italic;
}
.logo a {
  text-decoration:none;
  color:inherit;
}

nav a {
  margin-left:25px;
  text-decoration:none;
  color:#333;
  font-size:14px;
}
nav a:hover { text-decoration:underline; }

/* CONTAINER */
.container {
  max-width:900px;
  margin:80px auto;
  padding:0 20px;
}

h1 {
  font-family:'Playfair Display',serif;
  font-size:36px;
  margin-bottom:10px;
}

.desc {
  font-size:15px;
  color:#555;
  margin-bottom:40px;
}

.history-card {
  background:#fff;
  border:1px solid #eee6da;
  border-radius:16px;
  padding:20px 25px;
  margin-bottom:18px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 8px 18px rgba(0,0,0,.06);
}

.history-card a {
  color:#1f1f1f;
  text-decoration:none;
  font-size:14px;
  word-break:break-all;
}

.history-card a:hover {
  text-decoration: underline;
}

a {
  color: blue;
  text-decoration: underline;
}

.copy-btn {
  background:#1f1f1f;
  color:#fff;
  border:none;
  border-radius:8px;
  padding:8px 14px;
  cursor:pointer;
  font-size:13px;
}

.success-toast {
  position:fixed;
  top:20px;
  left:50%;
  transform:translateX(-50%);
  background:#1f1f1f;
  color:#fff;
  padding:14px 26px;
  border-radius:25px;
  font-size:14px;
  z-index:9999;
  animation:slideDown .4s ease;
}

@keyframes slideDown {
  from { opacity:0; transform:translate(-50%,-20px); }
  to { opacity:1; transform:translate(-50%,0); }
}

footer {
  text-align:center;
  padding:30px;
  font-size:13px;
  color:#777;
}
</style>
</head>

<body>

<?php if (!empty($_SESSION['pantun_success'])): ?>
  <div class="success-toast">
    ✨ Pantun berjaya dihantar
  </div>
<?php unset($_SESSION['pantun_success']); endif; ?>

<header>
  <div class="logo">
    <a href="index.html">pantunletter</a>
  </div>
  <nav>
    <a href="send.php">Send Pantun</a>
    <a href="browsepantun.php">Browse</a>
    <a href="historypantun.php">History</a>
  </nav>
</header>

<section class="container">
  <h1>History</h1>
  <p class="desc">
    Ini adalah pautan pantun yang telah dihantar. Pautan boleh dikongsi dan akan dipadam secara automatik selepas 7 hari.
  </p>

  <?php while($row = $res->fetch_assoc()):
    $link = "http://localhost/pantunletter/details.php?token=".$row['share_token'];
  ?>
  <div class="history-card">
    <a href="<?= $link ?>" target="_blank"><?= $link ?></a>
    <button class="copy-btn" onclick="copyLink('<?= $link ?>')">Copy</button>
  </div>
  <?php endwhile; ?>
</section>

<footer>
  © 2026 PantunLetter · Traditional Malay Pantun Platform
</footer>

<script>
function copyLink(text){
  navigator.clipboard.writeText(text);
  alert("Link disalin");
}
</script>

</body>
</html>
