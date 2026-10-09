<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
$items = $favorites->forUser(userId());
$pageTitle = 'My favorites'; require __DIR__ . '/includes/header.php';
?>
<section class="intro"><p class="eyebrow">Keep these close</p><h1>My favorites</h1><p>Your saved recipes, ready for the next meal.</p></section>
<?php $onFavoritesPage = true; require __DIR__ . '/includes/recipe-list.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
