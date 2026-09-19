<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/includes/helpers.php';

session_start();

$categories = db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY id')->fetchAll();
$products = db()->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 8')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(siteName()) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="public-page">
  <header class="topbar">
    <div class="brand-wrap">
      <button type="button" class="hamburger-btn" aria-label="Open menu">☰</button>
      <div class="logo-circle">G</div>
      <div class="brand-text"><strong><?= e(siteName()) ?></strong></div>
    </div>

    <nav class="main-nav" id="mainNav">
      <a href="index.php">হোম</a>
      <a href="products.php">প্রোডাক্ট</a>
      <a href="#categories">ক্যাটেগরি</a>
      <a href="cart.php">কার্ট (<?= e(cartCount()) ?>)</a>
      <?php if (userLoggedIn()): ?>
        <a href="user/dashboard.php">ড্যাশবোর্ড</a>
      <?php else: ?>
        <a href="user/login.php">লগইন</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="page-shell">
    <section class="hero">
      <small>⚡ নতুন সিজন অফার</small>
      <h1><?= e(siteTagline()) ?></h1>
      <p><?= e(setting('footer_description', 'স্মার্টফোন, ল্যাপটপ, অডিও, স্মার্টওয়াচ ও একাধিক গ্যাজেটস।')) ?></p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="products.php">নতুন জিনিস দেখুন</a>
        <?php if (!userLoggedIn()): ?>
          <a class="btn btn-secondary" href="user/register.php">একাউন্ট খুলুন</a>
        <?php endif; ?>
      </div>
    </section>

    <section id="categories" class="section-block">
      <div class="section-title-row">
        <h2>ক্যাটেগরি ব্রাউজ করুন</h2>
      </div>
      <div class="category-grid">
        <?php foreach ($categories as $c): ?>
          <a class="category-card" href="products.php?category=<?= e($c['id']) ?>">
            <span class="emoji"><?= e($c['icon'] ?: '📦') ?></span>
            <strong><?= e($c['name']) ?></strong>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section-block">
      <div class="section-title-row">
        <h2>ফিচার্ড প্রোডাক্ট</h2>
        <a class="btn btn-secondary" href="products.php">সব দেখুন</a>
      </div>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <article class="product-card">
            <div class="img-box">📦</div>
            <div class="product-meta">
              <h3><?= e($p['name']) ?></h3>
              <div class="rating">★★★★★ <span>(<?= e($p['rating'] ?? 4.8) ?>)</span></div>
              <div class="price-row">
                <span class="price">৳<?= e(number_format((float)$p['price'], 2)) ?></span>
                <?php if (!empty($p['old_price'])): ?>
                  <span class="old-price">৳<?= e(number_format((float)$p['old_price'], 2)) ?></span>
                <?php endif; ?>
              </div>
              <div class="card-actions">
                <a class="btn btn-primary" href="product.php?id=<?= e($p['id']) ?>">বিস্তারিত</a>
                <a class="btn btn-secondary" href="cart.php?add=<?= e($p['id']) ?>">কার্টে যোগ</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <h2><?= e(siteName()) ?></h2>
    <p><?= e(setting('footer_description', 'আপনার গ্যাজেটের গ্লোবাল স্টোর')) ?></p>
    <div class="footer-meta">
      <span>☎ <?= e(setting('phone', '')) ?></span>
      <span>✉ <?= e(setting('email', '')) ?></span>
      <span>📍 <?= e(setting('address', '')) ?></span>
    </div>
    <div class="social">
      <?php if (setting('facebook_url', '') !== ''): ?><a href="<?= e(setting('facebook_url')) ?>">Facebook</a><?php endif; ?>
      <?php if (setting('instagram_url', '') !== ''): ?><a href="<?= e(setting('instagram_url')) ?>">Instagram</a><?php endif; ?>
      <?php if (setting('youtube_url', '') !== ''): ?><a href="<?= e(setting('youtube_url')) ?>">YouTube</a><?php endif; ?>
      <?php if (setting('twitter_url', '') !== ''): ?><a href="<?= e(setting('twitter_url')) ?>">Twitter</a><?php endif; ?>
    </div>
  </footer>

  <script>
    const btn = document.querySelector('.hamburger-btn');
    const nav = document.getElementById('mainNav');
    if (btn && nav) {
      btn.addEventListener('click', () => {
        document.body.classList.toggle('menu-open');
        nav.classList.toggle('open');
      });
    }
  </script>
</body>
</html>
