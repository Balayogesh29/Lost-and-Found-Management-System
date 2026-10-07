<?php
require_once 'auth.php'; requireLogin();
$statement = db()->prepare("SELECT claims.*, items.title, items.category FROM claims JOIN items ON items.id = claims.item_id WHERE claims.user_id = ? ORDER BY claims.created_at DESC");
$statement->execute([currentUser()['id']]); $claims = $statement->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'My Claims'; require 'header.php';
?>
<section class="page-heading"><div><p class="eyebrow">Your account</p><h1>My claims</h1><p class="intro">Follow the status of every item you have claimed.</p></div></section>
<section class="admin-table claims-list"><div class="table-wrap"><table><thead><tr><th>Item</th><th>Submitted</th><th>Status</th><th>Administrator note</th><th></th></tr></thead><tbody>
<?php foreach ($claims as $claim): ?><tr><td><strong><?= htmlspecialchars($claim['title']) ?></strong><small><?= htmlspecialchars($claim['category']) ?></small></td><td><?= date('d M Y', strtotime($claim['created_at'])) ?></td><td><span class="status <?= $claim['status'] ?>"><?= ucfirst($claim['status']) ?></span></td><td><?= $claim['admin_note'] ? htmlspecialchars($claim['admin_note']) : '<span class="muted">No note yet</span>' ?></td><td><a href="item.php?id=<?= $claim['item_id'] ?>">View item</a></td></tr><?php endforeach; ?>
<?php if (!$claims): ?><tr><td colspan="5" class="muted">You have not submitted any claims yet.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'footer.php'; ?>
