<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
/* ================= RESET ================= */
*{box-sizing:border-box;margin:0;padding:0}

/* ================= MOTION BACKGROUND (IMAGE BASED) ================= */
body {
  font-family: 'Poppins', sans-serif;
  color: #2e2e2e;
  line-height: 1.6;

  /* LAYER 1: animated soft light */
  background-image:
    linear-gradient(
      120deg,
      rgba(255,255,255,0.35),
      rgba(255,255,255,0.15),
      rgba(255,255,255,0.35)
    ),

    /* LAYER 2: repeating traditional pattern */
    url("assets/bgfloral4.jpeg");

  background-repeat: repeat;
  background-size:
    400% 400%,   /* gradient motion */
    400px 400px; /* 👈 IMPORTANT: pattern scale */

  animation: bgMove 40s ease infinite;
}




/* ================= HEADER ================= */
header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:12px 40px;
  background:rgba(255,255,255,.92);
  backdrop-filter:blur(6px);
  border-bottom:1px solid #e5e0d8;
}

.logo{
  font-family:'Playfair Display',serif;
  font-size:22px;
  font-style:italic;
}

nav a{
  margin-left:25px;
  text-decoration:none;
  color:#333;
  font-size:14px;
}

/* ================= HERO ================= */
.hero{
  text-align:center;
  padding:60px 20px 40px; /* LESS HEIGHT */
  max-width:900px;
  margin:auto;
}

.hero h1{
  font-family:'Playfair Display',serif;
  font-size:48px;
  font-style:italic;
  margin-bottom:18px;
}

.hero p{
  font-size:15px;
  color:#555;
  margin-bottom:30px;
}

.gold{color:#c9a24d}

/* ================= BUTTONS ================= */
.hero-buttons a,
.hero-buttons button{
  margin:0 10px;
}

.btn-primary{
  background:#1f1f1f;
  color:#fff;
  padding:12px 32px;
  border-radius:26px;
  border:none;
  cursor:pointer;
  font-size:14px;
  text-decoration:none;
}

.btn-secondary{
  background:transparent;
  color:#1f1f1f;
  padding:12px 32px;
  border-radius:26px;
  border:1px solid #1f1f1f;
  text-decoration:none;
}

/* ================= SLIDESHOW (MOVED UP & STRONGER) ================= */
.pantun-carousel{
  padding:40px 0 70px; /* MUCH HIGHER */
  overflow:hidden;
  position:relative;
}

.pantun-carousel h2{
  font-family:'Playfair Display',serif;
  font-size:30px;
  text-align:center;
  margin-bottom:35px;
}

/* Gradient fade edges */
.pantun-carousel::before,
.pantun-carousel::after{
  content:"";
  position:absolute;
  top:0;
  width:120px;
  height:100%;
  z-index:2;
}

.pantun-carousel::before{
  left:0;
  background:linear-gradient(to right, #faf7f2, transparent);
}
.pantun-carousel::after{
  right:0;
  background:linear-gradient(to left, #faf7f2, transparent);
}

/* Track */
.carousel-track{
  display:flex;
  gap:34px;
  width:max-content;
  animation:scroll 45s linear infinite;
}

.carousel-wrapper:hover .carousel-track{
  animation-play-state:paused;
}

@keyframes scroll{
  from{transform:translateX(0)}
  to{transform:translateX(-50%)}
}

/* ================= SLIDESHOW CARD (MORE VISIBLE & COOL) ================= */
.pantun-card{
  min-width:340px;
  background:rgba(255,255,255,.97);
  border-radius:22px;
  padding:36px;
  border:1px solid #e8dcc7;
  box-shadow:
    0 20px 40px rgba(0,0,0,.14),
    inset 0 0 0 1px rgba(201,162,77,.18);
  transition:.45s ease;
}

.pantun-card:hover{
  transform:translateY(-8px) scale(1.05);
  box-shadow:
    0 30px 60px rgba(0,0,0,.22),
    inset 0 0 0 1px rgba(201,162,77,.35);
}

.pantun-card p{
  font-family:'Playfair Display',serif;
  font-size:18px;
  font-style:italic;
  margin-bottom:16px;
  line-height:1.7;
}

.pantun-card span{
  font-size:13px;
  color:#777;
}

/* ================= FEATURES ================= */
.features{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:30px;
  padding:50px 80px 90px;
}

.feature-card{
  background:rgba(255,255,255,.96);
  padding:34px;
  border-radius:18px;
  border:1px solid #eee6da;
  box-shadow:0 14px 32px rgba(0,0,0,.10);
  transition:.35s;
}

.feature-card:hover{
  transform:translateY(-6px);
  box-shadow:0 24px 48px rgba(0,0,0,.16);
}

/* ================= FOOTER ================= */
footer{
  text-align:center;
  padding:40px;
  font-size:13px;
  color:#777;
}
</style>
</head>

<body>

<?php include "header.php"; ?>

<section class="hero">
  <h1>
    kata yang <span class="gold">tidak terucap</span>,<br>
    disampaikan melalui <span class="gold">pantun</span>
  </h1>

  <p>Sampaikan perasaan secara halus melalui pantun dan iringan muzik tradisional Melayu.</p>

  <div class="hero-buttons">
    <?php if($isLoggedIn): ?>
      <a href="send.php" class="btn-primary">Send a Pantun</a>
    <?php else: ?>
      <button class="btn-primary" onclick="requireLogin()">Send a Pantun</button>
    <?php endif; ?>
    <a href="browsepantun.php" class="btn-secondary">Browse Pantun</a>
  </div>
</section>

<!-- SLIDESHOW MOVED UP -->
<section class="pantun-carousel">
  <h2>Pantun Pilihan</h2>

  <div class="carousel-wrapper">
    <div class="carousel-track">
      <?php
      $pantuns=[
        ["Pulau Pandan jauh ke tengah,<br>Gunung Daik bercabang tiga.","Aisyah"],
        ["Pagi petang siang malam,<br>Hati terang senang faham.","Amir"],
        ["Kayu lurus dalam ladang,<br>Kerbau kurus banyak tulang.","Sarah"],
        ["Hujung bendul dalam semak,<br>Kerbau mandul banyak lemak.","Hakim"],
      ];
      foreach(array_merge($pantuns,$pantuns) as $p):
      ?>
      <div class="pantun-card">
        <p><?= $p[0] ?></p>
        <span>— Untuk <?= $p[1] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="features">
  <div class="feature-card">
    <h3>Hantar Pantun</h3>
    <p>Pilih pantun yang sesuai dengan niat anda.</p>
  </div>
  <div class="feature-card">
    <h3>Teroka Pantun</h3>
    <p>Lihat pantun-pantun yang pernah dikongsi.</p>
  </div>
  <div class="feature-card">
    <h3>Makna Pantun</h3>
    <p>Keindahan bahasa Melayu yang tersirat.</p>
  </div>
</section>

<?php include "footer.php"; ?>

<script>
function requireLogin(){
  if(confirm("You need to login first.\nGo to login page now?")){
    window.location.href="user_login.php";
  }
}
</script>

</body>
</html>
