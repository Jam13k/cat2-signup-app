<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
        <p class="sub">You are logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>
        <div class="alert success">Login successful. This page is only visible to signed-in users.</div>
        <p class="switch"><a href="logout.php">Log out</a></p>
    </div>
</body>
</html>
