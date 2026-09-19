<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$orderId = (int)($_GET['id'] ?? 0);
$order = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$order->execute([$orderId, $_SESSION['user_id']]);
$order = $order->fetch();
if (!$order) { header('Location: dashboard.php'); exit; }

$items = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$orderId]);
$items = $items->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Details</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="user-body">
  <header class="topbar user-topbar">
    <div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?= e(setting('site_name','GadgetMart')) ?></strong></div></div>
    <div class="header-right"><a href="../index.php">হোম</a><a href="dashboard.php">ড্যাশবোর্ড</a><a href="logout.php">লগআউট</a></div>
  </header>
  <main class="page-shell dashboard-shell">
    <section class="panel">
      <h2>অর্ডার ডিটেইল #<?= e($orderId) ?></h2>
      <p><strong>স্ট্যাটাস:</strong> <?= e($order['status']) ?></p>
      <p><strong>ঠিকানা:</strong> <?= e($order['address']) ?></p>
      <p><strong>মোট:</strong> ৳<?= e(number_format((float)$order['total'], 2)) ?></p>
      <div class="table-wrap">
        <table>
          <tr><th>প্রোডাক্ট</th><th>পরিমাণ</th><th>মূল্য</th><th>মোট</th></tr>
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
