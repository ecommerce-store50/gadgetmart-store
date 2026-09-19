<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';
session_start();
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_active=1 LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { header('Location: products.php'); exit; }
function productImage(?string $image): string { if (!$image) return 'https://placehold.co/800x600/1d2b40/ffffff?text=Gadget'; if (preg_match('/^https?:\\/\\//',$image)) return $image; return ltrim($image,'/'); }
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($product['name'])?> - <?=e(siteName())?></title><link rel="stylesheet" href="assets/style.css"></head><body><header><div class="logo">G</div><strong><?=e(siteName())?></strong><nav><a href="index.php">হোম</a><a href="products.php">প্রোডাক্ট</a><a href="cart.php">কার্ট</a></nav></header><main class="page-shell"><section class="detail-layout"><div class="detail-image" style="background-image:url('<?=e(productImage($product['image']))?>')"></div><div class="detail-info"><small><?=e($product['category_name']??'গ্যাজেট')?></small><h1><?=e($product['name'])?></h1><div class="stars">★★★★★ <span>(<?=e($product['rating'])?>)</span></div><div class="price-wrap"><b>৳ <?=e(number_format((float)$product['price'],2))?></b><?php if($product['old_price']): ?><span class="old-price">৳ <?=e(number_format((float)$product['old_price'],2))?></span><?php endif; ?></div><p><?=e($product['description']?:'এই প্রোডাক্টটি আপনার জন্য উপযুক্ত।')?></p><div class="stock-box">স্টক: <?=e($product['stock'])?></div><div class="detail-actions"><a class="button" href="cart.php?add=<?=e($product['id'])?>">কার্টে যোগ করুন</a><a class="button secondary" href="products.php">আরও প্রোডাক্ট</a></div></div></section></main></body></html>
