<?php
require_once 'auth.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) $error = 'Enter your name, a valid email, and a password with at least 6 characters.';
    else {
        try { db()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)')->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]); }
        catch (PDOException $e) { $error = 'That email address is already registered.'; }
        if (!$error) { $_SESSION['user'] = ['id' => db()->lastInsertId(), 'name' => $name, 'email' => $email, 'role' => 'user']; header('Location: my_reports.php'); exit; }
    }
}
$pageTitle = 'Create account'; require 'header.php';
?>
<section class="auth-page"><div class="auth-card"><p class="eyebrow">Join the community</p><h1>Create an account</h1><?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
<form class="item-form" method="post"><?= csrfInput() ?><label>Name<input required name="name"></label><label>Email<input required type="email" name="email"></label><label>Password<input required type="password" name="password" minlength="6" placeholder="At least 6 characters"></label><button class="button full" type="submit">Create account</button></form><p>Already registered? <a href="login.php">Sign in</a>.</p></div></section>
<?php require 'footer.php'; ?>
