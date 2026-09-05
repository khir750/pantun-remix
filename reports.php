<?php
session_start();

/* =============================
   ACCESS CONTROL (MANAGER ONLY)
============================= */
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'manager') {
    die("Access denied");
}

/* =============================
   DATABASE CONNECTION
============================= */
$conn = new mysqli("sql309.infinityfree.com", "if0_42299665", "Pantun2026", "if0_42299665_pantun");
if ($conn->connect_error) {
    die("Database connection failed");
}

/* =============================
   SYSTEM SUMMARY
============================= */
$totalPantun = $conn->query("SELECT COUNT(*) total FROM pantun_dataset_clean")->fetch_assoc()['total'];
$totalMusic  = $conn->query("SELECT COUNT(*) total FROM music_background")->fetch_assoc()['total'];
$totalSent   = $conn->query("SELECT COUNT(*) total FROM sent_pantun")->fetch_assoc()['total'];

/* =============================
   PANTUN BY THEME
============================= */
$themeData = [];
$themeCount = [];

$themeQuery = $conn->query("
  SELECT theme, COUNT(*) total
  FROM pantun_dataset_clean
  GROUP BY theme
  ORDER BY total DESC
");

while ($row = $themeQuery->fetch_assoc()) {
    $themeData[]  = $row['theme'];
    $themeCount[] = $row['total'];
}

/* =============================
   MOST USED MUSIC
============================= */
$musicData = [];
$musicCount = [];

$musicQuery = $conn->query("
  SELECT mb.music_name, COUNT(sp.music_id) total
  FROM sent_pantun sp
  JOIN music_background mb ON sp.music_id = mb.music_id
  GROUP BY mb.music_id
  ORDER BY total DESC
  LIMIT 5
");

while ($row = $musicQuery->fetch_assoc()) {
    $musicData[]  = $row['music_name'];
    $musicCount[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>System Reports | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
* { box-sizing:border-box; margin:0; padding:0; }

body {
  font-family:'Poppins',sans-serif;
  background:url("../assets/bgfloral.jpeg") repeat;
  background-size:420px auto;
  color:#2e2e2e;
}

.container {
  max-width:1100px;
  margin:80px auto;
  padding:0 20px;
}

.page-title {
  text-align:center;
  margin-bottom:40px;
}

.page-title h1 {
  font-family:'Playfair Display',serif;
  font-size:36px;
}

/* === SUMMARY CARDS === */
.summary {
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:25px;
  margin-bottom:60px;
}

.summary-card {
  background:#fff;
  border:1px solid #eee6da;
  border-radius:16px;
  padding:25px;
  text-align:center;
  box-shadow:0 8px 18px rgba(0,0,0,.08);
}

.summary-card h3 {
  font-family:'Playfair Display',serif;
  font-size:20px;
  margin-bottom:10px;
}

.summary-card span {
  font-size:28px;
  font-weight:500;
  color:#c9a24d;
}

/* === REPORT SECTIONS === */
.section {
  margin-bottom:80px;
}

.section h2 {
  font-family:'Playfair Display',serif;
  font-size:28px;
  margin-bottom:25px;
  text-align:center;
}

/* === CHART CARD === */
.chart-card {
  background:#fff;
  border:1px solid #eee6da;
  border-radius:16px;
  padding:30px;
  box-shadow:0 8px 18px rgba(0,0,0,.08);
}

.back-link {
  text-align:center;
  margin-top:50px;
}
</style>
</head>

<body>

<section class="container">

  <div class="page-title">
    <h1>System Reports</h1>
  </div>

  <!-- =============================
       SUMMARY SECTION
  ============================== -->
  <div class="summary">
    <div class="summary-card">
      <h3>Total Pantun</h3>
      <span><?= $totalPantun ?></span>
    </div>
    <div class="summary-card">
      <h3>Total Music</h3>
      <span><?= $totalMusic ?></span>
    </div>
    <div class="summary-card">
      <h3>Pantun Sent</h3>
      <span><?= $totalSent ?></span>
    </div>
  </div>

  <!-- =============================
       PANTUN BY THEME (BAR CHART)
  ============================== -->
  <div class="section">
    <h2>Pantun Distribution by Theme</h2>
    <div class="chart-card">
      <canvas id="themeChart"></canvas>
    </div>
  </div>

  <!-- =============================
       MUSIC USAGE (PIE CHART)
  ============================== -->
  <div class="section">
    <h2>Top Background Music Usage</h2>
    <div class="chart-card">
      <canvas id="musicChart"></canvas>
    </div>
  </div>

  <div class="back-link">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>

</section>

<script>
/* === PANTUN THEME BAR CHART === */
new Chart(document.getElementById('themeChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($themeData) ?>,
    datasets: [{
      label: 'Total Pantun',
      data: <?= json_encode($themeCount) ?>,
      backgroundColor: '#c9a24d'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    }
  }
});

/* === MUSIC PIE CHART === */
new Chart(document.getElementById('musicChart'), {
  type: 'pie',
  data: {
    labels: <?= json_encode($musicData) ?>,
    datasets: [{
      data: <?= json_encode($musicCount) ?>,
      backgroundColor: [
        '#c9a24d','#e0c68c','#bfa76f','#d8c090','#a58f5f'
      ]
    }]
  },
  options: {
    responsive: true
  }
});
</script>

</body>
</html>





