<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$pantuns = [];
$detectedTheme = null;

$recipient = $_REQUEST['recipient'] ?? '';
$message   = $_REQUEST['message'] ?? '';
$music_id  = $_REQUEST['music_id'] ?? '';
$isAnon    = isset($_REQUEST['is_anonymous']) ? 1 : 0;
$action    = $_REQUEST['action'] ?? '';

/* ===============================
   HELPER
=============================== */
function extractKeywords($text){
  $text = strtolower($text);
  $text = preg_replace('/[^a-zA-Z\s]/','',$text);
  $stop = ['aku','saya','dan','yang','untuk','dengan','tidak','akan','di','ke','dari','ini','itu','adalah'];
  return array_unique(array_filter(explode(' ',$text), fn($w)=>strlen($w)>3 && !in_array($w,$stop)));
}

/* ===============================
   POST LOGIC
=============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  /* ===== SEND PANTUN ===== */
  if ($action === "submit" && !empty($_POST['selected_pantun'])) {

    $token = bin2hex(random_bytes(8));
    $personalNote = $_POST['personal_note'] ?? null;
    if ($personalNote === '') {
        $personalNote = null;
    }

    $stmt = $conn->prepare("
      INSERT INTO sent_pantun
      (user_id, is_anonymous, recipient, message, pantun_text, music_id, share_token)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
      "iisssis",
      $userId,
      $isAnon,
      $recipient,
      $personalNote,
      $_POST['selected_pantun'],
      $music_id,
      $token
    );
    $stmt->execute();

    header("Location: user_profile.php");
    exit;
  }
}

/* ===== RECOMMENDATION ===== */
if (!empty($message)) {

  // 1️⃣ intent keywords
  $stmt = $conn->prepare("
    SELECT theme FROM intent_keywords
    WHERE ? LIKE CONCAT('%', keyword, '%')
    LIMIT 1
  ");
  $msgLower = strtolower($message);
  $stmt->bind_param("s",$msgLower);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    $detectedTheme = $row['theme'];
  }

  // 2️⃣ corpus match
  if (!$detectedTheme) {
    $keys = extractKeywords($message);
    if ($keys) {
      $conds=[];$params=[];$types="";
      foreach($keys as $k){$conds[]="pantun_text LIKE ?";$params[]="%$k%";$types.="s";}
      $sql="SELECT theme FROM pantun_dataset_clean WHERE ".implode(" OR ",$conds)." LIMIT 1";
      $stmt=$conn->prepare($sql);
      $stmt->bind_param($types,...$params);
      $stmt->execute();
      $res=$stmt->get_result();
      if($row=$res->fetch_assoc()) $detectedTheme=$row['theme'];
    }
  }

  // 3️⃣ fallback
  if (!$detectedTheme) {
    $row=$conn->query("SELECT theme FROM pantun_dataset_clean ORDER BY RAND() LIMIT 1")->fetch_assoc();
    $detectedTheme=$row['theme'];
  }

  // fetch pantun
  $stmt=$conn->prepare("
    SELECT pantun_text FROM pantun_dataset_clean
    WHERE theme=? ORDER BY RAND() LIMIT 6
  ");
  $stmt->bind_param("s",$detectedTheme);
  $stmt->execute();
  $res=$stmt->get_result();
  while($r=$res->fetch_assoc()) $pantuns[]=$r['pantun_text'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Send Pantun</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
  html, body {
  margin: 0;
  padding: 0;
}
body{
  font-family:'Poppins',sans-serif;
  background:#faf7f2 url("assets/bgfloral.jpeg");
  background-size:420px auto;
  color:#2e2e2e;
}

.container{
  max-width:1200px;
  margin:30px auto;
  padding:0 20px;
}

.card{
  background:rgba(255,255,255,.75);
  backdrop-filter:blur(6px);
  border-radius:28px;
  padding:30px 45px;
  box-shadow:0 20px 45px rgba(0,0,0,.12);
}

.form-grid{
  display:grid;
  grid-template-columns:320px 1fr;
  gap:30px 40px;
  align-items:flex-start;
  transition:grid-template-columns 0.3s ease;
}

.form-grid.has-selection{
  grid-template-columns:320px 1fr 280px;
}

@media (max-width: 992px) {
  .form-grid.has-selection {
    grid-template-columns: 320px 1fr;
  }
  #finalStepColumn {
    grid-column: 1 / span 2;
    margin-top: 20px;
  }
}

@media (max-width: 768px) {
  .form-grid, .form-grid.has-selection {
    grid-template-columns: 1fr !important;
    gap: 24px !important;
  }
  #finalStepColumn {
    grid-column: auto;
  }
}

h2{
  font-family:'Playfair Display',serif;
  font-size:30px;
  margin-bottom:18px;
}

label{
  font-size:13px;
  margin-bottom:6px;
  display:block;
}

input, textarea, select{
  width:100%;
  padding:12px;
  border-radius:12px;
  border:1px solid #ddd;
  font-family:'Poppins',sans-serif;
  margin-bottom:16px;
}

textarea{
  min-height:90px;
  resize:none;
}

.actions{
  margin-top:18px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

button{
  background:#1f1f1f;
  color:#fff;
  border:none;
  border-radius:25px;
  padding:10px 22px;
  cursor:pointer;
}

/* RIGHT COLUMN HEADER */
.right-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:14px;
}

.right-header h3{
  font-family:'Playfair Display',serif;
  font-size:22px;
  margin:0;
}



/* PANTUN LIST */
.pantun-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:12px;
}

.pantun{
  background:#fff;
  padding:16px;
  border-radius:16px;
  border:1.5px solid #d8b36a;
  font-family:'Playfair Display',serif;
  font-style:italic;
  font-size:17px;
  font-weight:400;
  line-height:1.75;
  cursor:pointer;
  transition:transform .25s ease, box-shadow .25s ease;
}

.pantun:hover{
  transform:translateY(-6px);
  box-shadow:0 14px 30px rgba(201,162,77,.25);
}

.pantun.selected{
  border:2px solid #c9a24d;
  box-shadow:0 0 0 2px rgba(201,162,77,.25);
}

.pantun.new-card {
  animation: cardFadeIn 0.4s ease forwards;
}

@keyframes cardFadeIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

audio{
  width:100%;
  margin-top:12px;
}

.hero-title{
  font-family:'Playfair Display',serif;
  font-size:38px;
  font-style:italic;
  line-height:1.3;
  margin-bottom:12px;
}

.hero-subtitle{
  font-size:14px;
  color:#555;
  margin-bottom:28px;
}

.gold{
  color:#c9a24d;
  position:relative;
}

.gold::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-4px;
  width:100%;
  height:2px;
  background:rgba(201,162,77,.45);
}

/* Anonymous toggle (UI only) */
.anon-wrap{
  margin-top:18px;
}

.anon-toggle{
  display:inline-block;
}

.anon-toggle input{
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.anon-toggle span{
  display:inline-block;
  padding:9px 18px;
  border-radius:22px;
  border:1.5px solid #c9a24d;
  color:#c9a24d;
  font-size:13px;
  cursor:pointer;
  transition:all .25s ease;
}

.anon-toggle input:checked + span{
  background:#c9a24d;
  color:#fff;
  box-shadow:0 6px 16px rgba(201,162,77,.35);
}
.site-header,
header {
  background: rgba(255, 250, 243, 0.96) !important;
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  border-bottom: 1px solid #e5dccb;
  position: relative;
  z-index: 20;
}

/* push content down cleanly */
.container {
  margin-top: 30px;
}

.tag {
  display: inline-block;
  font-size: 12px;
  background: #f1ede6;
  padding: 4px 10px;
  border-radius: 20px;
  margin-bottom: 10px;
  color: #555;
  border: 1.5px solid #d8b36a;
}
</style>

</head>

<body>

<?php include "header.php"; ?>

<section class="container">
<div class="card">
<form method="GET" id="sendForm">

<input type="hidden" name="action" id="action">

<div class="form-grid" id="formGrid">

<!-- LEFT -->
<div>
<h1 class="hero-title">
  sampaikan <span class="gold">niat</span><br>
  melalui <span class="gold">pantun</span>
</h1>

<label>Recipient</label>
<input name="recipient" required value="<?= htmlspecialchars($recipient) ?>">

<label>Ceritakan Perasaan Anda</label>
<span style="display: block; color: #777; font-style: italic; font-size: 12px; margin-top: -4px; margin-bottom: 6px;">Field ini hanya digunakan untuk mencadangkan pantun yang sesuai, dan TIDAK akan dihantar kepada penerima.</span>
<textarea name="message" required><?= htmlspecialchars($message) ?></textarea>

<label>Music</label>
<select name="music_id" id="musicSelect">
<option value="">— No music —</option>
<?php
$m=$conn->query("SELECT * FROM music_background");
while($x=$m->fetch_assoc()):
$sel = ($music_id==$x['music_id'])?'selected':'';
?>
<option value="<?= $x['music_id'] ?>" data-file="<?= $x['file_path'] ?>" <?= $sel ?>>
<?= htmlspecialchars($x['music_name']) ?>
</option>
<?php endwhile; ?>
</select>

<audio id="player" controls></audio>

<div class="anon-toggle">
  <label>
    <input type="checkbox" name="is_anonymous" <?= $isAnon?'checked':'' ?>>
    <span>Hantar Tanpa Nama</span>
  </label>
</div>

<button onclick="setAction('search')">Cari Pantun</button>

</div>

<!-- RIGHT -->
<div>
<?php if($pantuns): ?>
<?php if (!empty($detectedTheme)): ?>
  <div style="margin-bottom: 5px;">
    <span class="tag">Berdasarkan tema: <?= htmlspecialchars($detectedTheme) ?></span>
  </div>
<?php endif; ?>
<div class="right-header">
<h3>Pantun Dicadangkan</h3>
<div style="display: flex; gap: 8px; align-items: center;">
  <button type="button" id="prevBtn" onclick="prevPantun()" style="display: none; font-size: 13px; padding: 8px 16px; border-radius: 20px; background: transparent; color: #1f1f1f; border: 1px solid #1f1f1f; cursor: pointer; transition: all 0.2s ease;">Sebelumnya</button>
  <button type="button" id="loadMoreBtn" onclick="loadMorePantun()" style="font-size: 13px; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Lihat Lebih Banyak</button>
</div>
</div>

<div class="pantun-grid">
<?php foreach($pantuns as $p): ?>
<div class="pantun" onclick="selectPantun(this,`<?= htmlspecialchars($p) ?>`)">
<?= nl2br(htmlspecialchars($p)) ?>
</div>
<?php endforeach; ?>
</div>

<input type="hidden" name="selected_pantun" id="selectedPantun">

<?php endif; ?>
</div>

<!-- COLUMN 3: RIGHTMOST FINAL STEP -->
<?php if($pantuns): ?>
<div id="finalStepColumn" style="display: none; animation: cardFadeIn 0.4s ease forwards;">
  <h3 style="font-family:'Playfair Display',serif; font-size:22px; margin-bottom: 14px;">Hantar</h3>
  <div style="margin-bottom: 16px;">
    <label>Pesanan Peribadi (Pilihan)</label>
    <textarea name="personal_note" placeholder="Tulis pesanan ringkas untuk penerima... (pilihan)" style="min-height: 120px;"></textarea>
  </div>
  <button type="submit" onclick="return submitPantun()" style="width: 100%;">Hantar Pantun</button>
</div>
<?php endif; ?>

</div>
</form>
</div>
</section>

<?php include "footer.php"; ?>

<script>
let chosen="";
let allPantuns = <?= json_encode($pantuns) ?>;
let shownPantuns = allPantuns.slice(0);
let detectedTheme = <?= json_encode($detectedTheme) ?>;
let currentPage = 0;

function renderCurrentPage() {
  const grid = document.querySelector(".pantun-grid");
  if (!grid) return;
  
  grid.innerHTML = "";
  
  const start = currentPage * 6;
  const end = start + 6;
  const pagePantuns = allPantuns.slice(start, end);
  
  pagePantuns.forEach(p => {
    const card = document.createElement("div");
    card.className = "pantun new-card";
    
    if (p === chosen) {
      card.classList.add("selected");
    }
    
    const escaped = p
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
    
    card.innerHTML = escaped.replace(/\n/g, "<br>");
    
    card.onclick = function() {
      selectPantun(this, p);
    };
    
    grid.appendChild(card);
  });
  
  const prevBtn = document.getElementById("prevBtn");
  if (prevBtn) {
    prevBtn.style.display = currentPage > 0 ? "inline-block" : "none";
  }
}

function loadMorePantun() {
  if (!detectedTheme) return;
  
  const nextStart = (currentPage + 1) * 6;
  if (allPantuns.length > nextStart) {
    currentPage++;
    renderCurrentPage();
    return;
  }
  
  const btn = document.getElementById("loadMoreBtn");
  if (!btn) return;
  
  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = "Loading...";

  fetch("load_more_pantun.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      theme: detectedTheme,
      exclude: shownPantuns
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data && data.length > 0) {
      data.forEach(p => {
        allPantuns.push(p);
        shownPantuns.push(p);
      });
      currentPage++;
      renderCurrentPage();
    } else {
      alert("Tiada lagi pantun untuk tema ini.");
    }
    btn.disabled = false;
    btn.textContent = originalText;
  })
  .catch(err => {
    console.error(err);
    btn.disabled = false;
    btn.textContent = originalText;
  });
}

function prevPantun() {
  if (currentPage > 0) {
    currentPage--;
    renderCurrentPage();
  }
}

function setAction(a){
  document.getElementById("action").value=a;
}

function selectPantun(el,text){
  document.querySelectorAll('.pantun').forEach(p=>p.classList.remove('selected'));
  el.classList.add('selected');
  chosen=text;
  document.getElementById("selectedPantun").value=text;
  
  const grid = document.getElementById("formGrid");
  if (grid) {
    grid.classList.add("has-selection");
  }
  const column = document.getElementById("finalStepColumn");
  if (column) {
    column.style.display = "block";
  }
}

function submitPantun(){
  if(!chosen){
    alert("⚠️ Sila pilih pantun dahulu.");
    return false;
  }
  setAction("submit");
  const form = document.getElementById("sendForm");
  if (form) {
    form.method = "POST";
  }
  return true;
}

/* 🎵 FIX: change music immediately when selection changes */
const musicSelect = document.getElementById("musicSelect");
const player = document.getElementById("player");

if(musicSelect){
  musicSelect.addEventListener("change", function(){
    const opt = this.selectedOptions[0];

    if(opt && opt.dataset.file){
      player.pause();
      player.src = opt.dataset.file;
      player.load();
      player.volume = 0.45;
      player.play().catch(()=>{});
    }else{
      player.pause();
      player.src = "";
    }
  });
}

/* 🔁 Restore selected music after Cari Pantun */
document.addEventListener("DOMContentLoaded", ()=>{
  if(musicSelect && musicSelect.value){
    musicSelect.dispatchEvent(new Event("change"));
  }

  const anonCheckbox = document.querySelector('input[name="is_anonymous"]');
  const recipientInput = document.querySelector('input[name="recipient"]');

  function updateRecipientRequired() {
    if (anonCheckbox && recipientInput) {
      if (anonCheckbox.checked) {
        recipientInput.removeAttribute("required");
      } else {
        recipientInput.setAttribute("required", "required");
      }
    }
  }

  // Run on load
  updateRecipientRequired();

  // Run on checkbox change/click
  if (anonCheckbox) {
    anonCheckbox.addEventListener("change", updateRecipientRequired);

    anonCheckbox.addEventListener("click", function(e) {
      const messageInput = document.querySelector('textarea[name="message"]');
      const messageVal = messageInput ? messageInput.value.trim() : "";
      
      if (!messageVal) {
        e.preventDefault(); // Cancel toggle
        alert("⚠️ Sila ceritakan perasaan anda terlebih dahulu.");
      }
    });
  }
});

</script>

</body>
</html>
