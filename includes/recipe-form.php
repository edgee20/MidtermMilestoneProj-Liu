<?php declare(strict_types=1); ?>
<section class="panel form-panel"><p class="eyebrow">From your kitchen</p><h1><?= e($pageTitle) ?></h1>
<p>Write the ingredients and steps so a neighbor can follow along.</p>
<?php showErrors($errors); ?>
<form method="post"><?php csrfField(); ?>
<label>Recipe title<input name="title" minlength="3" maxlength="150" value="<?= e($data['title']) ?>" required></label>
<label>Short description<textarea name="description" minlength="10" maxlength="500" rows="3" required><?= e($data['description']) ?></textarea></label>
<label>Category<select name="category_id" required><option value="">Choose a category</option>
<?php foreach ($categories->all() as $category): ?>
<option value="<?= $category['id'] ?>" <?= (int) $data['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
<?php endforeach; ?></select></label>
<fieldset><legend>Ingredients</legend><small>Include amounts, for example: 2 tablespoons soy sauce. Maximum 30 ingredients.</small>
<div data-ingredients>
<?php foreach (array_slice($data['ingredients'] ?: [''], 0, 30) as $ingredient): ?>
<div class="ingredient-row"><label>Ingredient<input name="ingredients[]" maxlength="255" value="<?= e(is_string($ingredient) ? $ingredient : '') ?>" required></label>
<button type="button" class="secondary" data-remove-ingredient>Remove</button></div>
<?php endforeach; ?></div>
<button type="button" class="secondary" data-add-ingredient>Add ingredient</button>
<noscript><p>Enable JavaScript to add separate ingredient fields.</p></noscript>
</fieldset>
<label>Cooking instructions<textarea name="instructions" minlength="10" maxlength="10000" rows="8" placeholder="1. Prepare the ingredients.&#10;2. Cook and serve." required><?= e($data['instructions']) ?></textarea></label>
<div class="actions"><button>Save recipe</button><a href="<?= isset($id) ? 'recipe.php?id=' . $id : 'index.php' ?>">Cancel</a></div>
</form></section>
