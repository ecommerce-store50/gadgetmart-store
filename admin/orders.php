<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$allowed = ['pending','confirmed','packed','shipped','delivered','cancelled'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $status = $_POST['status'] ?? 'pending';
    if (in_array($status, $allowed, true)) db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status,(int)$_POST['order_id']]);
    header('Location: orders.php'); exit;
}
$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Orders</title><link rel="stylesheet" href="../assets/style.css"></head><body class="admin-body"><aside class="admin-sidebar"><h2>GadgetMart</h2><nav><a href="index.php">ড্যাশবোর্ড</a><a href="products.php">প্রোডাক্ট</a><a class="active" href="orders.php">অর্ডার</a><a href="settings.php">সেটিংস</a><a href="logout.php">লগআউট</a></nav></aside><main class="admin-main"><div class="topbar"><h1>অর্ডার ম্যানেজমেন্ট</h1></div><section class="panel"><div class="table-wrap"><table><tr><th>ID</th><th>কাস্টমার</th><th>ফোন</th><th>মোট</th><th>পেমেন্ট</th><th>স্ট্যাটাস</th><th>আপডেট</th></tr><?php foreach($orders as $o):?><tr><td><a href="order_detail.php?id=<?=e($o['id'])?>">#<?=e($o['id'])?></a></td><td><?=e($o['customer_name'])?></td><td><?=e($o['phone'])?></td><td>৳<?=e(number_format((float)$o['total'],2))?></td><td><?=e($o['payment_method']??'cod')?> / <?=e($o['payment_status']??'unpaid')?></td><td><?=e($o['status'])?></td><td><form method="post" class="inline-form"><input type="hidden" name="order_id" value="<?=e($o['id'])?>"><select name="status"><?php foreach($allowed as $s):?><option value="<?=e($s)?>" <?=$o['status']===$s?'selected':''?>><?=e($s)?></option><?php endforeach;?></select><button class="btn btn-secondary">আপডেট</button></form></td></tr><?php endforeach;?></table></div></section></main></body></html>
