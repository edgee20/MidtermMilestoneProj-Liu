<?php
declare(strict_types=1);
// CLI only: never expose test execution through Apache.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../includes/functions.php';
foreach (['Database', 'User', 'Recipe', 'Category', 'Comment', 'Favorite'] as $class) {
    require __DIR__ . '/../classes/' . $class . '.php';
}
$database = new Database(require __DIR__ . '/../config/database.php');
$db = $database->getConnection();
$users = new User($db); $recipes = new Recipe($db); $comments = new Comment($db);
$favorites = new Favorite($db); $categories = new Category($db);
$checks = 0;
function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) { throw new RuntimeException('FAIL: ' . $message); }
    $checks++; echo "PASS: $message\n";
}
$base = 'http://localhost/MidtermMilestoneProj-Liu/';
$runtime = __DIR__ . '/.runtime';
if (!is_dir($runtime)) { mkdir($runtime, 0700, true); }
$cookieA = $runtime . '/a.cookies'; $cookieB = $runtime . '/b.cookies';
function request(string $page, ?array $post = null, ?string $cookie = null): array
{
    global $base;
    $handle = curl_init($base . $page);
    curl_setopt_array($handle, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 15]);
    if ($post !== null) { curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($post)); }
    if ($cookie !== null) { curl_setopt($handle, CURLOPT_COOKIEFILE, $cookie); curl_setopt($handle, CURLOPT_COOKIEJAR, $cookie); }
    $response = curl_exec($handle);
    if ($response === false) { throw new RuntimeException(curl_error($handle)); }
    $size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
    $result = ['status' => curl_getinfo($handle, CURLINFO_HTTP_CODE), 'headers' => substr($response, 0, $size), 'body' => substr($response, $size)];
    curl_close($handle); return $result;
}
function token(array $response): string
{
    preg_match('/name="csrf_token" value="([^"]+)"/', $response['body'], $match);
    if (empty($match[1])) { throw new RuntimeException('Missing form CSRF token'); }
    return $match[1];
}
$tag = bin2hex(random_bytes(6)); $ids = [];
$password = 'TestPassword123!';
try {
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    sort($tables);
    check($tables === ['categories', 'comments', 'favorites', 'ingredients', 'recipes', 'users'], 'Exactly six required tables');
    check(count($categories->all()) === 8, 'Eight category seeds');
    check(request('index.php')['status'] === 303, 'Guests redirected from feed');
    check(request('api/toggle_favorite.php', ['recipe_id' => 1])['status'] === 401, 'Guest API request rejected');
    $register = request('register.php', null, $cookieA); $csrf = token($register);
    $emailA = 'test-' . $tag . '-a@example.invalid';
    $emailB = 'test-' . $tag . '-b@example.invalid';
    $registration = ['csrf_token' => $csrf, 'name' => 'Test Cook A', 'email' => $emailA, 'password' => $password, 'confirm_password' => $password];
    check(request('register.php', $registration, $cookieA)['status'] === 303, 'Registration through HTTP');
    $a = $users->findByEmail($emailA); $ids[] = (int) $a['id'];
    check($a['password'] !== $password && password_verify($password, $a['password']), 'Password stored as verified hash');
    check(strpos(request('register.php', $registration, $cookieA)['body'], 'already registered') !== false, 'Duplicate email rejected');
    $registration['email'] = 'bad-email'; $registration['confirm_password'] = 'different';
    check(strpos(request('register.php', $registration, $cookieA)['body'], 'Passwords do not match') !== false, 'Registration validation rejects mismatch');
    $bId = $users->register('Test Cook B', $emailB, $password); $ids[] = $bId;
    $login = request('login.php', null, $cookieA); $csrf = token($login);
    check(strpos(request('login.php', ['csrf_token' => $csrf, 'email' => $emailA, 'password' => 'wrong'], $cookieA)['body'], 'incorrect') !== false, 'Wrong password rejected');
    check(request('login.php', ['csrf_token' => $csrf, 'email' => $emailA, 'password' => $password], $cookieA)['status'] === 303, 'Login through HTTP');
    $feed = request('index.php', null, $cookieA); $csrfA = token($feed);
    check($csrfA !== $csrf, 'CSRF token rotated at login');
    check(strpos($feed['body'], 'Test Cook A') !== false, 'Session persists to protected page');
    $loginB = request('login.php', null, $cookieB);
    check(request('login.php', ['csrf_token' => token($loginB), 'email' => $emailB, 'password' => $password], $cookieB)['status'] === 303, 'Second account login');
    $csrfB = token(request('index.php', null, $cookieB));
    $data = ['title' => 'Test Adobo ' . $tag, 'description' => 'A home recipe for testing.',
        'category_id' => 2, 'ingredients' => ['1 cup chicken', '2 tbsp soy sauce'], 'instructions' => 'Combine the ingredients. Simmer until cooked.'];
    check(request('create-recipe.php', $data + ['csrf_token' => 'bad'], $cookieA)['status'] === 403, 'Invalid CSRF rejected');
    $invalid = $data; $invalid['ingredients'] = [['unexpected array']];
    check(strpos(request('create-recipe.php', $invalid + ['csrf_token' => $csrfA], $cookieA)['body'], 'Each ingredient must') !== false, 'Nested ingredient input rejected safely');
    $created = request('create-recipe.php', $data + ['csrf_token' => $csrfA], $cookieA);
    preg_match('/Location: recipe.php\?id=(\d+)/i', $created['headers'], $match);
    check($created['status'] === 303 && !empty($match[1]), 'Recipe creation redirects to detail');
    $recipeId = (int) $match[1];
    check($recipes->ingredients($recipeId) === $data['ingredients'], 'Separate ingredient rows preserve order');
    $detail = request('recipe.php?id=' . $recipeId, null, $cookieA);
    check($detail['status'] === 200 && strpos($detail['body'], '1 min read') !== false, 'Recipe detail and reading time');
    check(count($recipes->all((int) $a['id'], $tag, 2)) === 1 && count($recipes->all((int) $a['id'], $tag, 1)) === 0, 'Combined search and category filtering');
    check(request('edit-recipe.php?id=' . $recipeId, null, $cookieB)['status'] === 403, 'Other member edit URL rejected');
    check(request('edit-recipe.php?id=' . $recipeId, $data + ['csrf_token' => $csrfB], $cookieB)['status'] === 403, 'Other member direct edit POST rejected');
    check(request('delete-recipe.php', ['recipe_id' => $recipeId, 'csrf_token' => $csrfB], $cookieB)['status'] === 403, 'Other member delete rejected');
    check(request('delete-recipe.php', null, $cookieA)['status'] === 405, 'GET cannot delete');
    check(request('recipe.php?id[]=1', null, $cookieA)['status'] === 400, 'Array ID safely rejected');
    check(request('recipe.php?id=2147483647', null, $cookieA)['status'] === 404, 'Missing recipe returns 404');
    check(request('edit-recipe.php?id=' . $recipeId, $data + ['csrf_token' => $csrfA], $cookieA)['status'] === 303
        && !(bool) $recipes->find($recipeId)['is_edited'], 'Unchanged edit does not set edited flag');
    $data['ingredients'] = array_reverse($data['ingredients']); $data['title'] .= ' updated';
    check(request('edit-recipe.php?id=' . $recipeId, $data + ['csrf_token' => $csrfA], $cookieA)['status'] === 303
        && (bool) $recipes->find($recipeId)['is_edited'] && $recipes->ingredients($recipeId) === $data['ingredients'], 'Actual edit replaces ingredient order and marks edited');
    $before = count($recipes->all((int) $a['id']));
    $bad = $data; $bad['ingredients'] = [null];
    $rolledBack = false;
    try { $recipes->create((int) $a['id'], $bad); } catch (PDOException $error) { $rolledBack = true; }
    check($rolledBack && count($recipes->all((int) $a['id'])) === $before, 'Failed ingredient insertion rolls back recipe');
    $originalIngredients = $recipes->ingredients($recipeId); $originalTitle = $recipes->find($recipeId)['title'];
    $bad['title'] = 'Should be rolled back';
    try { $recipes->update($recipeId, (int) $a['id'], $bad); } catch (PDOException $error) {}
    check($recipes->ingredients($recipeId) === $originalIngredients && $recipes->find($recipeId)['title'] === $originalTitle, 'Failed edit restores recipe and ingredients');
    $favorite = ['recipe_id' => $recipeId, 'is_favorited' => '1', 'csrf_token' => $csrfA];
    $response = request('api/toggle_favorite.php', $favorite, $cookieA); $json = json_decode($response['body'], true);
    check($response['status'] === 200 && $json['is_favorited'] === true, 'Authenticated favorites JSON response');
    request('api/toggle_favorite.php', $favorite, $cookieA);
    check(count($favorites->forUser((int) $a['id'])) === 1, 'Repeated save creates no duplicate');
    check(count($favorites->forUser($bId)) === 0, 'Favorites isolated by account');
    check(strpos(request('favorites.php', null, $cookieA)['body'], $data['title']) !== false, 'Favorites page contains saved recipe');
    $favorite['is_favorited'] = '0';
    check(json_decode(request('api/toggle_favorite.php', $favorite, $cookieA)['body'], true)['is_favorited'] === false, 'Unsave returns updated state');
    $xss = '<script>alert(1)</script>';
    check(request('comment-action.php', ['action' => 'add', 'recipe_id' => $recipeId, 'content' => $xss, 'csrf_token' => $csrfA], $cookieA)['status'] === 303, 'Comment creation');
    $comment = $comments->all($recipeId)[0]; $commentId = (int) $comment['id'];
    $html = request('recipe.php?id=' . $recipeId, null, $cookieB)['body'];
    check(strpos($html, '&lt;script&gt;') !== false && strpos($html, $xss) === false, 'User content escaped in HTML');
    check(request('comment-action.php', ['action' => 'edit', 'comment_id' => $commentId, 'content' => 'Hijack', 'csrf_token' => $csrfB], $cookieB)['status'] === 403, 'Other member comment edit rejected');
    check(request('comment-action.php', ['action' => 'delete', 'comment_id' => $commentId, 'csrf_token' => $csrfB], $cookieB)['status'] === 403, 'Other member comment delete rejected');
    $comments->update($commentId, (int) $a['id'], $xss);
    check(!(bool) $comments->find($commentId)['is_edited'], 'Unchanged comment is not marked edited');
    check(request('comment-action.php', ['action' => 'edit', 'comment_id' => $commentId, 'content' => 'A useful tip', 'csrf_token' => $csrfA], $cookieA)['status'] === 303
        && (bool) $comments->find($commentId)['is_edited'], 'Comment edit and edited flag');
    check(request('comment-action.php', ['action' => 'delete', 'comment_id' => $commentId, 'csrf_token' => $csrfA], $cookieA)['status'] === 303
        && !$comments->find($commentId), 'Author deletes comment');
    $comments->add($recipeId, $bId, 'Cascade test'); $favorites->add($bId, $recipeId);
    check(request('delete-recipe.php', ['recipe_id' => $recipeId, 'csrf_token' => $csrfA], $cookieA)['status'] === 303, 'Author deletes recipe');
    check(!$recipes->find($recipeId) && !$recipes->ingredients($recipeId) && !$comments->all($recipeId) && !$favorites->exists($bId, $recipeId), 'Recipe delete cascades to all dependent records');
    check(readMinutes(['title' => '', 'description' => '', 'instructions' => str_repeat('salita ', 201)], []) === 2, 'Read time rounds 201 Unicode words up to two minutes');
    check(readMinutes(['title' => '', 'description' => '', 'instructions' => ''], []) === 1, 'Read time minimum one minute');
    check(request('logout.php', ['csrf_token' => $csrfA], $cookieA)['status'] === 303
        && request('index.php', null, $cookieA)['status'] === 303, 'Logout ends access');
    echo "\n$checks checks passed.\n";
} finally {
    // Delete only the IDs this run created; related test rows cascade.
    $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
    foreach ($ids as $id) { $stmt->execute([$id]); }
    foreach ([$cookieA, $cookieB] as $cookie) { if (is_file($cookie)) { unlink($cookie); } }
}
