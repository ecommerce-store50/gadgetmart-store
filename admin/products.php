<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = (int)($_POST['category'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $oldPrice = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(str_replace(' ', '-', $name));
    $description = trim($_POST['description'] ?? '');
    $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
    $isBestSeller = !empty($_POST['is_bestseller']) ? 1 : 0;
    $image = trim($_POST['image'] ?? '');
    $stmt = db()->prepare('INSERT INTO products(category_id, name, slug, description, image, price, old_price, stock, is_featured, is_bestseller, is_active) VALUES(?,?,?,?,?,?,?,?,?,?,1)');
    $stmt->execute([$category, $name, $slug, $description, $image, $price, $oldPrice, $stock, $isFeatured, $isBestSeller]);
    $saveMsg = 'প্রোডাক্ট সফলভাবে যোগ হয়েছে।';
}

$categories = db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name')->fetchAll();
$products = db()->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Management</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <h2>GadgetMart</h2>
    <nav>
      <a href="index.php">ড্যাশবোর্ড</a>
      <a href="products.php" class="active">প্রোডাক্ট ম্যানেজ</a>
      <a href="orders.php">অর্ডার ম্যানেজ</a>
      <a href="settings.php">সাইট সেটিংস</a>
      <a href="logout.php">লগআউট</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="topbar"><h1>প্রোডাক্ট ম্যানেজ</h1></div>
    <section class="panel">
      <h3>নতুন প্রোডাক্ট যোগ</h3>
      <?php if (!empty($saveMsg)): ?><div class="alert success"><?=e($saveMsg)?></div><?php endif; ?>
      <form method="post" class="stacked-form small-form">
        <label>নাম<input name="name" required></label>
        <label>Slug<input name="slug"></label>
        <label>ক্যাটেগরি
          <select name="category">
            <?php foreach ($categories as $c): ?><option value="<?=e($c['id'])?>"><?=e($c['name'])?></option><?php endforeach; ?>
          </select>
        </label>
        <label>মূল্য<input name="price" type="number" step="0.01" required></label>
        <label>পুরানো মূল্য<input name="old_price" type="number" step="0.01"></label>
        <label>স্টক<input name="stock" type="number" required></label>
        <label>ছবি URL<input name="image" placeholder="https://..."></label>
        <label>বর্ণনা<textarea name="description"></textarea></label>
        <div class="checkbox-row">
          <label><input type="checkbox" name="is_featured"> Featured</label>
          <label><input type="checkbox" name="is_bestseller"> Best Seller</label>
        </div>
        <button type="submit" class="btn btn-primary">সেভ করুন</button>
      </form>
    </section>

    <section class="panel">
      <h3>মোট প্রোডাক্ট</h3>
      <div class="table-wrap">
        <table>
          <tr><th>নাম</th><th>ক্যাটেগরি</th><th>মূল্য</th><th>স্টক</th><th>স্ট্যাটাস</th></tr>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?=e($p['name'])?></td>
              <td><?=e($p['category_name'] ?? '-')?></td>
              <td>৳<?=e(number_format((float)$p['price'], 2))?></td>
              <td><?=e($p['stock'])?></td>
              <td><?=e($p['is_active'] ? 'Active' : 'Inactive')?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
