<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
foreach (['Database', 'User', 'Recipe', 'Category', 'Comment', 'Favorite'] as $class) {
    require_once __DIR__ . '/../classes/' . $class . '.php';
}
set_exception_handler(function (Throwable $error): void {
    error_log((string) $error);
    http_response_code(500);
    if (defined('JSON_ENDPOINT')) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'message' => 'Unable to complete the request. Check MySQL and database setup.']);
    } else {
        $pageTitle = 'Service unavailable';
        require __DIR__ . '/../includes/header.php';
        echo '<section class="panel"><h1>Service unavailable</h1><p>Check that MySQL is running and database.sql has been imported, then try again.</p></section>';
        require __DIR__ . '/../includes/footer.php';
    }
});
// Check guests before connecting or rendering.
if (!defined('PUBLIC_PAGE') && !defined('JSON_ENDPOINT')) { requireLogin(); }
$database = new Database(require __DIR__ . '/database.php');
$db = $database->getConnection();
$users = new User($db); $recipes = new Recipe($db); $categories = new Category($db);
$comments = new Comment($db); $favorites = new Favorite($db);
if (userId() && !$users->findById(userId())) { unset($_SESSION['user_id'], $_SESSION['name']); }
if (!defined('PUBLIC_PAGE') && !defined('JSON_ENDPOINT')) { requireLogin(); }
