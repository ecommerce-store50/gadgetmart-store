<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

session_start();

if (!empty($_GET['add'])) {
    addToCart((int)$_GET['add']);
    header('Location: cart.php');
    exit;
}

if (!empty($_POST['qty'])) {
    foreach ($_POST['qty'] as $productId => $qty) {
        updateCartQty((int)$productId, (int)$qty);
    }
    header('Location: cart.php');
    exit;
}

$items = cartItems();
$total = cartTotal();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cart - <?=e(siteName())?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar"><div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?=e(siteName())?></strong></div></div><div class="header-right"><a href="index.php">হোম</a><a href="products.php">ক্যাটেগরি</a></div></header>
  <main class="page-shell">
    <section class="section-block">
      <div class="section-title-row"><h2>কার্ট</h2></div>
      <?php if (!$items): ?>
        <div class="empty-state">কার্ট খালি।</div>
      <?php else: ?>
        <form method="post" class="cart-stack">
          <?php foreach ($items as $row): $product = $row['product']; ?>
            <div class="cart-item">
              <div class="img-box mini">📦</div>
              <div class="cart-item-info">
                <h3><?=e($product['name'])?></h3>
                <p>৳<?=e(number_format((float)$product['price'], 2))?></p>
              </div>
              <input type="number" min="1" name="qty[<?=e($product['id'])?>]" value="<?=e($row['qty'])?>">
            </div>
          <?php endforeach; ?>
          <div class="cart-summary">
            <strong>মোট: ৳<?=e(number_format((float)$total, 2))?></strong>
            <button class="btn btn-primary" type="submit">আপডেট</button>
            <a class="btn btn-secondary" href="checkout.php">চেকআউট</a>
          </div>
        </form>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
