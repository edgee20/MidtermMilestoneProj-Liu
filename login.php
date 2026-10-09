<?php
declare(strict_types=1);
define('PUBLIC_PAGE', true);
require __DIR__ . '/config/app.php';
if (userId()) { redirect('index.php'); }
$errors = []; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePost();
    $email = textInput($_POST, 'email');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 72 || strpos($password, chr(0)) !== false) {
        $errors[] = 'Enter a valid email and password.';
    } else {
        $user = $users->authenticate($email, $password);
        if ($user) { signIn($user); redirect('index.php'); }
        $errors[] = 'Email or password is incorrect.';
    }
}
$pageTitle = 'Log in'; require __DIR__ . '/includes/header.php';
?>
<section class="panel auth-panel">
<p class="eyebrow">Welcome to the table</p><h1>Log in</h1>
<p>Share your home recipes and discover your neighbors' favorites.</p>
<?php showErrors($errors); ?>
<form method="post"><?php csrfField(); ?>
<label>Email<input type="email" name="email" autocomplete="email" maxlength="255" value="<?= e($email) ?>" required></label>
<label>Password<input type="password" name="password" autocomplete="current-password" required></label>
<button>Log in</button></form>
<p>New here? <a href="register.php">Create an account</a>.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
