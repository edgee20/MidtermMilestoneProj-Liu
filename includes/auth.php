<?php
declare(strict_types=1);
function userId(): int { return positiveId($_SESSION['user_id'] ?? null); }
function requireLogin(): void { if (!userId()) { redirect('login.php'); } }
function signIn(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
