<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$settings = ['site_name','site_tagline','footer_description','phone','email','address','facebook_url','instagram_url','youtube_url','twitter_url','whatsapp_url'];
$saveMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO settings(setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($settings as $key) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }
    $saveMsg = 'সাইট সেটিংস সংরক্ষণ হয়েছে।';
}

$stats = [
    'total_orders' => db()->query('SELECT COUNT(*) as c FROM orders')->fetch()['c'],
    'pending_orders' => db()->query("SELECT COUNT(*) as c FROM orders WHERE status IN ('pending','confirmed','processing')")->fetch()['c'],
    'products' => db()->query('SELECT COUNT(*) as c FROM products')->fetch()['c'],
    'users' => db()->query('SELECT COUNT(*) as c FROM users')->fetch()['c'],
];

$recentOrders = db()->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 8')->fetchAll();
$products = db()->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 10')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - <?=e(siteName())?></title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <h2>GadgetMart</h2>
    <nav>
      <a href="index.php" class="active">ড্যাশবোর্ড</a>
      <a href="products.php">প্রোডাক্ট ম্যানেজ</a>
      <a href="orders.php">অর্ডার ম্যানেজ</a>
      <a href="settings.php">সাইট সেটিংস</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="topbar">
      <h1>অ্যাডমিন ড্যাশবোর্ড</h1>
      <a href="../index.php" class="btn btn-primary">স্টোর দেখুন</a>
    </div>

    <div class="stats-grid">
      <div class="stat-card"><div>Total Orders</div><strong><?=e($stats['total_orders'])?></strong></div>
      <div class="stat-card"><div>Pending Orders</div><strong><?=e($stats['pending_orders'])?></strong></div>
      <div class="stat-card"><div>Products</div><strong><?=e($stats['products'])?></strong></div>
      <div class="stat-card"><div>Users</div><strong><?=e($stats['users'])?></strong></div>
    </div>

    <div class="panel-grid">
      <section class="panel">
        <h3>সাইট সেটিংস</h3>
        <form method="post" class="stacked-form small-form">
          <?php foreach ($settings as $key): ?>
            <label>
              <?=e(str_replace('_', ' ', ucfirst($key)))?>
              <input name="<?=e($key)?>" value="<?=e(setting($key, ''))?>">
            </label>
          <?php endforeach; ?>
          <button type="submit" class="btn btn-primary">সেভ করুন</button>
          <?php if ($saveMsg): ?><div class="alert success"><?=e($saveMsg)?></div><?php endif; ?>
        </form>
      </section>

      <section class="panel">
        <h3>সাম্প্রতিক অর্ডার</h3>
        <div class="table-wrap">
          <table>
            <tr><th>অর্ডার</th><th>ক্লায়েন্ট</th><th>স্ট্যাটাস</th><th>মোট</th></tr>
            <?php foreach ($recentOrders as $o): ?>
              <tr>
                <td>#<?=e($o['id'])?></td>
                <td><?=e($o['customer_name'])?></td>
                <td><?=e($o['status'])?></td>
                <td>৳<?=e(number_format((float)$o['total'], 2))?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </section>
    </div>

    <section class="panel">
      <h3>সর্বশেষ প্রোডাক্ট</h3>
      <div class="product-row">
        <?php foreach ($products as $p): ?>
          <div class="mini-product">
            <div class="mini-thumb">📦</div>
            <div>
              <strong><?=e($p['name'])?></strong><br>
              <span>৳<?=e(number_format((float)$p['price'], 2))?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
