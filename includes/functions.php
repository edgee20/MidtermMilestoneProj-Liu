<?php
declare(strict_types=1);
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function textInput(array $source, string $key): string
{
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
}
function positiveId($value): int
{
    if (!is_string($value) && !is_int($value)) { return 0; }
    return (int) (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0);
}
function lengthOf(string $value): int { return mb_strlen($value, 'UTF-8'); }
function redirect(string $localPage): void
{
    // Callers supply fixed local paths, never untrusted redirect URLs.
    header('Location: ' . $localPage, true, 303); exit;
}
function fail(int $status, string $message): void
{
    http_response_code($status);
    $pageTitle = 'Request could not be completed';
    require __DIR__ . '/header.php';
    echo '<section class="panel"><h1>Request could not be completed</h1><p>' . e($message) . '</p><a href="index.php">Back to recipes</a></section>';
    require __DIR__ . '/footer.php'; exit;
}
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf_token'];
}
function csrfField(): void { echo '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">'; }
function validCsrf(): bool
{
    $token = textInput($_POST, 'csrf_token');
    return $token !== '' && hash_equals(csrfToken(), $token);
}
function requirePost(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { header('Allow: POST'); fail(405, 'Please use the form to perform this action.'); }
    if (!validCsrf()) { fail(403, 'Your form expired. Reload and try again.'); }
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function dateLabel(string $value): string { return date('M j, Y', strtotime($value)); }
function readMinutes(array $recipe, array $ingredients): int
{
    $text = $recipe['title'] . ' ' . $recipe['description'] . ' ' . implode(' ', $ingredients) . ' ' . $recipe['instructions'];
    $words = preg_match_all('/[\p{L}\p{N}]+(?:[\x{0027}\x{2019}-][\p{L}\p{N}]+)*/u', $text);
    return max(1, (int) ceil(($words === false ? 0 : $words) / 200));
}
function recipeInput(array $source): array
{
    $ingredients = $source['ingredients'] ?? [];
    return ['title' => textInput($source, 'title'), 'description' => textInput($source, 'description'),
        'instructions' => textInput($source, 'instructions'), 'category_id' => positiveId($source['category_id'] ?? null),
        'ingredients' => is_array($ingredients) ? array_values($ingredients) : []];
}
function recipeErrors(array $data, Category $categories): array
{
    $errors = [];
    foreach (['title' => [3, 150], 'description' => [10, 500], 'instructions' => [10, 10000]] as $field => $bounds) {
        $length = lengthOf($data[$field]);
        if ($length < $bounds[0] || $length > $bounds[1]) { $errors[] = ucfirst($field) . " must have {$bounds[0]}-{$bounds[1]} characters."; }
    }
    if (!$categories->exists($data['category_id'])) { $errors[] = 'Choose an available category.'; }
    if (count($data['ingredients']) < 1 || count($data['ingredients']) > 30) { $errors[] = 'Enter 1-30 ingredients.'; }
    foreach ($data['ingredients'] as $ingredient) {
        if (!is_string($ingredient) || lengthOf(trim($ingredient)) < 1 || lengthOf(trim($ingredient)) > 255) {
            $errors[] = 'Each ingredient must have 1-255 characters.'; break;
        }
    }
    return $errors;
}
function showErrors(array $errors): void
{
    if (!$errors) { return; }
    echo '<div class="notice error" role="alert"><ul>';
    foreach ($errors as $error) { echo '<li>' . e($error) . '</li>'; }
    echo '</ul></div>';
}
