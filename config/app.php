<?php
declare(strict_types=1);
date_default_timezone_set("Asia/Manila");
ini_set("display_errors", "0"); // debugging
ini_set("session.use_strict_mode", "1"); // avoids session fixation attacks/ reject uninitialized session ids
session_set_cookie_params([
    "lifetime" => 0, // Session cookie expires when the browser session ends
    "path" => "/", // Cookie is available throughout the website
    "httponly" => true, // JavaScript cannot access the cookie through document.cookie
    "samesite" => "Lax", //	Restricts sending the cookie in certain cross-site requests
    "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off", // 	Sends the cookie only over HTTPS when enabled
]);
session_start();
header("Content-Type: text/html; charset=UTF-8"); // tells the browser to interpret the page as UTF-8 encoded HTML
header("X-Content-Type-Options: nosniff"); // prevents the browser from interpreting files as a different MIME type than what is specified in the Content-Type header
header("Cache-Control: no-store"); // prevents caching of sensitive information
require_once __DIR__ . "/../includes/functions.php"; // __DIR__ is a PHP magic constant that contains the directory of the current PHP file.
require_once __DIR__ . "/../includes/auth.php";
foreach (
    ["Database", "User", "Recipe", "Category", "Comment", "Favorite"]
    as $class
) {
    require_once __DIR__ . "/../classes/" . $class . ".php";
}
set_exception_handler(function (Throwable $error): void {
    error_log((string) $error);
    http_response_code(500);
    if (defined("JSON_ENDPOINT")) {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" =>
                "Unable to complete the request. Check MySQL and database setup.",
        ]); // converts the PHP array into a JSON string and outputs it to the client   
    } else {
        $pageTitle = "Service unavailable";
        require __DIR__ . "/../includes/header.php";
        echo '<section class="panel"><h1>Service unavailable</h1><p>Check that MySQL is running and database.sql has been imported, then try again.</p></section>';
        require __DIR__ . "/../includes/footer.php";
    }
});
// Check guests before connecting or rendering.
if (!defined("PUBLIC_PAGE") && !defined("JSON_ENDPOINT")) {
    requireLogin();
}
$database = new Database(require __DIR__ . "/database.php");
$db = $database->getConnection();
$users = new User($db);
$recipes = new Recipe($db);
$categories = new Category($db);
$comments = new Comment($db);
$favorites = new Favorite($db);
if (userId() && !$users->findById(userId())) { // check if the user exists in the database, if not, log them out
    unset($_SESSION["user_id"], $_SESSION["name"]);
} // Imagine an administrator deletes a user from the database while that user still has an active session.
if (!defined("PUBLIC_PAGE") && !defined("JSON_ENDPOINT")) {
    requireLogin();
}

// First check: Prevents guests from accessing protected pages before the 
// application establishes a database connection.

// Second check: Ensures that users whose accounts no longer exist are treated
//  as guests after their session information is cleared.
