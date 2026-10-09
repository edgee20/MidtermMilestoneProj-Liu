<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
$data = ['title' => '', 'description' => '', 'category_id' => 0, 'ingredients' => [''], 'instructions' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePost(); $data = recipeInput($_POST); $errors = recipeErrors($data, $categories);
    if (!$errors) {
        $data['ingredients'] = array_map('trim', $data['ingredients']);
        $id = $recipes->create(userId(), $data);
        flash('Your recipe has been shared.'); redirect('recipe.php?id=' . $id);
    }
}
$pageTitle = 'Share a recipe';
require __DIR__ . '/includes/header.php'; require __DIR__ . '/includes/recipe-form.php'; require __DIR__ . '/includes/footer.php';
