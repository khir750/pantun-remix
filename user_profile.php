<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

/* SENT PANTUN */
$stmt = $conn->prepare("
    SELECT recipient, pantun_text, share_token, created_at
    FROM sent_pantun
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$sentPantun = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* FAVOURITE PANTUN */
$stmt = $conn->prepare("
    SELECT p.theme, p.pantun_text, f.created_at
    FROM favourite_pantun f
    JOIN pantun_dataset_clean p ON f.pantun_id = p.pantun_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$favPantun = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Profile | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --paper:#fbf7f1;
  --ink:#2b2b2b;

  /* Traditional Malay tones */
  --gold:#c9a24d;
  --gold-soft:#ead7a1;
  --brown:#6b4f2a;
  --cream:#fffaf2;

  --border:#e6d6b8;
  --shadow:rgba(107,79,42,.15);
}

/* ===== BACKGROUND WITH MOTION ===== */
body{
  margin:0;
  font-family:'Poppins',sans-serif;
  color:var(--ink);
  background:url("assets/bgfloral2.jpeg") repeat;
  background-size:500px auto;
  animation:bgMove 60s linear infinite;
}


@keyframes bgMove{
  from{background-position:0 0;}
  to{background-position:0 1000px;}
}

/* ===== CONTAINER ===== */
.container{
  max-width:1200px;
  margin:24px auto 40px;
  padding:0 20px;
}

/* ===== HERO ===== */
.profile-header{
  text-align:center;
  margin-bottom:20px;
}

.profile-header h1{
  font-family:'Playfair Display',serif;
  font-size:40px;
  margin:0;
}

.profile-header h1 span{
  color:var(--gold);
}

.profile-header .subtitle{
  margin-top:6px;
  font-size:14px;
  font-style:italic;
  color:#666;
}

/* ===== GRID ===== */
.profile-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
}

/* ===== SECTION BOX ===== */
.section{
  background:linear-gradient(180deg, #fffdf8, var(--cream));
  border:1px solid var(--border);
  border-radius:22px;
  padding:22px 24px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  transition:transform .35s ease, box-shadow .35s ease;
}
.section:hover{
  transform:translateY(-4px);
  box-shadow:
    0 18px 38px rgba(0,0,0,.12),
    0 0 0 1px var(--gold-soft);
}
.section h2{
  font-family:'Playfair Display',serif;
  font-size:22px;
  margin-bottom:18px;
  padding-bottom:8px;
  border-bottom:1px solid var(--gold-soft);
  color:var(--brown);
  letter-spacing:.3px;
}
/* ===== CARD ===== */
.card{
  background:#ffffff;
  border:1px solid var(--border);
  border-radius:16px;
  padding:18px 20px;
  margin-bottom:16px;
  box-shadow:0 4px 12px rgba(0,0,0,.06);
  transition:
    transform .25s ease,
    box-shadow .25s ease,
    border-color .25s ease;
}

.card:hover{
  transform:translateY(-3px);
  border-color:var(--gold);
  box-shadow:
    0 10px 22px rgba(0,0,0,.1),
    0 0 0 1px var(--gold-soft);
}
/* ===== PANTUN TEXT ===== */
.card p{
  font-family:'Playfair Display',serif;
  font-style:italic;
  font-size:15.8px;
  line-height:1.65;
  margin-bottom:10px;
}

/* ===== META ===== */
.meta{
  display:flex;
  justify-content:space-between;
  align-items:center;
  font-size:12.8px;
  color:#666;
  gap:10px;
}

/* ===== COPY LINK ===== */
.copy-link{
  cursor:pointer;
  font-size:12.5px;
  color:var(--gold);
  font-weight:500;
  position:relative;
}

.copy-link::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-2px;
  width:100%;
  height:1px;
  background:var(--gold);
  opacity:.6;
}

.copy-link:hover{
  color:#b8892e;
}


/* ===== EMPTY TEXT ===== */
.empty{
  font-size:13px;
  color:#777;
  font-style:italic;
}

/* ===== TOAST ===== */
.toast{
  position:fixed;
  bottom:24px;
  right:24px;
  background:#1f1f1f;
  color:#fff;
  padding:10px 18px;
  border-radius:14px;
  font-size:12.5px;
  opacity:0;
  pointer-events:none;
  transition:.3s;
}
.toast.show{opacity:1}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
  .profile-grid{grid-template-columns:1fr}
}
</style>
</head>

<body>

<?php include "header.php"; ?>

<section class="container">

  <div class="profile-header">
    <h1>Welcome, <span><?= htmlspecialchars($userName) ?></span></h1>
    <div class="subtitle">Here is your Pantun activity · <a href="update_user_password.php" style="color: var(--gold); text-decoration: none;">Change Password</a></div>
  </div>

  <div class="profile-grid">

    <!-- SENT PANTUN -->
    <div class="section">
      <h2>Sent Pantun</h2>

      <?php if(empty($sentPantun)): ?>
        <div class="empty">You have not sent any pantun yet.</div>
      <?php else: foreach($sentPantun as $s):
        $link="details.php?token=".$s['share_token']; ?>
        <div class="card" onclick="window.location='<?= $link ?>'">
          <p><?= nl2br(htmlspecialchars($s['pantun_text'])) ?></p>
          <div class="meta">
            <span>To <?= htmlspecialchars($s['recipient']) ?> · <?= date("d M Y",strtotime($s['created_at'])) ?></span>
            <span class="copy-link" onclick="copyLink(event,'<?= $link ?>')">Copy link</span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- FAVOURITE PANTUN -->
    <div class="section">
      <h2>Favourite Pantun</h2>

      <?php if(empty($favPantun)): ?>
        <div class="empty">No favourite pantun yet.</div>
      <?php else: foreach($favPantun as $f): ?>
        <div class="card">
          <p><?= nl2br(htmlspecialchars($f['pantun_text'])) ?></p>
          <div class="meta">
            <span>Theme: <?= htmlspecialchars($f['theme']) ?></span>
            <span><?= date("d M Y",strtotime($f['created_at'])) ?></span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

  </div>

</section>

<?php include "footer.php"; ?>

<div id="toast" class="toast">Link copied</div>

<script>
function copyLink(e, link){
  e.stopPropagation();
  navigator.clipboard.writeText(location.origin+"/pantun/"+link).then(()=>{
    const t=document.getElementById("toast");
    t.classList.add("show");
    setTimeout(()=>t.classList.remove("show"),2000);
  });
}
</script>

</body>
</html>
