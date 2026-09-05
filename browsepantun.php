<?php
session_start();
include "db.php";

$isLoggedIn = isset($_SESSION['user_id']);

$theme     = $_GET['theme'] ?? '';
$recipient = $_GET['recipient'] ?? '';
$page      = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$limit     = 9;
$offset    = ($page - 1) * $limit;
$results   = [];
$totalRows = 0;
$totalPages = 0;
$mode      = 'none';

if (!empty($theme) && empty($recipient)) {
    $mode = 'theme';

    // 1. Get total rows for theme
    $countStmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM pantun_dataset_clean
        WHERE theme = ?
    ");
    $countStmt->bind_param("s", $theme);
    $countStmt->execute();
    $totalRows = $countStmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // 2. Fetch limit/offset results
    $stmt = $conn->prepare("
        SELECT pantun_id, pantun_text, theme
        FROM pantun_dataset_clean
        WHERE theme = ?
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sii", $theme, $limit, $offset);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

else if (!empty($recipient)) {
    $mode = 'recipient';

    $like = "%".$recipient."%";
    // 1. Get total rows for recipient
    $countStmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM sent_pantun
        WHERE recipient LIKE ?
    ");
    $countStmt->bind_param("s", $like);
    $countStmt->execute();
    $totalRows = $countStmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // 2. Fetch limit/offset results
    $stmt = $conn->prepare("
        SELECT recipient, pantun_text, share_token
        FROM sent_pantun
        WHERE recipient LIKE ?
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sii", $like, $limit, $offset);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Helper to preserve params
function getPageUrl($pageNum, $theme, $recipient) {
    $params = [];
    if (!empty($theme)) {
        $params['theme'] = $theme;
    }
    if (!empty($recipient)) {
        $params['recipient'] = $recipient;
    }
    $params['page'] = $pageNum;
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Browse Pantun | PantunLetter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
* { box-sizing:border-box; margin:0; padding:0; }

body {
  font-family:'Poppins',sans-serif;
  background:url("assets/bgfloral.jpeg") repeat;
  background-size:420px auto;
  color:#2e2e2e;
}

.container {
  max-width:900px;
  margin:15px auto;
  padding:0 20px;
}

.page-title {
  text-align:center;
  margin-bottom:12px;
}

h1 {
  font-family:'Playfair Display',serif;
  font-size:28px;
}

.filters {
  display:grid;
  grid-template-columns:1fr 1fr auto;
  gap:15px;
  margin-bottom:15px;
}

select, input {
  padding:14px;
  border-radius:12px;
  border:1px solid #ddd;
  font-family:'Poppins',sans-serif;
}

button {
  padding:14px 22px;
  border:none;
  border-radius:12px;
  background:#1f1f1f;
  color:#fff;
  cursor:pointer;
}

.pantun-grid {
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:16px;
}

.pantun-card {
  background:#fff;
  border:1px solid #eee6da;
  border-radius:16px;
  padding:12px 14px;
  box-shadow:0 8px 18px rgba(0,0,0,.08);
  transition:transform .2s ease, box-shadow .2s ease;
}

.pantun-card:hover {
  transform:translateY(-4px);
  box-shadow:0 14px 26px rgba(0,0,0,.12);
}

.pantun-card p {
  font-family:'Playfair Display',serif;
  font-style:italic;
  font-size:14px;
  line-height:1.45;
  color:#333;
  margin-bottom:8px;
}

.tag {
  display:inline-block;
  font-size:11px;
  background:#f1ede6;
  padding:3px 8px;
  border-radius:20px;
  margin-bottom:6px;
  color:#555;
}

/* Pagination styles consistent with black rounded buttons */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 15px;
  flex-wrap: wrap;
}

.pagination a, .pagination span {
  display: inline-block;
  padding: 10px 18px;
  text-decoration: none;
  background: #1f1f1f;
  color: #fff;
  border-radius: 12px;
  font-size: 14px;
  transition: opacity 0.2s;
}

.pagination a:hover {
  opacity: 0.8;
}

.pagination .active {
  background: #c9a24d;
  color: #fff;
  cursor: default;
}

.pagination .disabled {
  background: #ccc;
  color: #666;
  cursor: not-allowed;
  opacity: 0.6;
}

.page-info {
  text-align: center;
  margin-top: 6px;
  font-size: 12px;
  color: #555;
}

/* Mobile responsiveness media query */
@media (max-width: 768px) {
  .filters {
    grid-template-columns: 1fr;
  }
  .pagination a, .pagination span {
    padding: 8px 14px;
    font-size: 13px;
  }
}
</style>
</head>

<body>

<?php include "header.php"; ?>

<section class="container">

<div class="page-title">
  <h1>Browse Pantun</h1>
</div>

<form method="GET" class="filters">
  <select name="theme">
    <option value="">— Browse by Theme —</option>
    <?php
    $themes = $conn->query("SELECT DISTINCT theme FROM pantun_dataset_clean");
    while ($t = $themes->fetch_assoc()):
      $sel = ($theme === $t['theme'] && empty($recipient)) ? 'selected' : '';
    ?>
      <option value="<?= htmlspecialchars($t['theme']) ?>" <?= $sel ?>>
        <?= htmlspecialchars($t['theme']) ?>
      </option>
    <?php endwhile; ?>
  </select>

  <input type="text"
         name="recipient"
         placeholder="Search by recipient name"
         value="<?= htmlspecialchars($recipient) ?>">

  <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
<div class="pantun-grid">

<?php foreach ($results as $p): ?>

  <!-- ===============================
       MODE A: THEME RESULT
  =============================== -->
  <?php if ($mode === 'theme'): ?>
    <div class="pantun-card">
      <span class="tag"><?= htmlspecialchars($p['theme']) ?></span>

      <p><?= nl2br(htmlspecialchars($p['pantun_text'])) ?></p>

      <?php if ($isLoggedIn): ?>
        <button type="button"
          id="fav-btn-<?= $p['pantun_id'] ?>"
          onclick="addFavourite(<?= $p['pantun_id'] ?>)"
          style="background:none;border:none;font-size:13px;color:#555;cursor:pointer;">
          ♡ Add to Favourite
        </button>
      <?php else: ?>
        <button type="button"
          onclick="requireLogin('save this pantun', 'browsepantun.php')"
          style="background:none;border:none;font-size:13px;color:#555;cursor:pointer;">
          ♡ Add to Favourite
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- ===============================
       MODE B: RECIPIENT RESULT
  =============================== -->
  <?php if ($mode === 'recipient'): ?>
    <div class="pantun-card"
         onclick="window.location.href='details.php?token=<?= urlencode($p['share_token']) ?>'"
         style="cursor:pointer;">

      <span class="tag">
        To: <?= htmlspecialchars($p['recipient']) ?>
      </span>

      <p><?= nl2br(htmlspecialchars($p['pantun_text'])) ?></p>

      <div style="font-size:12px;color:#777;margin-top:8px;">
        Click to view pantun
      </div>
    </div>
  <?php endif; ?>

<?php endforeach; ?>

</div>

<!-- Pagination Controls -->
<?php if ($totalPages > 0): ?>
  <div class="pagination">
    <!-- Previous Link -->
    <?php if ($page > 1): ?>
      <a href="<?= getPageUrl($page - 1, $theme, $recipient) ?>">Previous</a>
    <?php else: ?>
      <span class="disabled">Previous</span>
    <?php endif; ?>

    <!-- Page Numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php if ($i === $page): ?>
        <span class="active"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= getPageUrl($i, $theme, $recipient) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <!-- Next Link -->
    <?php if ($page < $totalPages): ?>
      <a href="<?= getPageUrl($page + 1, $theme, $recipient) ?>">Next</a>
    <?php else: ?>
      <span class="disabled">Next</span>
    <?php endif; ?>
  </div>
  <div class="page-info">
    Page <?= $page ?> of <?= $totalPages ?>
  </div>
<?php endif; ?>
<?php else: ?>
<p style="text-align:center;color:#777;">No pantun found.</p>
<?php endif; ?>

</section>

<?php include "footer.php"; ?>

<script>
function addFavourite(pantunId) {
  fetch("favourite_pantun.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "pantun_id=" + pantunId
  })
  .then(res => res.text())
  .then(response => {
    if (response === "login_required") {
      requireLogin("save this pantun", "browsepantun.php");
      return;
    }

    if (response === "success") {
      alert("✅ Pantun added to favourites");
      const btn = document.getElementById("fav-btn-" + pantunId);
      if (btn) {
        btn.textContent = "❤️ Added";
        btn.disabled = true;
        btn.style.color = "#c9a24d";
        btn.style.cursor = "default";
      }
    }
  });
}

function requireLogin(action, redirectPage) {
  if (confirm("Please login to " + action + ".\n\nGo to login page now?")) {
    window.location.href =
      "user_login.php?redirect=" + encodeURIComponent(redirectPage);
  }
}
</script>

</body>
</html>
