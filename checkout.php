<?php
require __DIR__ . '/config/config.php';
session_start();
if (empty($_SESSION['user_id'])) { header('Location: user/login.php'); exit; }

function checkoutItems(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($marks) AND is_active = 1");
    $stmt->execute($ids);
    $items = [];
    foreach ($stmt->fetchAll() as $product) $items[] = ['product' => $product, 'qty' => max(1, (int)($cart[$product['id']]['qty'] ?? 1))];
    return $items;
}

$items = checkoutItems();
if (!$items) { header('Location: cart.php'); exit; }
$userStmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();
$error = '';
$deliveryFee = (float)setting('delivery_fee', '50');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $email = trim($_POST['email'] ?? ''); $address = trim($_POST['address'] ?? '');
    if ($name === '' || $phone === '' || $address === '') $error = 'নাম, ফোন ও ঠিকানা অবশ্যই দিতে হবে।';
    $subtotal = 0;
    foreach ($items as $row) { if ($row['qty'] > (int)$row['product']['stock']) $error = 'পর্যাপ্ত স্টক নেই।'; $subtotal += (float)$row['product']['price'] * $row['qty']; }
    if (!$error) {
        $pdo = db(); $total = $subtotal + $deliveryFee;
        try {
            $pdo->beginTransaction();
            $order = $pdo->prepare("INSERT INTO orders (user_id,customer_name,phone,email,address,total,status,payment_method,payment_status) VALUES (?,?,?,?,?,?,?,?,?)");
            $order->execute([$_SESSION['user_id'],$name,$phone,$email,$address,$total,'pending','cod','unpaid']);
            $orderId = $pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,quantity,unit_price) VALUES (?,?,?,?,?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');
            $logStmt = $pdo->prepare('INSERT INTO stock_logs (product_id,change_qty,reason,order_id) VALUES (?,?,?,?)');
            foreach ($items as $row) { $p=$row['product']; $qty=$row['qty']; $stockStmt->execute([$qty,$p['id'],$qty]); if ($stockStmt->rowCount() !== 1) throw new RuntimeException('Stock unavailable'); $itemStmt->execute([$orderId,$p['id'],$p['name'],$qty,$p['price']]); $logStmt->execute([$p['id'],-$qty,'Order placed',$orderId]); }
            $pdo->commit(); $_SESSION['cart']=[]; header('Location: user/dashboard.php?success=1'); exit;
        } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $error='অর্ডার তৈরি করা যায়নি। আবার চেষ্টা করুন।'; }
    }
} else { $subtotal=0; foreach ($items as $row) $subtotal += (float)$row['product']['price']*$row['qty']; }
$total = $subtotal + $deliveryFee;
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Checkout</title><link rel="stylesheet" href="assets/style.css"></head><body><header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?= e(setting('site_name','GadgetMart')) ?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="cart.php">কার্ট</a></div></header><main class="page-shell compact"><section class="panel-form"><h2>চেকআউট</h2><?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?><form method="post" class="stacked-form"><label>নাম<input name="name" value="<?= e($user['name'] ?? '') ?>" required></label><label>ফোন<input name="phone" value="<?= e($user['phone'] ?? '') ?>" required></label><label>ইমেইল<input name="email" type="email" value="<?= e($user['email'] ?? '') ?>"></label><label>ঠিকানা<textarea name="address" required><?= e($user['address'] ?? '') ?></textarea></label><div class="order-summary"><h3>অর্ডার সামারি</h3><?php foreach ($items as $row): ?><div class="summary-row"><span><?= e($row['product']['name']) ?> × <?= e($row['qty']) ?></span><span>৳<?= e(number_format((float)$row['product']['price']*$row['qty'],2)) ?></span></div><?php endforeach; ?><div class="summary-row"><span>ডেলিভারি</span><span>৳<?= e(number_format($deliveryFee,2)) ?></span></div><div class="summary-row total"><span>মোট</span><strong>৳<?= e(number_format($total,2)) ?></strong></div></div><button class="btn btn-primary full-width">ক্যাশ অন ডেলিভারি অর্ডার করুন</button></form></section></main></body></html>
