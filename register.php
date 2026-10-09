<?php
declare(strict_types=1);
define('PUBLIC_PAGE', true);
require __DIR__ . '/config/app.php';
if (userId()) { redirect('index.php'); }
$errors = []; $name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePost();
    $name = textInput($_POST, 'name'); $email = textInput($_POST, 'email');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['confirm_password']) && is_string($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    if (lengthOf($name) < 2 || lengthOf($name) > 100) { $errors[] = 'Name must have 2-100 characters.'; }
    if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email.'; }
    // PHP 8.0 PASSWORD_DEFAULT uses bcrypt, which only uses the first 72 bytes.
    if (lengthOf($password) < 8 || strlen($password) > 72 || strpos($password, chr(0)) !== false) { $errors[] = 'Password needs at least 8 characters, at most 72 bytes, and no null characters.'; }
    if ($password !== $confirm) { $errors[] = 'Passwords do not match.'; }
    if (!$errors) {
        try {
            if ($users->findByEmail($email)) { $errors[] = 'This email is already registered.'; }
            else {
                $users->register($name, $email, $password);
                flash('Account created. You can now log in.'); redirect('login.php');
            }
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? 0) === 1062) { $errors[] = 'This email is already registered.'; }
            else { throw $error; }
        }
    }
}
$pageTitle = 'Register'; require __DIR__ . '/includes/header.php';
?>
<section class="panel auth-panel"><p class="eyebrow">Join the community</p><h1>Create an account</h1>
<?php showErrors($errors); ?>
<form method="post"><?php csrfField(); ?>
<label>Name<input name="name" autocomplete="name" minlength="2" maxlength="100" value="<?= e($name) ?>" required></label>
<label>Email<input type="email" name="email" autocomplete="email" maxlength="255" value="<?= e($email) ?>" required></label>
<label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required><small>At least 8 characters; maximum 72 bytes.</small></label>
<label>Confirm password<input type="password" name="confirm_password" autocomplete="new-password" minlength="8" maxlength="72" required></label>
<button>Create account</button></form>
<p>Already a member? <a href="login.php">Log in</a>.</p></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
