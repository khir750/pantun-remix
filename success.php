<?php
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pantun Sent</title>
<meta http-equiv="refresh" content="3;url=historypantun.php">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background:#faf7f2;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}
.box {
  background:#fff;
  padding:40px;
  border-radius:16px;
  border:1px solid #eee6da;
  text-align:center;
}
</style>
</head>
<body>

<div class="box">
  <h2>✨ Pantun berjaya dihantar</h2>
  <p>Anda akan dibawa ke halaman sejarah.</p>
</div>

</body>
</html>
