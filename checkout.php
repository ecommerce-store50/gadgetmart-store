<?php
require __DIR__ . '/config/config.php';
if (empty($_GET['id'])) { header('Location: index.php'); exit; }
$productId = (int)$_GET['id'];
$product = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
$product->execute([$productId]);
$product = $product->fetch();
if (!$product) { header('Location: index.php'); exit; }

function productImageUrl($image): string {
    if (empty($image)) return 'https://placehold.co/800x800/1d2b40/ffffff?text=Gadget';
    if (preg_match('/^https?:\/\//', $image)) return $image;
    return '/' . ltrim($image, '/');
}
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($product['name'])?> - <?=e(setting('site_name','GadgetMart'))?></title><link rel="stylesheet" href="assets/style.css"></head><body><header><div class="logo">G</div><strong><?=e(setting('site_name','GadgetMart'))?></strong><nav><a href="index.php">হোম</a><a href="products.php">প্রোডাক্ট</a></nav></header><main><section class="detail-layout"><div class="detail-image" style="background-image:url('<?= productImageUrl($product['image']) ?>');background-size:cover;background-position:center;"></div><div class="detail-info"><h1><?=e($product['name'])?></h1><div class="stars">★★★★★ <span>(<?=e($product['rating'])?>)</span></div><div class="price-wrap"><b>৳ <?=number_format((float)$product['price'])?></b><?php if(!empty($product['old_price'])): ?><span class="old-price">৳ <?=number_format((float)$product['old_price'])?></span><?php endif; ?></div><p><?=e($product['description'] ?: 'এই প্রোডাক্টটি আপনার জন্য খুবই উপযুক্ত।')?></p><div class="detail-actions"><a class="button" href="cart.php?add=<?=e($product['id'])?>">কার্টে যোগ করুন</a><a class="button secondary" href="products.php">আরও দেখুন</a></div><div class="stock-box">স্টক: <?=e((int)$product['stock'])?></div></div></section></main></body></html>
