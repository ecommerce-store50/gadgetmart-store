<?php
require __DIR__ . '/config/config.php';
session_start();

if (empty($_SESSION['user_id'])) { header('Location: user/login.php'); exit; }

function checkoutCartItems(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ') AND is_active = 1');
    $stmt->execute($ids);
    $items = [];
    foreach ($stmt->fetchAll() as $product) {
        $qty = max(1, (int)($cart[$product['id']]['qty'] ?? 1));
        $items[] = ['product' => $product, 'qty' => $qty];
    }
    return $items;
}

$items = checkoutCartItems();
if (!$items) { header('Location: cart.php'); exit; }

$deliveryFee = (float)setting('delivery_fee', '50');
$couponCode = trim(setting('coupon_code', ''));
$couponDiscount = (float)setting('coupon_discount', '0');
$userStmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $allowedMethods = ['cod', 'bkash_demo', 'nagad_demo', 'card_demo'];
    if (!in_array($paymentMethod, $allowedMethods, true)) $paymentMethod = 'cod';

    foreach ($items as $row) {
        if ($row['qty'] > (int)$row['product']['stock']) {
            $error = '“' . $row['product']['name'] . '” এর পর্যাপ্ত স্টক নেই।';
            break;
        }
    }

    $name = trim($_POST['name'] ?? ($user['name'] ?? ''));
    $phone = trim($_POST['phone'] ?? ($user['phone'] ?? ''));
    $email = trim($_POST['email'] ?? ($user['email'] ?? ''));
    $address = trim($_POST['address'] ?? ($user['address'] ?? ''));
    $enteredCoupon = strtoupper(trim($_POST['coupon'] ?? ''));

    if (!$error && ($name === '' || $phone === '' || $address === '')) $error = 'নাম, ফোন ও ঠিকানা অবশ্যই দিতে হবে।';

    $subtotal = 0;
    foreach ($items as $row) $subtotal += (float)$row['product']['price'] * $row['qty'];
    $discount = ($couponCode !== '' && $enteredCoupon === strtoupper($couponCode)) ? $couponDiscount : 0;
    $total = max(0, $subtotal + $deliveryFee - $discount);

    if (!$error) {
        $paymentStatus = $paymentMethod === 'cod' ? 'unpaid' : 'demo_paid';
        $paymentReference = $paymentMethod === 'cod' ? null : 'DEMO-' . strtoupper(bin2hex(random_bytes(4)));
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, phone, email, address, total, status, payment_method, payment_status, payment_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $orderStmt->execute([$_SESSION['user_id'], $name, $phone, $email, $address, $total, 'pending', $paymentMethod, $paymentStatus, $paymentReference]);
            $orderId = $pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
            $logStmt = $pdo->prepare('INSERT INTO stock_logs (product_id, change_qty, reason, order_id) VALUES (?, ?, ?, ?)');
            foreach ($items as $row) {
                $p = $row['product']; $qty = $row['qty'];
                $stockStmt->execute([$qty, $p['id'], $qty]);
                if ($stockStmt->rowCount() !== 1) throw new RuntimeException('স্টক পরিবর্তন করা যায়নি।');
                $itemStmt->execute([$orderId, $p['id'], $p['name'], $qty, $p['price']]);
                $logStmt->execute([$p['id'], -$qty, 'Order placed', $orderId]);
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            header('Location: user/dashboard.php?success=1'); exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'অর্ডার তৈরি করা যায়নি। আবার চেষ্টা করুন।';
        }
    }
}

$subtotal = 0;
foreach ($items as $row) $subtotal += (float)$row['product']['price'] * $row['qty'];
$total = $subtotal + $deliveryFee;
?>
<!doctype html>
<html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Checkout</title><link rel="stylesheet" href="assets/style.css"></head>
<body><header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?= e(setting('site_name','GadgetMart')) ?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="cart.php">কার্ট</a></div></header>
<main class="page-shell compact"><section class="panel-form"><h2>চেকআউট</h2><?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?><form method="post" class="stacked-form">
<label>নাম<input name="name" value="<?= e($user['name'] ?? '') ?>" required></label><label>ফোন<input name="phone" value="<?= e($user['phone'] ?? '') ?>" required></label><label>ইমেইল<input name="email" type="email" value="<?= e($user['email'] ?? '') ?>"></label><label>ঠিকানা<textarea name="address" required><?= e($user['address'] ?? '') ?></textarea></label><label>কুপন কোড<input name="coupon" placeholder="যদি থাকে"></label>
<fieldset><legend>পেমেন্ট পদ্ধতি</legend><label><input type="radio" name="payment_method" value="cod" checked> ক্যাশ অন ডেলিভারি (সক্রিয়)</label><label><input type="radio" name="payment_method" value="bkash_demo"> bKash (ডামি/টেস্ট)</label><label><input type="radio" name="payment_method" value="nagad_demo"> Nagad (ডামি/টেস্ট)</label><label><input type="radio" name="payment_method" value="card_demo"> Card (ডামি/টেস্ট)</label><small>ডামি পেমেন্টে কোনো আসল টাকা কাটা হবে না।</small></fieldset>
<div class="order-summary"><h3>অর্ডার সামারি</h3><?php foreach ($items as $row): ?><div class="summary-row"><span><?= e($row['product']['name']) ?> × <?= e($row['qty']) ?></span><span>৳<?= e(number_format($row['product']['price'] * $row['qty'], 2)) ?></span></div><?php endforeach; ?><div class="summary-row"><span>ডেলিভারি</span><span>৳<?= e(number_format($deliveryFee, 2)) ?></span></div><div class="summary-row total"><span>মোট</span><strong>৳<?= e(number_format($total, 2)) ?></strong></div></div><button class="btn btn-primary full-width">অর্ডার নিশ্চিত করুন</button></form></section></main></body></html>
