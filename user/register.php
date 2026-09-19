<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (!empty($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($name && $email && $password) {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $error = 'এই ইমেইল দিয়ে আগেই অ্যাকাউন্ট আছে।';
        } else {
            $stmt = db()->prepare('INSERT INTO users(name, email, phone, password_hash, address) VALUES(?,?,?,?,?)');
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $address]);
            $_SESSION['user_id'] = db()->lastInsertId();
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Register</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>ইউজার রেজিস্টার</h1>
    <?php if (!empty($error)): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
    <form method="post" class="stacked-form">
      <input type="hidden" name="register" value="1">
      <label>নাম<input name="name" required></label>
      <label>ইমেইল<input type="email" name="email" required></label>
      <label>ফোন<input name="phone"></label>
      <label>ঠিকানা<textarea name="address"></textarea></label>
      <label>পাসওয়ার্ড<input type="password" name="password" required></label>
      <button type="submit" class="btn btn-primary full-width">একাউন্ট খুলুন</button>
      <p class="note">আগে অ্যাকাউন্ট আছে? <a href="login.php">লগইন করুন</a></p>
    </form>
  </div>
</body>
</html>
