<?php
require_once 'auth.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit; }
$statement = db()->prepare('SELECT * FROM items WHERE id = ?');
$statement->execute([$id]);
$item = $statement->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); $pageTitle = 'Item not found'; require 'header.php'; echo '<p class="empty">Item not found. <a href="index.php">Return to the list</a>.</p>'; require 'footer.php'; exit; }
$canManage = isAdmin() || (isLoggedIn() && (int)$item['user_id'] === (int)currentUser()['id']);
$related = db()->prepare('SELECT id, title, item_type, category, location FROM items WHERE id != ? AND status = "open" AND (category = ? OR location = ?) ORDER BY created_at DESC LIMIT 3');
$related->execute([$id, $item['category'], $item['location']]);
$relatedItems = $related->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $item['status'] === 'open' && $canManage) { requireCsrf(); db()->prepare("UPDATE items SET status = 'returned' WHERE id = ?")->execute([$id]); header('Location: item.php?id=' . $id . '&updated=1'); exit; }
$pageTitle = $item['title'];
require 'header.php';
?>
<a class="back" href="index.php">← Back to all items</a>
<article class="detail-card">
    <div class="card-top"><span class="badge <?= $item['item_type'] ?>"><?= ucfirst($item['item_type']) ?> item</span><span class="status <?= $item['status'] ?>"><?= ucfirst($item['status']) ?></span></div>
    <?php if (isset($_GET['updated'])): ?><div class="alert success">This item has been marked as returned.</div><?php endif; ?>
    <?php if (isset($_GET['edited'])): ?><div class="alert success">Your changes have been saved.</div><?php endif; ?>
    <?php if (isset($_GET['claimed'])): ?><div class="alert success">Your claim is pending review by an administrator.</div><?php endif; ?>
    <p class="category"><?= htmlspecialchars($item['category']) ?></p><h1><?= htmlspecialchars($item['title']) ?></h1>
    <?php if ($item['image_path']): ?><img class="detail-photo" src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"><?php endif; ?>
    <div class="detail-meta"><div><strong>Location</strong><span>📍 <?= htmlspecialchars($item['location']) ?></span></div><div><strong>Date</strong><span>📅 <?= date('d M Y', strtotime($item['item_date'])) ?></span></div></div>
    <h2>Description</h2><p class="description"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    <?php if ($canManage): ?>
        <section class="contact-box"><h2>Contact</h2><p>Reported by <strong><?= htmlspecialchars($item['contact_name']) ?></strong></p><a class="button" href="mailto:<?= htmlspecialchars($item['contact_email']) ?>?subject=Regarding%20<?= rawurlencode($item['title']) ?>">Email reporter</a></section>
        <div class="manage-links"><a href="edit_item.php?id=<?= $item['id'] ?>">Edit report</a><form method="post" action="delete_item.php" onsubmit="return confirm('Delete this report?')"><?= csrfInput() ?><input type="hidden" name="id" value="<?= $item['id'] ?>"><button type="submit">Delete report</button></form></div>
    <?php elseif ($item['status'] === 'open' && isLoggedIn()): ?>
        <section class="contact-box"><h2>Think this is yours?</h2><p>Submit a claim with proof of ownership. An administrator will review it before sharing contact details.</p><a class="button" href="claim.php?item_id=<?= $item['id'] ?>">Submit a claim</a></section>
    <?php elseif ($item['status'] === 'open'): ?>
        <section class="contact-box"><h2>Think this is yours?</h2><p>Sign in to submit a protected claim request for this item.</p><a class="button" href="login.php?required=1">Sign in to claim</a></section>
    <?php endif; ?>
    <?php if ($item['status'] === 'open' && $canManage): ?><form class="return-form" method="post" onsubmit="return confirm('Mark this item as returned?')"><?= csrfInput() ?><button class="outline-button" type="submit">Mark as returned</button></form><?php endif; ?>
</article>
<?php if ($relatedItems): ?><section class="related-items"><h2>Related reports</h2><div class="related-grid"><?php foreach ($relatedItems as $relatedItem): ?><a href="item.php?id=<?= $relatedItem['id'] ?>"><span class="badge <?= $relatedItem['item_type'] ?>"><?= ucfirst($relatedItem['item_type']) ?></span><strong><?= htmlspecialchars($relatedItem['title']) ?></strong><small>📍 <?= htmlspecialchars($relatedItem['location']) ?></small></a><?php endforeach; ?></div></section><?php endif; ?>
<?php require 'footer.php'; ?>
