<?php
require_once 'auth.php';
requireLogin();
$itemId = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);
if (!$itemId) { header('Location: index.php'); exit; }
$statement = db()->prepare('SELECT * FROM items WHERE id = ?'); $statement->execute([$itemId]); $item = $statement->fetch(PDO::FETCH_ASSOC);
if (!$item || $item['status'] !== 'open') { header('Location: index.php'); exit; }
if ((int)$item['user_id'] === (int)currentUser()['id']) { header('Location: item.php?id=' . $itemId); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $message = trim($_POST['message'] ?? '');
    if (strlen($message) < 15) $error = 'Please provide at least a short description proving your connection to the item.';
    else {
        try { db()->prepare('INSERT INTO claims (item_id, user_id, message) VALUES (?, ?, ?)')->execute([$itemId, currentUser()['id'], $message]); header('Location: item.php?id=' . $itemId . '&claimed=1'); exit; }
        catch (PDOException $e) { $error = 'You have already submitted a claim for this item.'; }
    }
}
$pageTitle = 'Submit Claim'; require 'header.php';
?>
<section class="auth-page"><div class="auth-card"><p class="eyebrow">Claim request</p><h1>Claim “<?= htmlspecialchars($item['title']) ?>”</h1><p class="intro">Describe proof of ownership without sharing sensitive personal information.</p>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form class="item-form" method="post"><?= csrfInput() ?><label>Why do you believe this item is yours?<textarea required name="message" rows="6" minlength="15" placeholder="For example: describe a unique mark, the contents, or when you lost it."></textarea></label><div class="form-actions"><a href="item.php?id=<?= $itemId ?>">Cancel</a><button class="button" type="submit">Submit claim</button></div></form></div></section>
<?php require 'footer.php'; ?>
