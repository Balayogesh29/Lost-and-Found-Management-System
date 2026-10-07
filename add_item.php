<?php
require_once 'auth.php';
requireLogin();
$errors = [];
$values = ['item_type' => 'lost', 'title' => '', 'category' => '', 'description' => '', 'location' => '', 'item_date' => '', 'contact_name' => '', 'contact_email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    foreach ($values as $field => $default) {
        $values[$field] = trim($_POST[$field] ?? $default);
    }
    if (!in_array($values['item_type'], ['lost', 'found'], true)) $errors[] = 'Choose whether the item is lost or found.';
    if ($values['title'] === '') $errors[] = 'Item name is required.';
    if ($values['category'] === '') $errors[] = 'Category is required.';
    if ($values['description'] === '') $errors[] = 'Description is required.';
    if ($values['location'] === '') $errors[] = 'Location is required.';
    if ($values['item_date'] === '') $errors[] = 'Date is required.';
        

    $imagePath = null;
    if (!$errors && isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || !isset($allowedTypes[$mime])) $errors[] = 'Upload a JPG, PNG, or WEBP image.';
        elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) $errors[] = 'Image must be 3 MB or smaller.';
        else { $fileName = bin2hex(random_bytes(12)) . '.' . $allowedTypes[$mime]; if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $fileName)) $imagePath = 'uploads/' . $fileName; else $errors[] = 'Image upload failed.'; }
    }
    if (!$errors) {
        $values['user_id'] = currentUser()['id']; $values['image_path'] = $imagePath; $values['contact_name'] = currentUser()['name']; $values['contact_email'] = currentUser()['email'];
        $query = 'INSERT INTO items (user_id, item_type, title, category, description, image_path, location, item_date, contact_name, contact_email) VALUES (:user_id, :item_type, :title, :category, :description, :image_path, :location, :item_date, :contact_name, :contact_email)';
        db()->prepare($query)->execute($values);
        header('Location: index.php?reported=1');
        exit;
    }
}
$pageTitle = 'Report an Item';
require 'header.php';
?>
<section class="form-page">
    <p class="eyebrow">New report</p><h1>Report a lost or found item</h1>
    <p class="intro">Provide enough detail for the owner or finder to recognize the item.</p>
    <?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $error): ?><div><?= htmlspecialchars($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form class="item-form" method="post" enctype="multipart/form-data"><?= csrfInput() ?>
        <fieldset><legend>Item type</legend><label><input type="radio" name="item_type" value="lost" <?= $values['item_type'] === 'lost' ? 'checked' : '' ?>> Lost item</label><label><input type="radio" name="item_type" value="found" <?= $values['item_type'] === 'found' ? 'checked' : '' ?>> Found item</label></fieldset>
        <div class="two-column"><label>Item name<input required name="title" value="<?= htmlspecialchars($values['title']) ?>" placeholder="e.g. Black wallet"></label><label>Category<select required name="category"><option value="">Select category</option><?php foreach (['Electronics', 'Documents', 'Keys', 'Bags', 'Clothing', 'Accessories', 'Other'] as $option): ?><option <?= $values['category'] === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach; ?></select></label></div>
        <label>Description<textarea required name="description" rows="5" placeholder="Describe color, brand, identifying marks, or contents."><?= htmlspecialchars($values['description']) ?></textarea></label>
        <label>Item photo <span class="optional">(optional, JPG/PNG/WEBP, max 3 MB)</span><input data-image-input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
        <img data-image-preview class="upload-preview" alt="Selected item preview" hidden>
        <div class="two-column"><label>Location<input required name="location" value="<?= htmlspecialchars($values['location']) ?>" placeholder="e.g. Library, second floor"></label><label>Date<input required type="date" name="item_date" value="<?= htmlspecialchars($values['item_date']) ?>"></label></div>
        <h2>Contact details</h2><p class="muted">Your signed-in account details will be used for this report.</p>
        <div class="form-actions"><a href="index.php">Cancel</a><button class="button" type="submit">Submit report</button></div>
    </form>
</section>
<?php require 'footer.php'; ?>
