<?php declare(strict_types=1); ?>
<div class="recipe-grid" <?= $onFavoritesPage ? 'data-favorites-page' : '' ?>>
<?php foreach ($items as $item): ?>
    <article class="panel recipe-card" data-recipe-card>
        <span class="tag"><?= e($item['category']) ?></span>
        <h2><a href="recipe.php?id=<?= $item['id'] ?>"><?= e($item['title']) ?></a></h2>
        <p class="description"><?= e($item['description']) ?></p>
        <p class="meta">By <?= e($item['author']) ?> · <?= e(dateLabel($item['created_at'])) ?><?= $item['is_edited'] ? ' · Edited' : '' ?></p>
        <div class="actions">
            <a href="recipe.php?id=<?= $item['id'] ?>">View recipe →</a>
            <button class="secondary" type="button" data-favorite data-id="<?= $item['id'] ?>" data-csrf="<?= e(csrfToken()) ?>" aria-pressed="<?= $item['is_favorited'] ? 'true' : 'false' ?>"><?= $item['is_favorited'] ? 'Unsave' : 'Save' ?></button>
        </div>
        <p class="favorite-status meta" role="status"></p>
    </article>
<?php endforeach; ?>
</div>
<section class="panel empty-state" data-empty-favorites <?= $items ? 'hidden' : '' ?>>
    <h2><?= $onFavoritesPage ? 'Your favorites start here' : 'No recipes found' ?></h2>
    <p><?= $onFavoritesPage ? 'Save a recipe you like and come back to it whenever you are ready to cook.' : 'No recipes match right now. Try another search or share something from your kitchen.' ?></p>
    <a class="button-link" href="<?= $onFavoritesPage ? 'index.php' : 'create-recipe.php' ?>"><?= $onFavoritesPage ? 'Browse recipes' : 'Share a recipe' ?></a>
</section>
