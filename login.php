<?php
require_once 'auth.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $email = trim($_POST['email'] ?? ''); $password = $_POST['password'] ?? '';
    $statement = db()->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $statement->execute([$email]); $user = $statement->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']); $_SESSION['user'] = $user; header('Location: ' . (isAdmin() ? 'admin.php' : 'my_reports.php')); exit;
    }
    $error = 'Email or password is incorrect.';
}
$pageTitle = 'Sign in'; require 'header.php';
?>
<section class="auth-page"><div class="auth-card"><p class="eyebrow">Welcome back</p><h1>Sign in</h1>
<?php if (isset($_GET['required'])): ?><div class="alert error">Please sign in to report an item.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
<form class="item-form" method="post"><?= csrfInput() ?><label>Email<input required type="email" name="email"></label><label>Password<input required type="password" name="password"></label><button class="button full" type="submit">Sign in</button></form>
<p>New here? <a href="register.php">Create an account</a>.</p></div></section>
<?php require 'footer.php'; ?>
