<?php
require __DIR__ . '/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header('Location: user/login.php'); exit; }

function cartItemsForCheckout(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ') AND is_active = 1');
    $stmt->execute($ids);
    $items = [];
    foreach ($stmt->fetchAll() as $product) {
        $qty = (int)($cart[$product['id']]['qty'] ?? 1);
        $items[] = ['product' => $product, 'qty' => $qty];
    }
    return $items;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = cartItemsForCheckout();
    if (!$items) {
        header('Location: cart.php');
        exit;
    }

    foreach ($items as $row) {
        $product = $row['product'];
        $qty = (int)$row['qty'];
        if ($qty > (int)$product['stock']) {
            header('Location: cart.php?error=stock');
            exit;
        }
    }

    $user = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $user->execute([$_SESSION['user_id']]);
    $user = $user->fetch();

    $total = 0.0;
    foreach ($items as $row) {
        $p = $row['product'];
        $total += (float)$p['price'] * (int)$row['qty'];
    }

    $name = trim($_POST['name'] ?? ($user['name'] ?? ''));
    $phone = trim($_POST['phone'] ?? ($user['phone'] ?? ''));
    $email = trim($_POST['email'] ?? ($user['email'] ?? ''));
    $address = trim($_POST['address'] ?? ($user['address'] ?? ''));

    if ($name === '' || $phone === '' || $address === '') {
        header('Location: cart.php?error=missing-data');
        exit;
    }

    $stmt = db()->prepare('INSERT INTO orders (user_id, customer_name, phone, email, address, total, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $name, $phone, $email, $address, $total, 'pending']);
    $orderId = db()->lastInsertId();

    $orderItemStmt = db()->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
    foreach ($items as $row) {
        $p = $row['product'];
        $qty = (int)$row['qty'];
        $orderItemStmt->execute([$orderId, $p['id'], $p['name'], $qty, $p['price']]);

        $stockUpdate = db()->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
        $stockUpdate->execute([$qty, $p['id'], $qty]);
    }

    $_SESSION['cart'] = [];
    header('Location: user/dashboard.php?success=1');
    exit;
}

$items = cartItemsForCheckout();
$total = 0.0;
foreach ($items as $row) { $total += (float)$row['product']['price'] * (int)$row['qty']; }
$user = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout - <?= e(setting('site_name','GadgetMart')) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?= e(setting('site_name','GadgetMart')) ?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="cart.php">কার্ট</a></div></header>
  <main class="page-shell compact">
    <section class="panel-form">
      <h2>চেকআউট</h2>
      <form method="post" class="stacked-form">
        <label>নাম<input name="name" value="<?= e($user['name']) ?>" required></label>
        <label>ফোন<input name="phone" value="<?= e($user['phone'] ?? '') ?>" required></label>
        <label>ইমেইল<input name="email" type="email" value="<?= e($user['email']) ?>" required></label>
        <label>ঠিকানা<textarea name="address" required><?= e($user['address'] ?? '') ?></textarea></label>
        <div class="order-summary">
          <h3>অর্ডার সামারি</h3>
          <?php foreach ($items as $row): $p = $row['product']; ?>
            <div class="summary-row"><span><?= e($p['name']) ?> x <?= e($row['qty']) ?></span><span>৳<?= e(number_format((float)$p['price'] * (int)$row['qty'], 2)) ?></span></div>
          <?php endforeach; ?>
          <div class="summary-row total"><span>মোট</span><strong>৳<?= e(number_format((float)$total, 2)) ?></strong></div>
        </div>
        <button type="submit" class="btn btn-primary full-width">অর্ডার নিশ্চিত করুন</button>
      </form>
    </section>
  </main>
</body>
</html>
