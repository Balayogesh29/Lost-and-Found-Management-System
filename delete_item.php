<?php
require_once 'auth.php'; requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
requireCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); if (!$id) { header('Location: index.php'); exit; }
$statement = db()->prepare('SELECT user_id FROM items WHERE id = ?'); $statement->execute([$id]); $ownerId = $statement->fetchColumn();
if ($ownerId !== false && (isAdmin() || (int)$ownerId === (int)currentUser()['id'])) db()->prepare('DELETE FROM items WHERE id = ?')->execute([$id]);
header('Location: ' . (isAdmin() ? 'admin.php?deleted=1' : 'my_reports.php?deleted=1')); exit;
