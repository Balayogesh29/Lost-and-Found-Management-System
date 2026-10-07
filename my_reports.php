<?php
require_once 'auth.php'; requireLogin();
$statement = db()->prepare('SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC'); $statement->execute([currentUser()['id']]); $items = $statement->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'My Reports'; require 'header.php';
?>
<section class="page-heading"><div><p class="eyebrow">Your account</p><h1>My reports</h1><p class="intro">Track and manage the items you have reported.</p></div><a class="button" href="add_item.php">+ Report an item</a></section>
<?php if (isset($_GET['deleted'])): ?><div class="alert success">The report was deleted.</div><?php endif; ?>
<section class="item-grid">
<?php foreach ($items as $item): ?><article class="item-card"><?php if ($item['image_path']): ?><img class="item-photo" src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"><?php endif; ?><div class="card-top"><span class="badge <?= $item['item_type'] ?>"><?= ucfirst($item['item_type']) ?></span><span class="status <?= $item['status'] ?>"><?= ucfirst($item['status']) ?></span></div><p class="category"><?= htmlspecialchars($item['category']) ?></p><h2><?= htmlspecialchars($item['title']) ?></h2><a href="item.php?id=<?= $item['id'] ?>">View details <span>→</span></a><div class="card-actions"><a href="edit_item.php?id=<?= $item['id'] ?>">Edit</a><form method="post" action="delete_item.php" onsubmit="return confirm('Delete this report?')"><?= csrfInput() ?><input type="hidden" name="id" value="<?= $item['id'] ?>"><button type="submit">Delete</button></form></div></article><?php endforeach; ?>
<?php if (!$items): ?><p class="empty">You have not reported any items yet. <a href="add_item.php">Report your first item</a>.</p><?php endif; ?></section>
<?php require 'footer.php'; ?>
