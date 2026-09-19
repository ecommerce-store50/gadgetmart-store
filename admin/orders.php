<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status'])) {
    $id = (int)($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');
    db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
    $saveMsg = 'অর্ডার স্ট্যাটাস আপডেট করা হয়েছে।';
}

$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Management</title>
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
    <div class="topbar"><h1>অর্ডার ম্যানেজ</h1></div>
    <?php if (!empty($saveMsg)): ?><div class="alert success"><?=e($saveMsg)?></div><?php endif; ?>
    <section class="panel">
      <div class="table-wrap">
        <table>
          <tr><th>আইডি</th><th>ক্লায়েন্ট</th><th>ফোন</th><th>মোট</th><th>স্ট্যাটাস</th><th>সংশোধন</th></tr>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?=e($o['id'])?></td>
              <td><?=e($o['customer_name'])?></td>
              <td><?=e($o['phone'])?></td>
              <td>৳<?=e(number_format((float)$o['total'], 2))?></td>
              <td><?=e($o['status'])?></td>
              <td>
                <form method="post" class="inline-form">
                  <input type="hidden" name="order_id" value="<?=e($o['id'])?>">
                  <select name="status">
                    <?php foreach (orderStatuses() as $status): ?>
                      <option value="<?=e($status)?>" <?=($o['status'] === $status) ? 'selected' : ''?>><?=e($status)?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-secondary" type="submit">আপডেট</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
