<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
$id = positiveId($_GET['id'] ?? null);
if (!$id) { fail(400, 'Invalid recipe ID.'); }
$item = $recipes->find($id);
if (!$item) { fail(404, 'Recipe not found.'); }
$ingredients = $recipes->ingredients($id); $isSaved = $favorites->exists(userId(), $id);
$pageTitle = $item['title']; require __DIR__ . '/includes/header.php';
?>
<a class="back-link" href="index.php">← All recipes</a>
<article class="panel detail">
<span class="tag"><?= e($item['category']) ?></span><h1><?= e($item['title']) ?></h1>
<p><?= e($item['description']) ?></p>
<p class="meta">By <?= e($item['author']) ?> · <?= e(dateLabel($item['created_at'])) ?> · <?= readMinutes($item, $ingredients) ?> min read<?= $item['is_edited'] ? ' · Edited' : '' ?></p>
<div class="actions">
<button class="secondary" type="button" data-favorite data-id="<?= $id ?>" data-csrf="<?= e(csrfToken()) ?>" aria-pressed="<?= $isSaved ? 'true' : 'false' ?>"><?= $isSaved ? 'Unsave' : 'Save' ?></button>
<?php if ((int) $item['user_id'] === userId()): ?>
<a href="edit-recipe.php?id=<?= $id ?>">Edit recipe</a>
<form action="delete-recipe.php" method="post" data-confirm="Delete this recipe and its comments?">
<?php csrfField(); ?><input type="hidden" name="recipe_id" value="<?= $id ?>"><button class="danger">Delete recipe</button></form>
<?php endif; ?></div><p class="favorite-status meta" role="status"></p>
<div class="recipe-body">
<section><h2>Ingredients</h2><ul><?php foreach ($ingredients as $ingredient): ?><li><?= e($ingredient) ?></li><?php endforeach; ?></ul></section>
<section><h2>Cooking instructions</h2><div class="instructions"><?= nl2br(e($item['instructions'])) ?></div></section>
</div></article>
<section class="panel comments"><h2>Community comments</h2>
<form action="comment-action.php" method="post">
<?php csrfField(); ?><input type="hidden" name="action" value="add"><input type="hidden" name="recipe_id" value="<?= $id ?>">
<label>Your comment<textarea name="content" rows="3" maxlength="1000" required></textarea></label><button>Post comment</button></form>
<?php $recipeComments = $comments->all($id); if (!$recipeComments): ?><p class="muted">Be the first to leave a cooking tip.</p><?php endif; ?>
<?php foreach ($recipeComments as $comment): ?>
<article class="comment">
<p class="meta"><strong><?= e($comment['author']) ?></strong> · <?= e(dateLabel($comment['created_at'])) ?><?= $comment['is_edited'] ? ' · Edited' : '' ?></p>
<p><?= nl2br(e($comment['content'])) ?></p>
<?php if ((int) $comment['user_id'] === userId()): ?>
<details><summary>Edit comment</summary>
<form action="comment-action.php" method="post">
<?php csrfField(); ?><input type="hidden" name="action" value="edit"><input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
<label>Comment<textarea name="content" maxlength="1000" rows="3" required><?= e($comment['content']) ?></textarea></label><button class="secondary">Save changes</button>
</form></details>
<form action="comment-action.php" method="post" data-confirm="Delete this comment?">
<?php csrfField(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="comment_id" value="<?= $comment['id'] ?>"><button class="link-button danger-text">Delete comment</button>
</form>
<?php endif; ?></article>
<?php endforeach; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
