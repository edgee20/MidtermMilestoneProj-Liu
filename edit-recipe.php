<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
$id = positiveId($_GET['id'] ?? null);
if (!$id) { fail(400, 'Invalid recipe ID.'); }
$data = $recipes->find($id);
if (!$data) { fail(404, 'Recipe not found.'); }
if (!$recipes->isOwner($id, userId())) { fail(403, 'Only the author can edit this recipe.'); }
$data['ingredients'] = $recipes->ingredients($id);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePost(); $data = recipeInput($_POST); $errors = recipeErrors($data, $categories);
    if (!$errors) {
        $data['ingredients'] = array_map('trim', $data['ingredients']);
        if (!$recipes->update($id, userId(), $data)) { fail(403, 'Only the author can edit this recipe.'); }
        flash('Recipe saved.'); redirect('recipe.php?id=' . $id);
    }
}
$pageTitle = 'Edit recipe';
require __DIR__ . '/includes/header.php'; require __DIR__ . '/includes/recipe-form.php'; require __DIR__ . '/includes/footer.php';
