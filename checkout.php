<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

session_start();
if (!userLoggedIn()) { header('Location: user/login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = currentUser();
    $items = cartItems();
    $total = cartTotal();
    if (!$items) { header('Location: cart.php'); exit; }

    $orderStmt = db()->prepare('INSERT INTO orders(user_id, customer_name, phone, email, address, total, status) VALUES(?,?,?,?,?,?,?)');
    $orderStmt->execute([
        $user['id'],
        $user['name'],
        $user['phone'] ?? '',
        $user['email'],
        $user['address'] ?? '',
        $total,
        'pending'
    ]);
    $orderId = db()->lastInsertId();

    $itemStmt = db()->prepare('INSERT INTO order_items(order_id, product_id, product_name, quantity, unit_price) VALUES(?,?,?,?,?)');
    foreach ($items as $row) {
        $product = $row['product'];
        $itemStmt->execute([$orderId, $product['id'], $product['name'], $row['qty'], $product['price']]);
    }

    clearCart();
    header('Location: user/dashboard.php?success=1');
    exit;
}

$items = cartItems();
$total = cartTotal();
$user = currentUser();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout - <?=e(siteName())?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?=e(siteName())?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="cart.php">কার্ট</a></div></header>
  <main class="page-shell compact">
    <section class="panel-form">
      <h2>চেকআউট</h2>
      <form method="post" class="stacked-form">
        <label>নাম<input name="name" value="<?=e($user['name'])?>" required></label>
        <label>ফোন<input name="phone" value="<?=e($user['phone'] ?? '')?>" required></label>
        <label>ইমেইল<input name="email" type="email" value="<?=e($user['email'])?>" required></label>
        <label>ঠিকানা<textarea name="address" required><?=e($user['address'] ?? '')?></textarea></label>
        <div class="order-summary">
          <h3>অর্ডার সামারি</h3>
          <?php foreach ($items as $row): $p = $row['product']; ?>
            <div class="summary-row"><span><?=e($p['name'])?> x <?=e($row['qty'])?></span><span>৳<?=e(number_format((float)$p['price'] * (int)$row['qty'], 2))?></span></div>
          <?php endforeach; ?>
          <div class="summary-row total"><span>মোট</span><strong>৳<?=e(number_format((float)$total, 2))?></strong></div>
        </div>
        <button type="submit" class="btn btn-primary full-width">অর্ডার নিশ্চিত করুন</button>
      </form>
    </section>
  </main>
</body>
</html>
