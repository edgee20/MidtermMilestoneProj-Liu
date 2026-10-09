<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
requirePost();
$id = positiveId($_POST['recipe_id'] ?? null);
if (!$id) { fail(400, 'Invalid recipe ID.'); }
if (!$recipes->find($id)) { fail(404, 'Recipe not found.'); }
if (!$recipes->delete($id, userId())) { fail(403, 'Only the author can delete this recipe.'); }
flash('Recipe deleted.'); redirect('index.php');
