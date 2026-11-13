<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/config.php';

$errors = [];
$saved = isset($_GET['saved']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit     = trim($_POST['unit'] ?? '');
    $rating   = (int)($_POST['rating'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');

    if ($unit === '') {
        $errors[] = 'Please enter the unit name.';
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Choose a rating from 1 to 5.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO feedback (user_id, unit, rating, comments) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$_SESSION['user_id'], $unit, $rating, $comments]);
        header('Location: feedback.php?saved=1');
        exit;
    }
}

$rows = $pdo->query(
    'SELECT f.id, u.username, f.unit, f.rating, f.comments, f.submitted_at
     FROM feedback f JOIN users u ON u.id = f.user_id
     ORDER BY f.id DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unit Feedback</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .card { max-width: 760px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
        th, td { border: 1px solid #ccd; padding: 8px; text-align: left; }
        th { background: #eef2fa; }
        textarea, select { width: 100%; padding: 10px; margin-bottom: 12px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Unit Feedback</h1>
        <p class="sub">Signed in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>

        <?php foreach ($errors as $e): ?>
            <div class="alert error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if ($saved): ?>
            <div class="alert success">Feedback saved.</div>
        <?php endif; ?>

        <form method="post" action="feedback.php">
            <label for="unit">Unit</label>
            <input type="text" id="unit" name="unit" required>

            <label for="rating">Rating (1 to 5)</label>
            <select id="rating" name="rating" required>
                <option value="">Choose...</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <label for="comments">Comments</label>
            <textarea id="comments" name="comments" rows="3"></textarea>

            <button type="submit">Submit feedback</button>
        </form>

        <h2>Submitted feedback</h2>
        <table>
            <tr><th>ID</th><th>User</th><th>Unit</th><th>Rating</th><th>Comments</th><th>Submitted</th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['unit']) ?></td>
                    <td><?= (int)$r['rating'] ?></td>
                    <td><?= htmlspecialchars($r['comments']) ?></td>
                    <td><?= htmlspecialchars($r['submitted_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p class="switch"><a href="dashboard.php">Back to dashboard</a></p>
    </div>
</body>
</html>
