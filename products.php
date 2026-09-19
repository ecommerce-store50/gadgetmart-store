<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

session_start();
if (!empty($_GET['category'])) {
    $category = (int)$_GET['category'];
    $products = db()->prepare('SELECT * FROM products WHERE is_active = 1 AND category_id = ? ORDER BY id DESC');
    $products->execute([$category]);
    $products = $products->fetchAll();
} else {
    $products = db()->query('SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC')->fetchAll();
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products - <?=e(siteName())?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?=e(siteName())?></strong></div></div>
    <div class="header-right"><a href="index.php">হোম</a><a href="cart.php">কার্ট (<?=e(cartCount())?>)</a></div>
  </header>
  <main class="page-shell">
    <section class="section-block">
      <div class="section-title-row"><h2>সব প্রোডাক্ট</h2></div>
      <div class="product-grid">
        <?php foreach ($products as $product): ?>
          <article class="product-card">
            <div class="img-box">📦</div>
            <div class="product-meta">
              <h3><?=e($product['name'])?></h3>
              <div class="rating">★★★★★ <span>(<?=e($product['rating'])?>)</span></div>
              <div class="price-row"><span class="price">৳<?=e(number_format((float)$product['price'], 2))?></span><?php if ($product['old_price']): ?><span class="old-price">৳<?=e(number_format((float)$product['old_price'], 2))?></span><?php endif; ?></div>
              <div class="card-actions">
                <a class="btn btn-primary" href="product.php?id=<?=e($product['id'])?>">বিস্তারিত</a>
                <a class="btn btn-secondary" href="cart.php?add=<?=e($product['id'])?>">কার্টে যোগ</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
