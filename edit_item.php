<?php
require_once 'auth.php'; requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); if (!$id) { header('Location: my_reports.php'); exit; }
$statement = db()->prepare('SELECT * FROM items WHERE id = ?'); $statement->execute([$id]); $item = $statement->fetch(PDO::FETCH_ASSOC);
if (!$item || (!isAdmin() && (int)$item['user_id'] !== (int)currentUser()['id'])) { header('Location: my_reports.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $itemType = $_POST['item_type'] ?? ''; $title = trim($_POST['title'] ?? ''); $category = trim($_POST['category'] ?? ''); $description = trim($_POST['description'] ?? ''); $location = trim($_POST['location'] ?? ''); $itemDate = $_POST['item_date'] ?? '';
    if (!in_array($itemType, ['lost', 'found'], true) || !$title || !$category || !$description || !$location || !$itemDate) $errors[] = 'Complete every required field.';
    if (!$errors) { db()->prepare('UPDATE items SET item_type=?, title=?, category=?, description=?, location=?, item_date=? WHERE id=?')->execute([$itemType, $title, $category, $description, $location, $itemDate, $id]); header('Location: item.php?id=' . $id . '&edited=1'); exit; }
    $item = array_merge($item, ['item_type'=>$itemType, 'title'=>$title, 'category'=>$category, 'description'=>$description, 'location'=>$location, 'item_date'=>$itemDate]);
}
$pageTitle = 'Edit Item'; require 'header.php';
?>
<section class="form-page"><p class="eyebrow">Manage report</p><h1>Edit item</h1><?php if ($errors): ?><div class="alert error"><?= htmlspecialchars($errors[0]) ?></div><?php endif; ?>
<form class="item-form" method="post"><?= csrfInput() ?><fieldset><legend>Item type</legend><label><input type="radio" name="item_type" value="lost" <?= $item['item_type']==='lost'?'checked':'' ?>> Lost item</label><label><input type="radio" name="item_type" value="found" <?= $item['item_type']==='found'?'checked':'' ?>> Found item</label></fieldset><div class="two-column"><label>Item name<input required name="title" value="<?= htmlspecialchars($item['title']) ?>"></label><label>Category<input required name="category" value="<?= htmlspecialchars($item['category']) ?>"></label></div><label>Description<textarea required name="description" rows="5"><?= htmlspecialchars($item['description']) ?></textarea></label><div class="two-column"><label>Location<input required name="location" value="<?= htmlspecialchars($item['location']) ?>"></label><label>Date<input required type="date" name="item_date" value="<?= htmlspecialchars($item['item_date']) ?>"></label></div><div class="form-actions"><a href="item.php?id=<?= $id ?>">Cancel</a><button class="button" type="submit">Save changes</button></div></form></section>
<?php require 'footer.php'; ?>
