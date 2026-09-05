<?php
include "db.php";

$token = $_GET['token'] ?? '';

if ($token === '') {
  die("Token tidak sah.");
}

$stmt = $conn->prepare("
  SELECT 
    s.recipient,
    s.message,
    s.pantun_text,
    s.is_anonymous,
    u.name AS sender_name,
    m.music_name,
    m.file_path
  FROM sent_pantun s
  LEFT JOIN users u ON s.user_id = u.user_id
  LEFT JOIN music_background m ON s.music_id = m.music_id
  WHERE s.share_token = ?
  LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
  die("Pantun tidak dijumpai.");
}

$senderDisplay = $data['is_anonymous']
  ? 'Anonymous'
  : ($data['sender_name'] ?? 'Pengirim');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pantun Untuk <?= htmlspecialchars($data['recipient']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}

/* ===== BACKGROUND MOTION ===== */
body{
  font-family:'Poppins',sans-serif;
  background:url("assets/bgfloral2.jpeg") repeat;
  background-size:520px auto;
  animation:bgFloat 90s linear infinite;
  color:#2e2e2e;
}

@keyframes bgFloat{
  from{ background-position:0 0; }
  to{ background-position:0 -1200px; }
}

/* ===== SHARED CARD ===== */
.card{
  background:linear-gradient(180deg, rgba(255,255,255,.92), rgba(250,246,238,.9));
  backdrop-filter:blur(6px);
  border-radius:28px;
  box-shadow:0 22px 48px rgba(0,0,0,.14);
}

/* ===== LAYOUT ===== */
.container{
  max-width:900px;
  margin:30px auto;
  padding:0 20px;
  text-align:center;
}

/* ===== HERO CARD ===== */
.hero{
  padding:30px 24px;
  margin-bottom:24px;
}

.recipient{
  font-size:18px;
  margin-bottom:10px;
}
.recipient span{
  color:#c9a24d;
  font-weight:600;
  position:relative;
}
.recipient span::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-6px;
  width:100%;
  height:3px;
  background:rgba(201,162,77,.55);
}

.sender{
  font-size:14px;
  color:#666;
  margin-bottom:26px;
}

.hero h1{
  font-family:'Playfair Display',serif;
  font-size:34px;
  font-style:italic;
  line-height:1.5;
}

.gold{ color:#c9a24d }

/* ===== CONTENT CARD ===== */
.content{
  padding:36px 30px;
}

.label{
  font-size:12px;
  letter-spacing:2px;
  text-transform:uppercase;
  color:#777;
  margin-bottom:26px;
}

/* PANTUN */
.pantun-text{
  font-family:'Playfair Display',serif;
  font-size:22px;
  font-style:italic;
  line-height:1.9;
  margin-bottom:24px;
}

/* DIVIDER */
.divider{
  width:80px;
  height:2px;
  background:#e6d3a3;
  margin:20px auto;
}

/* MESSAGE */
.message-text{
  font-family:'Playfair Display',serif;
  font-size:18px;
  font-style:italic;
  line-height:1.75;
  color:#3b352b;
}

/* MUSIC */
.music-box{
  margin-top:38px;
}
.music-title{
  font-size:14px;
  color:#666;
  margin-bottom:12px;
}
audio{
  width:100%;
  border-radius:14px;
}
</style>
</head>

<body>

<?php include "header.php"; ?>

<section class="container">

  <!-- HERO -->
  <div class="card hero">
    <div class="recipient">
      Untuk: <span><?= htmlspecialchars($data['recipient']) ?></span>
    </div>

    <div class="sender">
      Daripada: <?= htmlspecialchars($senderDisplay) ?>
    </div>

    <h1>
      sebuah <span class="gold">pantun</span><br>
      disampaikan dengan <span class="gold">ikhlas</span>
    </h1>
  </div>

  <!-- COMBINED CONTENT -->
  <div class="card content">

    <div class="label">Pantun</div>

    <div class="pantun-text">
      <?= nl2br(htmlspecialchars($data['pantun_text'])) ?>
    </div>

    <?php if (!empty($data['file_path'])): ?>
      <div class="music-box">
        <div class="music-title">
          🎵 Iringan Muzik: <?= htmlspecialchars($data['music_name']) ?>
        </div>
        <audio id="bgMusic" controls autoplay loop>
          <source src="<?= htmlspecialchars($data['file_path']) ?>" type="audio/mpeg">
        </audio>
      </div>
    <?php endif; ?>

    <?php if (!empty($data['message'])): ?>
      <div class="divider"></div>
      <div class="label">Pesanan</div>
      <div class="message-text">
        <?= nl2br(htmlspecialchars($data['message'])) ?>
      </div>
    <?php endif; ?>

  </div>

</section>

<?php include "footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded",()=>{
  const music=document.getElementById("bgMusic");
  if(music){
    music.volume=0.45;
    music.play().catch(()=>{});
  }
});
</script>

</body>
</html>
