<?php
require_once 'auth.php';
$pageTitle = $pageTitle ?? 'Lost & Found';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Lost & Found</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="index.php">Lost<span>&amp;</span>Found</a>
        <nav>
            <a href="index.php">Browse items</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= isAdmin() ? 'admin.php' : 'my_reports.php' ?>"><?= isAdmin() ? 'Dashboard' : 'My reports' ?></a>
                <?php if (!isAdmin()): ?><a href="my_claims.php">My claims</a><?php endif; ?>
                <form class="logout-form" method="post" action="logout.php"><?= csrfInput() ?><button type="submit">Sign out</button></form>
            <?php else: ?>
                <a href="login.php">Sign in</a>
            <?php endif; ?>
            <a class="button button-small" href="add_item.php">Report an item</a>
        </nav>
    </div>
</header>
<main class="container">
