<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$keys = ['site_name','site_tagline','footer_description','phone','email','address','facebook_url','instagram_url','youtube_url','twitter_url','whatsapp_url','delivery_fee','coupon_code','coupon_discount'];

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
    <div class="admin-brand">
      <div class="logo-circle">G</div>
      <h2>GadgetMart</h2>
    </div>
    <nav>
      <a href="index.php">ড্যাশবোর্ড</a>
      <a href="products.php">প্রোডাক্ট</a>
      <a href="orders.php">অর্ডার</a>
      <a href="settings.php" class="active">সাইট সেটিংস</a>
      <a href="../index.php">সাইট দেখুন</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="topbar">
      <div>
        <h1>সাইট সেটিংস</h1>
        <p>আপনার স্টোরের মূল সেটিংস</p>
      </div>
      <button type="button" class="hamburger-btn" aria-label="Toggle menu">☰</button>
    </div>

    <?php if (!empty($message)): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>

    <section class="panel">
      <form method="post" class="stacked-form">
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

  <script>
    const btn = document.querySelector('.hamburger-btn');
    if (btn) {
      btn.addEventListener('click', function () {
        document.body.classList.toggle('menu-open');
      });
    }
  </script>
</body>
</html>
