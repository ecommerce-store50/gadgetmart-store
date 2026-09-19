<?php
require __DIR__ . '/config/config.php';

session_start();
if (!empty($_SESSION['admin_id'])) { header('Location: admin/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit;
    }
    $error = 'ইমেইল অথবা পাসওয়ার্ড ভুল হয়েছে।';
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - <?=e(siteName())?></title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>Admin Login</h1>
    <?php if (!empty($error)): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
    <form method="post" class="stacked-form">
      <label>
        ইমেইল
        <input type="email" name="email" required>
      </label>
      <label>
        পাসওয়ার্ড
        <input type="password" name="password" required>
      </label>
      <button type="submit" class="btn btn-primary full-width">লগইন</button>
    </form>
  </div>
</body>
</html>
