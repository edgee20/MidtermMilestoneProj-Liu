<?php
declare(strict_types=1);
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Recipes') ?> · Lutong Bahay</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
<header class="site-header">
    <nav class="container nav" aria-label="Main navigation">
        <a class="brand" href="index.php">Lutong Bahay<span>A community cookbook</span></a>
        <div class="nav-links">
        <?php if (userId()): ?>
            <a href="index.php" <?= in_array($currentPage, ['index.php', 'recipe.php', 'edit-recipe.php'], true) ? 'aria-current="page"' : '' ?>>Recipes</a>
            <a href="favorites.php" <?= $currentPage === 'favorites.php' ? 'aria-current="page"' : '' ?>>Favorites</a>
            <a class="nav-cta" href="create-recipe.php" <?= $currentPage === 'create-recipe.php' ? 'aria-current="page"' : '' ?>>Share a recipe</a>
            <form action="logout.php" method="post"><?php csrfField(); ?><button class="link-button">Log out</button></form>
        <?php else: ?>
            <a href="login.php" <?= $currentPage === 'login.php' ? 'aria-current="page"' : '' ?>>Log in</a>
            <a href="register.php" <?= $currentPage === 'register.php' ? 'aria-current="page"' : '' ?>>Register</a>
        <?php endif; ?>
        </div>
    </nav>
</header>
<main class="container">
<?php if (isset($_SESSION['flash'])): ?>
    <p class="notice" role="status"><?= e($_SESSION['flash']) ?></p>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
