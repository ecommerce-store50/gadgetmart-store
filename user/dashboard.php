<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/helpers.php';

session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$user = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();

$orders = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="user-body">
  <header class="topbar user-topbar">
    <div class="brand-wrap"><div class="logo-circle">G</div><div class="brand-text"><strong><?=e(siteName())?></strong></div></div>
    <div class="header-right">
      <a href="../index.php">হোম</a>
      <a href="logout.php">লগআউট</a>
    </div>
  </header>

  <main class="page-shell dashboard-shell">
    <section class="user-profile">
      <div>
        <h2>স্বাগতম, <?=e($user['name'])?></h2>
        <p><?=e($user['email'])?></p>
      </div>
      <a href="../products.php" class="btn btn-primary">আরও শপ করুন</a>
    </section>

    <section class="panel">
      <h3>আপনার অর্ডার</h3>
      <?php if (!$orders): ?><div class="alert success">এখনো কোন অর্ডার নেই।</div><?php else: ?>
      <div class="table-wrap">
        <table>
          <tr><th>অর্ডার</th><th>তারিখ</th><th>মোট</th><th>স্ট্যাটাস</th></tr>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?=e($o['id'])?></td>
              <td><?=e($o['created_at'])?></td>
              <td>৳<?=e(number_format((float)$o['total'], 2))?></td>
              <td><span class="status-badge status-<?=e($o['status'])?>"><?=e($o['status'])?></span></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
