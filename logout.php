<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
requireCsrf();
$_SESSION = []; session_destroy(); header('Location: index.php'); exit;
