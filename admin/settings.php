<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$settings = ['site_name','site_tagline','footer_description','phone','email','address','facebook_url','instagram_url','youtube_url','twitter_url','whatsapp_url'];
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Site Settings</title>
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
    <section class="panel">
      <form method="post" action="index.php" class="stacked-form small-form">
        <?php foreach ($settings as $key): ?>
          <label>
            <?=e(str_replace('_', ' ', ucfirst($key)))?>
            <input name="<?=e($key)?>" value="<?=e(setting($key, ''))?>">
          </label>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">সেভ করুন</button>
      </form>
    </section>
  </main>
</body>
</html>
