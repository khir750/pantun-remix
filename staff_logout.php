<?php
session_start();

/* =============================
   CLEAR STAFF SESSION
============================= */
$_SESSION = [];
session_destroy();

/* =============================
   REDIRECT TO STAFF LOGIN
============================= */
header("Location: staff_login.php");
exit;
