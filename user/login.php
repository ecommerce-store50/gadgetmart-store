<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (userLoggedIn()) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) { $_SESSION['user_id'] = $user['id']; header('Location: dashboard.php'); exit; }
    $error = 'ইমেইল অথবা পাসওয়ার্ড ভুল হয়েছে।';
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Login</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>ইউজার লগইন</h1>
    <?php if (!empty($error)): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
    <form method="post" class="stacked-form">
      <label>ইমেইল<input type="email" name="email" required></label>
      <label>পাসওয়ার্ড<input type="password" name="password" required></label>
      <button type="submit" class="btn btn-primary full-width">লগইন</button>
      <p class="note">অ্যাকাউন্ট নেই? <a href="register.php">নতুন অ্যাকাউন্ট খুলুন</a></p>
    </form>
  </div>
</body>
</html>
