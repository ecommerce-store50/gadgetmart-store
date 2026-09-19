<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$keys = ['site_name','site_tagline','footer_description','phone','email','address','facebook_url','instagram_url','youtube_url','twitter_url','whatsapp_url'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($keys as $key) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }
    $message = 'সাইট সেটিংস সংরক্ষণ হয়েছে।';
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <h2>GadgetMart</h2>
    <nav>
      <a href="index.php">ড্যাশবোর্ড</a>
      <a href="products.php">প্রোডাক্ট ম্যানেজ</a>
      <a href="orders.php">অর্ডার ম্যানেজ</a>
      <a href="settings.php" class="active">সাইট সেটিংস</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="topbar"><h1>সাইট সেটিংস</h1></div>
    <?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
    <section class="panel">
      <form method="post" class="stacked-form small-form">
        <?php foreach ($keys as $key): ?>
          <label>
            <?= e(str_replace('_', ' ', ucfirst($key))) ?>
            <input name="<?= e($key) ?>" value="<?= e(setting($key, '')) ?>">
          </label>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">সেভ করুন</button>
      </form>
    </section>
  </main>
</body>
</html>
