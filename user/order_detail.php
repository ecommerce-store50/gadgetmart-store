<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$orderId = (int)($_GET['id'] ?? 0);
$order = db()->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
$order->execute([$orderId]);
$order = $order->fetch();
if (!$order) { header('Location: orders.php'); exit; }

$items = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$orderId]);
$items = $items->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Detail</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <h2>GadgetMart</h2>
    <nav>
      <a href="index.php">ড্যাশবোর্ড</a>
      <a href="products.php">প্রোডাক্ট ম্যানেজ</a>
      <a href="orders.php" class="active">অর্ডার ম্যানেজ</a>
      <a href="settings.php">সাইট সেটিংস</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="topbar"><h1>অর্ডার ডিটেইল #<?= e($orderId) ?></h1></div>
    <section class="panel">
      <p><strong>কাস্টমার:</strong> <?= e($order['customer_name']) ?></p>
      <p><strong>ফোন:</strong> <?= e($order['phone']) ?></p>
      <p><strong>ইমেইল:</strong> <?= e($order['email']) ?></p>
      <p><strong>ঠিকানা:</strong> <?= e($order['address']) ?></p>
      <p><strong>স্ট্যাটাস:</strong> <?= e($order['status']) ?></p>
      <p><strong>মোট:</strong> ৳<?= e(number_format((float)$order['total'], 2)) ?></p>
      <hr>
      <h3>অর্ডার আইটেম</h3>
      <div class="table-wrap">
        <table>
          <tr><th>নাম</th><th>পরিমাণ</th><th>প্রতি ইউনিট</th><th>মোট</th></tr>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['product_name']) ?></td>
              <td><?= e($it['quantity']) ?></td>
              <td>৳<?= e(number_format((float)$it['unit_price'], 2)) ?></td>
              <td>৳<?= e(number_format((float)$it['unit_price'] * (int)$it['quantity'], 2)) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
