<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

session_start();

$productId = (int)($_GET['id'] ?? 0);
$product = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
$product->execute([$productId]);
$product = $product->fetch();
if (!$product) { header('Location: index.php'); exit; }

if (!empty($_GET['add_to_cart'])) {
    addToCart((int)$_GET['add_to_cart']);
    header('Location: cart.php');
    exit;
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=e($product['name'])?> - <?=e(siteName())?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?=e(siteName())?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="products.php">ক্যাটেগরি</a><a href="cart.php">কার্ট</a></div></header>
  <main class="page-shell">
    <section class="detail-layout">
      <div class="detail-image">📦</div>
      <div class="detail-info">
        <h1><?=e($product['name'])?></h1>
        <div class="rating">★★★★★ <span>(<?=e($product['rating'])?>)</span></div>
        <div class="price-row">
          <span class="price">৳<?=e(number_format((float)$product['price'], 2))?></span>
          <?php if (!empty($product['old_price'])): ?><span class="old-price">৳<?=e(number_format((float)$product['old_price'], 2))?></span><?php endif; ?>
        </div>
        <p><?=e($product['description'] ?: 'এই প্রোডাক্টটি আপনার জন্য খুবই উপযুক্ত।')?></p>
        <div class="detail-actions">
          <a href="product.php?id=<?=e($product['id'])?>&add_to_cart=1" class="btn btn-primary">কার্টে যোগ করুন</a>
          <a href="checkout.php" class="btn btn-secondary">কেনাকাটা করুন</a>
        </div>
        <div class="stock-badge">স্টক: <?=e((int)$product['stock'])?></div>
      </div>
    </section>
  </main>
</body>
</html>
