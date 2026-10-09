<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
$search = textInput($_GET, 'search'); $categoryId = positiveId($_GET['category_id'] ?? null);
if (lengthOf($search) > 150) { fail(400, 'Keep searches within 150 characters.'); }
$items = $recipes->all(userId(), $search, $categoryId);
$pageTitle = 'Community recipes'; require __DIR__ . '/includes/header.php';
?>
<section class="intro"><p class="eyebrow">Our community cookbook</p><h1>Good food, shared at home.</h1>
<p>Welcome, <?= e($_SESSION['name'] ?? '') ?>. Find something to cook or share a recipe of your own.</p></section>
<form method="get" class="panel filters">
<label>Search recipes<input type="search" name="search" maxlength="150" placeholder="Try adobo or sinigang" value="<?= e($search) ?>"></label>
<label>Category<select name="category_id"><option value="">All categories</option>
<?php foreach ($categories->all() as $category): ?>
<option value="<?= $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
<?php endforeach; ?></select></label>
<button>Search</button><a href="index.php">Reset</a></form>
<p class="muted results-count"><?= count($items) ?> recipe<?= count($items) === 1 ? '' : 's' ?> · newest first</p>
<?php $onFavoritesPage = false; require __DIR__ . '/includes/recipe-list.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
