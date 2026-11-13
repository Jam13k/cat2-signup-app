<?php
$errors = [];
$success = '';
$full_name = $username = $email = $course = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config.php';

    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $course    = trim($_POST['course'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '' || $email === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);

        if ($check->fetch()) {
            $errors[] = 'That username or email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare(
                'INSERT INTO users (full_name, username, email, course, password_hash) VALUES (?, ?, ?, ?, ?)'
            );
            $insert->execute([$full_name, $username, $email, $course, $hash]);
            $success = 'Account created! You can now log in.';
            $full_name = $username = $email = $course = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Create an account</h1>
        <p class="sub">Register to access the student portal.</p>

        <?php foreach ($errors as $e): ?>
            <div class="alert error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="signup.php">
            <label for="full_name">Full name</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="course">Course / programme</label>
            <input type="text" id="course" name="course" value="<?= htmlspecialchars($course) ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Sign up</button>
        </form>

        <p class="switch">Already registered? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
