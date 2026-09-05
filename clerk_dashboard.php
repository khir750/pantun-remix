<?php
session_start();

/* =============================
   DATABASE CONNECTION (DIRECT)
============================= */
$conn = new mysqli("sql309.infinityfree.com", "if0_42299665", "Pantun2026", "if0_42299665_pantun");
if ($conn->connect_error) {
    die("Database connection failed");
}

/* =============================
   ACCESS CONTROL (CLERK ONLY)
============================= */
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'clerk') {
    die("Access denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clerk Dashboard | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}

/* === SAME SVG BACKGROUND === */
body{
  font-family:'Poppins',sans-serif;
  background-color:#faf7f2;
  background-image:url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d6c9b2' fill-opacity='0.15'%3E%3Cpath d='M40 0c22.091 0 40 17.909 40 40S62.091 80 40 80 0 62.091 0 40 17.909 0 40 0zm0 6C21.222 6 6 21.222 6 40s15.222 34 34 34 34-15.222 34-34S58.778 6 40 6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  background-repeat:repeat;
  color:#2e2e2e;
}

/* === HEADER === */
header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 60px;
  background:#fff;
  border-bottom:1px solid #e5e0d8;
}
.logo{
  font-family:'Playfair Display',serif;
  font-size:18px;
  font-style:italic;
}
nav a{
  margin-left:25px;
  text-decoration:none;
  font-size:14px;
  color:#333;
}
nav a:hover{text-decoration:underline}

/* === HERO === */
.hero{
  text-align:center;
  padding:90px 20px 60px;
  max-width:900px;
  margin:auto;
  animation:fadeDown .9s ease;
}
.hero h1{
  font-family:'Playfair Display',serif;
  font-size:42px;
  font-style:italic;
  margin-bottom:15px;
}
.hero p{
  font-size:15px;
  color:#555;
}
.gold{color:#c9a24d}

/* === FEATURES === */
.features{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:30px;
  padding:40px 80px 80px;
}

.feature-card{
  background:#fff;
  padding:32px;
  border-radius:18px;
  border:1px solid #eee6da;
  box-shadow:0 10px 22px rgba(0,0,0,.08);
  text-align:center;
  animation:fadeUp .9s ease forwards;
  transition:.35s ease;
}
.feature-card:hover{
  transform:translateY(-10px);
  box-shadow:0 18px 36px rgba(0,0,0,.14);
}

.feature-card h3{
  font-family:'Playfair Display',serif;
  font-size:20px;
  margin-bottom:12px;
}

.feature-card p{
  font-size:14px;
  color:#555;
  margin-bottom:22px;
  line-height:1.6;
}

.feature-card a{
  display:inline-block;
  padding:13px 30px;
  background:#1f1f1f;
  color:#fff;
  border-radius:28px;
  text-decoration:none;
  font-size:14px;
  transition:.3s ease;
}
.feature-card a:hover{
  background:#000;
  transform:scale(1.05);
}

/* === FOOTER === */
footer{
  text-align:center;
  padding:30px;
  font-size:13px;
  color:#777;
}

/* === ANIMATION === */
@keyframes fadeUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:none}
}
@keyframes fadeDown{
  from{opacity:0;transform:translateY(-25px)}
  to{opacity:1;transform:none}
}
</style>
</head>

<body>

<header>
  <div class="logo">PantunLetter | Aksara Poetiqa</div>
  <nav>
    <a>Clerk</a>
    <a href="staff_logout.php">Logout</a>
  </nav>
</header>

<section class="hero">
  <h1>
    Selamat Datang,<br>
    <span class="gold"><?= htmlspecialchars($_SESSION['staff_name']) ?></span>
  </h1>
  <p>
    Panel pengurusan kandungan untuk memastikan pantun dan muzik sentiasa berkualiti
    dan tersusun.
  </p>
</section>

<section class="features">

  <div class="feature-card">
    <h3>Urus Pantun</h3>
    <p>
      Tambah, sunting dan padam pantun bagi memastikan kandungan sentiasa relevan
      dan berkualiti.
    </p>
    <a href="pantun_list.php">Manage Pantun</a>
  </div>

  <div class="feature-card">
    <h3>Urus Muzik</h3>
    <p>
      Mengurus muzik latar yang dipadankan dengan pantun untuk pengalaman pengguna
      yang lebih menarik.
    </p>
    <a href="music_list.php">Manage Music</a>
  </div>

</section>

<footer>
  © <?= date('Y') ?> PantunLetter · Clerk Dashboard
</footer>

</body>
</html>






