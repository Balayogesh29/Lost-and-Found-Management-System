<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function currentUser(): ?array { return $_SESSION['user'] ?? null; }
function isLoggedIn(): bool { return currentUser() !== null; }
function isAdmin(): bool { return isLoggedIn() && currentUser()['role'] === 'admin'; }
function requireLogin(): void { if (!isLoggedIn()) { header('Location: login.php?required=1'); exit; } }
function requireAdmin(): void { if (!isAdmin()) { header('Location: index.php'); exit; } }
function csrfToken(): string { if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function csrfInput(): string { return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">'; }
function requireCsrf(): void { if (!isset($_POST['csrf_token']) || !hash_equals(csrfToken(), $_POST['csrf_token'])) { http_response_code(419); exit('This form has expired. Please return to the page and try again.'); } }
