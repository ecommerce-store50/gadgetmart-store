<?php
require __DIR__ . '/config/config.php';
$products = db()->query("SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_active=1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 12")->fetchAll();
$categories = db()->query("SELECT * FROM categories WHERE is_active=1 ORDER BY id")->fetchAll();

function productImageUrl($image): string {
    if (empty($image)) return 'https://placehold.co/800x800/1d2b40/ffffff?text=Gadget';
    if (preg_match('/^https?:\/\//', $image)) return $image;
    return '/' . ltrim($image, '/');
}
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e(setting('site_name','GadgetMart'))?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><div class="logo">G</div><strong><?=e(setting('site_name','GadgetMart'))?></strong><nav><a href="#categories">ক্যাটেগরি</a><a href="#products">প্রোডাক্ট</a><a href="admin/login.php">অ্যাডমিন</a></nav></header>
<main><section class="hero"><small>⚡ নতুন সিজন অফার</small><h1><?=e(setting('site_tagline','সেরা গ্যাজেটস সেরা দামে'))?></h1><p>স্মার্টফোন, ল্যাপটপ, অডিও, স্মার্টওয়াচ ও অ্যাকসেসরিজের সেরা সংগ্রহ।</p><a class="button" href="#products">এখন কেনাকাটা করুন</a></section>
<section id="categories"><h2>▦ ক্যাটেগরি ব্রাউজ করুন</h2><div class="grid categories"><?php foreach($categories as $c): ?><div class="card"><span class="emoji"><?=e($c['icon'])?></span><h3><?=e($c['name'])?></h3></div><?php endforeach; ?></div></section>
<section id="products"><h2>★ ফিচার্ড প্রোডাক্ট</h2><div class="grid products"><?php foreach($products as $p): ?><article class="product"><div class="product-image" style="background-image:url('<?= productImageUrl($p['image']) ?>');background-size:cover;background-position:center;"></div><small><?=e($p['category_name']??'গ্যাজেট')?></small><h3><?=e($p['name'])?></h3><div class="stars">★★★★★ <span>(<?=e($p['rating'])?>)</span></div><b>৳ <?=number_format((float)$p['price'])?></b><p>স্টক: <?=e($p['stock'])?></p></article><?php endforeach; ?></div></section></main>
<footer><h2><?=e(setting('site_name','GadgetMart'))?></h2><p><?=e(setting('footer_description'))?></p><p>☎ <?=e(setting('phone'))?> &nbsp; ✉ <?=e(setting('email'))?> &nbsp; 📍 <?=e(setting('address'))?></p><div class="social"><a href="<?=e(setting('facebook_url','#'))?>">Facebook</a><a href="<?=e(setting('instagram_url','#'))?>">Instagram</a><a href="<?=e(setting('youtube_url','#'))?>">YouTube</a><a href="<?=e(setting('twitter_url','#'))?>">X/Twitter</a><a href="<?=e(setting('whatsapp_url','#'))?>">WhatsApp</a></div></footer></body></html>
