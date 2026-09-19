<?php
require __DIR__ . '/../config/config.php';

session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$stats = [
    'orders' => (int)db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'pending' => (int)db()->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
    'revenue' => (float)db()->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled'")->fetchColumn(),
    'products' => (int)db()->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn(),
    'users' => (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
];

$recent = db()->query('SELECT id, customer_name, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 7')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <div class="logo-circle">G</div>
      <h2>GadgetMart</h2>
    </div>
    <nav>
      <a href="index.php" class="active">ড্যাশবোর্ড</a>
      <a href="products.php">প্রোডাক্ট</a>
      <a href="orders.php">অর্ডার</a>
      <a href="settings.php">সাইট সেটিংস</a>
      <a href="../index.php">সাইট দেখুন</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="topbar">
      <div>
        <h1>অ্যাডমিন ড্যাশবোর্ড</h1>
        <p>স্টোরের সারসংক্ষেপ</p>
      </div>
      <button type="button" class="hamburger-btn" aria-label="Toggle menu">☰</button>
    </div>

    <div class="stats-grid">
      <div class="stat-card"><span>মোট অর্ডার</span><strong><?= e($stats['orders']) ?></strong></div>
      <div class="stat-card"><span>পেন্ডিং</span><strong><?= e($stats['pending']) ?></strong></div>
      <div class="stat-card"><span>মোট বিক্রি</span><strong>৳<?= e(number_format((float)$stats['revenue'], 2)) ?></strong></div>
      <div class="stat-card"><span>সক্রিয় প্রোডাক্ট</span><strong><?= e($stats['products']) ?></strong></div>
      <div class="stat-card"><span>ইউজার</span><strong><?= e($stats['users']) ?></strong></div>
    </div>

    <section class="panel">
      <div class="section-title-row">
        <h2>সাম্প্রতিক অর্ডার</h2>
        <a class="btn btn-secondary" href="orders.php">সব দেখুন</a>
      </div>
      <div class="table-wrap">
        <table>
          <tr>
            <th>অর্ডার</th>
            <th>কাস্টমার</th>
            <th>মোট</th>
            <th>স্ট্যাটাস</th>
            <th>তারিখ</th>
          </tr>
          <?php foreach ($recent as $o): ?>
            <tr>
              <td><a href="order_detail.php?id=<?= e($o['id']) ?>">#<?= e($o['id']) ?></a></td>
              <td><?= e($o['customer_name']) ?></td>
              <td>৳<?= e(number_format((float)$o['total'], 2)) ?></td>
              <td><?= e($o['status']) ?></td>
              <td><?= e($o['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
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
