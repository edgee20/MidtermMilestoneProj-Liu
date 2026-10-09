<?php
declare(strict_types=1);
require __DIR__ . '/config/app.php';
requirePost();
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'],
 'domain' => $params['domain'], 'secure' => $params['secure'], 'httponly' => $params['httponly'], 'samesite' => $params['samesite']]);
session_destroy(); redirect('login.php');
