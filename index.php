<?php
session_start();
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

$products = db()->query('SELECT * FROM products WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC LIMIT 12')->fetchAll();
$categories = db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY id')->fetchAll();

if (!empty($_GET['add_to_cart'])) {
    addToCart((int)$_GET['add_to_cart']);
    header('Location: cart.php');
    exit;
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=e(siteName())?> - Home</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <div class="brand-wrap">
      <div class="logo-circle">G</div>
      <div class="brand-text"><strong><?=e(siteName())?></strong></div>
    </div>
    <div class="header-right">
      <a href="products.php">ক্যাটেগরি</a>
      <a href="cart.php">কার্ট (<?=e(cartCount())?>)</a>
      <?php if (userLoggedIn()): ?>
        <a href="user/dashboard.php">ড্যাশবোর্ড</a>
        <a href="user/logout.php">লগআউট</a>
      <?php else: ?>
        <a href="user/login.php">লগইন</a>
        <a href="user/register.php">রেজিস্টার</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="page-shell">
    <section class="hero-banner">
      <span class="badge">⚡ নতুন সিজন অফার</span>
      <h1><?=e(siteTagline())?></h1>
      <p>স্মার্টফোন, ল্যাপটপ, অডিও, স্মার্টওয়াচ ও অ্যাকসেসরিজের সেরা সংগ্রহ।</p>
      <div class="hero-actions">
        <a href="products.php" class="btn btn-primary">এখন কেনাকাটা করুন</a>
      </div>
      <div class="stats-row">
        <div><strong>5000+</strong><span>প্রোডাক্ট</span></div>
        <div><strong>1000+</strong><span>কাস্টমার</span></div>
        <div><strong>96%</strong><span>সন্তুষ্টি</span></div>
      </div>
    </section>

    <section class="section-block">
      <div class="section-title-row"><h2>ক্যাটেগরি ব্রাউজ করুন</h2></div>
      <div class="category-grid">
        <?php foreach ($categories as $category): ?>
          <a class="category-card" href="products.php?category=<?=e($category['id'])?>">
            <div class="emoji"><?=e($category['icon'])?></div>
            <h3><?=e($category['name'])?></h3>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section-block">
      <div class="section-title-row"><h2>ফিচার্ড প্রোডাক্ট</h2></div>
      <div class="product-grid">
        <?php foreach ($products as $product): ?>
          <article class="product-card">
            <div class="img-box">📦</div>
            <div class="product-meta">
              <small><?=e($product['category_id'])?></small>
              <h3><?=e($product['name'])?></h3>
              <div class="rating">★★★★★ <span>(<?=e($product['rating'])?>)</span></div>
              <div class="price-row">
                <span class="price">৳<?=e(number_format((float)$product['price'], 2))?></span>
                <?php if (!empty($product['old_price'])): ?><span class="old-price">৳<?=e(number_format((float)$product['old_price'], 2))?></span><?php endif; ?>
              </div>
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

  <footer class="site-footer">
    <div class="footer-top">
      <div>
        <h3><?=e(siteName())?></h3>
        <p><?=e(setting('footer_description', 'আপনার গ্যাজেট ডেস্টিনেশন।'))?></p>
      </div>
      <div class="social-links">
        <a href="<?=e(setting('facebook_url', '#'))?>">Facebook</a>
        <a href="<?=e(setting('instagram_url', '#'))?>">Instagram</a>
        <a href="<?=e(setting('youtube_url', '#'))?>">YouTube</a>
        <a href="<?=e(setting('twitter_url', '#'))?>">X</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>📞 <?=e(setting('phone', '+880 1234-567890'))?></span>
      <span>✉️ <?=e(setting('email', 'support@gadgetmart.com'))?></span>
      <span>📍 <?=e(setting('address', 'ঢাকা, বাংলাদেশ'))?></span>
    </div>
  </footer>
</body>
</html>
