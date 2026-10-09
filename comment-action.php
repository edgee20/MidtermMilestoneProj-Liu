<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
requirePost();
$action = textInput($_POST, 'action');
if (!in_array($action, ['add', 'edit', 'delete'], true)) { fail(400, 'Invalid comment action.'); }
if ($action === 'add') {
    $recipeId = positiveId($_POST['recipe_id'] ?? null);
    if (!$recipeId) { fail(400, 'Invalid recipe ID.'); }
    if (!$recipes->find($recipeId)) { fail(404, 'Recipe not found.'); }
} else {
    $commentId = positiveId($_POST['comment_id'] ?? null);
    if (!$commentId) { fail(400, 'Invalid comment ID.'); }
    $comment = $comments->find($commentId);
    if (!$comment) { fail(404, 'Comment not found.'); }
    if (!$comments->isOwner($commentId, userId())) { fail(403, 'Only the author can change this comment.'); }
    $recipeId = (int) $comment['recipe_id'];
}
$content = textInput($_POST, 'content');
if ($action !== 'delete' && (lengthOf($content) < 1 || lengthOf($content) > 1000)) {
    fail(422, 'Comment must have 1-1,000 characters. Go back to correct it.');
}
if ($action === 'add') { $comments->add($recipeId, userId(), $content); }
elseif ($action === 'edit') { $comments->update($commentId, userId(), $content); }
else { $comments->delete($commentId, userId()); }
flash($action === 'delete' ? 'Comment deleted.' : 'Comment saved.');
redirect('recipe.php?id=' . $recipeId);
