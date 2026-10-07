<?php
require_once 'config.php';

$search = trim($_GET['search'] ?? '');
$type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? '';
$location = trim($_GET['location'] ?? '');
$fromDate = $_GET['from_date'] ?? '';
$params = [];
$where = [];

if ($search !== '') {
    $where[] = '(title LIKE :search OR description LIKE :search OR location LIKE :search)';
    $params['search'] = '%' . $search . '%';
}
if (in_array($type, ['lost', 'found'], true)) {
    $where[] = 'item_type = :type';
    $params['type'] = $type;
}
if ($category !== '') {
    $where[] = 'category = :category';
    $params['category'] = $category;
}
if ($location !== '') { $where[] = 'location LIKE :location'; $params['location'] = '%' . $location . '%'; }
if ($fromDate !== '') { $where[] = 'item_date >= :from_date'; $params['from_date'] = $fromDate; }

$sql = 'SELECT * FROM items';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC';
$statement = db()->prepare($sql);
$statement->execute($params);
$items = $statement->fetchAll(PDO::FETCH_ASSOC);
$categories = db()->query('SELECT DISTINCT category FROM items ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$summary = db()->query("SELECT COUNT(*) AS total, SUM(status = 'open') AS open_items, SUM(status = 'returned') AS returned_items FROM items")->fetch(PDO::FETCH_ASSOC);
$recentReturned = db()->query("SELECT id, title, category, location FROM items WHERE status = 'returned' ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Browse Items';
require 'header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Community item board</p>
        <h1>Find what was lost.<br>Return what was found.</h1>
        <p>Browse reported items or help someone by reporting an item you found.</p>
    </div>
    <a class="button" href="add_item.php">+ Report an item</a>
</section>

<?php if (isset($_GET['reported'])): ?><div class="alert success">Your item report has been submitted successfully.</div><?php endif; ?>

<section class="home-stats" aria-label="Community statistics">
    <div><strong><?= $summary['total'] ?></strong><span>Total reports</span></div>
    <div><strong><?= $summary['open_items'] ?></strong><span>Items still open</span></div>
    <div><strong><?= $summary['returned_items'] ?></strong><span>Items returned</span></div>
</section>

<section class="category-section"><div class="section-heading"><div><p class="eyebrow">Browse faster</p><h2>Explore by category</h2></div></div><div class="category-links">
<?php foreach (array_slice($categories, 0, 6) as $itemCategory): ?><a href="index.php?category=<?= rawurlencode($itemCategory) ?>"><span><?= htmlspecialchars($itemCategory) ?></span><b>→</b></a><?php endforeach; ?>
</div></section>

<form class="filters" method="get">
    <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, place, or description">
    <select name="type">
        <option value="">All types</option>
        <option value="lost" <?= $type === 'lost' ? 'selected' : '' ?>>Lost</option>
        <option value="found" <?= $type === 'found' ? 'selected' : '' ?>>Found</option>
    </select>
    <select name="category">
        <option value="">All categories</option>
        <?php foreach ($categories as $itemCategory): ?>
            <option value="<?= htmlspecialchars($itemCategory) ?>" <?= $category === $itemCategory ? 'selected' : '' ?>><?= htmlspecialchars($itemCategory) ?></option>
        <?php endforeach; ?>
    </select>
    <input name="location" value="<?= htmlspecialchars($location) ?>" placeholder="Location">
    <input type="date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>" title="Reported after">
    <button class="button" type="submit">Search</button>
    <?php if ($search || $type || $category || $location || $fromDate): ?><a class="clear" href="index.php">Clear</a><?php endif; ?>
</form>

<p class="result-count"><?= count($items) ?> item<?= count($items) === 1 ? '' : 's' ?> found</p>
<section class="item-grid">
    <?php foreach ($items as $item): ?>
        <article class="item-card">
            <?php if ($item['image_path']): ?><img class="item-photo" src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"><?php endif; ?>
            <div class="card-top"><span class="badge <?= $item['item_type'] ?>"><?= ucfirst($item['item_type']) ?></span><span class="status <?= $item['status'] ?>"><?= ucfirst($item['status']) ?></span></div>
            <p class="category"><?= htmlspecialchars($item['category']) ?></p>
            <h2><?= htmlspecialchars($item['title']) ?></h2>
            <p class="muted">📍 <?= htmlspecialchars($item['location']) ?></p>
            <p class="muted">📅 <?= date('d M Y', strtotime($item['item_date'])) ?></p>
            <a href="item.php?id=<?= $item['id'] ?>">View details <span>→</span></a>
        </article>
    <?php endforeach; ?>
    <?php if (!$items): ?><p class="empty">No items match your search. Try different keywords or <a href="add_item.php">report an item</a>.</p><?php endif; ?>
</section>
<?php if ($recentReturned): ?><section class="returned-section"><div class="section-heading"><div><p class="eyebrow">Good news</p><h2>Recently returned</h2></div></div><div class="returned-grid"><?php foreach ($recentReturned as $returned): ?><a href="item.php?id=<?= $returned['id'] ?>"><span class="status returned">Returned</span><strong><?= htmlspecialchars($returned['title']) ?></strong><small><?= htmlspecialchars($returned['category']) ?> · 📍 <?= htmlspecialchars($returned['location']) ?></small></a><?php endforeach; ?></div></section><?php endif; ?>
<?php require 'footer.php'; ?>
