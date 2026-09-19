<?php
require __DIR__ . '/../config/config.php';
session_start();
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $oldPrice = $_POST['old_price'] !== '' ? (float)$_POST['old_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $sku = trim($_POST['sku'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $featured = !empty($_POST['is_featured']) ? 1 : 0;
    $bestseller = !empty($_POST['is_bestseller']) ? 1 : 0;
    $active = !empty($_POST['is_active']) ? 1 : 0;

    if ($name && $categoryId && $price >= 0) {
        if ($slug === '') {
            $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9\s-]/', '', $name)));
        }
        $stmt = db()->prepare('INSERT INTO products (category_id, name, slug, description, image, price, old_price, stock, sku, is_featured, is_bestseller, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$categoryId, $name, $slug, $description, $image, $price, $oldPrice, $stock, $sku, $featured, $bestseller, $active]);
        $message = 'প্রোডাক্ট সফলভাবে যোগ হয়েছে।';
    } else {
        $message = 'নাম, ক্যাটেগরি ও মূল্য অবশ্যই দিতে হবে।';
    }
}

$categories = db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY id')->fetchAll();
$products = db()->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <div class="topbar">
      <h1>প্রোডাক্ট ম্যানেজ</h1>
    </div>

    <section class="panel">
      <h3>নতুন প্রোডাক্ট যোগ</h3>
      <?php if ($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
      <form method="post" class="stacked-form small-form">
        <label>প্রোডাক্ট নাম
          <input type="text" name="name" required>
        </label>
        <label>Slug
          <input type="text" name="slug">
        </label>
        <label>ক্যাটেগরি
          <select name="category_id" required>
            <option value="">নির্বাচন করুন</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= e($c['id']) ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>মূল্য
          <input type="number" step="0.01" name="price" required>
        </label>
        <label>পুরনো মূল্য
          <input type="number" step="0.01" name="old_price">
        </label>
        <label>স্টক
          <input type="number" name="stock" value="0" required>
        </label>
        <label>SKU
          <input type="text" name="sku">
        </label>
        <label>ছবি URL
          <input type="text" name="image" placeholder="https://example.com/image.jpg">
        </label>
        <label>বর্ণনা
          <textarea name="description"></textarea>
        </label>
        <div class="checkbox-row">
          <label><input type="checkbox" name="is_featured" value="1"> Featured</label>
          <label><input type="checkbox" name="is_bestseller" value="1"> Best Seller</label>
          <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>
        <button type="submit" class="btn btn-primary">সেভ করুন</button>
      </form>
    </section>

    <section class="panel">
      <h3>সকল প্রোডাক্ট</h3>
      <div class="table-wrap">
        <table>
          <tr>
            <th>নাম</th>
            <th>ক্যাটেগরি</th>
            <th>মূল্য</th>
            <th>স্টক</th>
            <th>ফিচার্ড</th>
            <th>স্ট্যাটাস</th>
          </tr>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= e($p['name']) ?></td>
              <td><?= e($p['category_name'] ?? '-') ?></td>
              <td>৳<?= e(number_format((float)$p['price'], 2)) ?></td>
              <td><?= e((int)$p['stock']) ?></td>
              <td><?= $p['is_featured'] ? 'হ্যাঁ' : 'না' ?></td>
              <td><?= $p['is_active'] ? 'Active' : 'Inactive' ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
