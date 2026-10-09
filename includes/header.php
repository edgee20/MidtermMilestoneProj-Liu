<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Recipes') ?> · Lutong Bahay</title>
<link rel="stylesheet" href="assets/css/style.css"><script src="assets/js/script.js" defer></script>
</head>
<body>
<header class="site-header"><nav class="container nav" aria-label="Main navigation">
<a class="brand" href="index.php">Lutong Bahay<span>Recipes from our community</span></a>
<div class="nav-links">
<?php if (userId()): ?>
<a href="index.php">Recipes</a><a href="favorites.php">Favorites</a><a href="create-recipe.php">Share a recipe</a>
<form action="logout.php" method="post"><?php csrfField(); ?><button class="link-button">Log out</button></form>
<?php else: ?><a href="login.php">Log in</a><a href="register.php">Register</a><?php endif; ?>
</div></nav></header>
<main class="container">
<?php if (isset($_SESSION['flash'])): ?>
<p class="notice" role="status"><?= e($_SESSION['flash']) ?></p>
<?php unset($_SESSION['flash']); endif; ?>
