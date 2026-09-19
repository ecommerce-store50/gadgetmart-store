<?php
require dirname(__DIR__) . '/config/config.php';
session_start();

function siteName(): string { return setting('site_name', 'GadgetMart'); }
function siteTagline(): string { return setting('site_tagline', 'সেরা গ্যাজেটস সেরা দামে'); }
function userLoggedIn(): bool { return !empty($_SESSION['user_id']); }
function currentUser(): ?array {
    if (!userLoggedIn()) return null;
    static $user = null;
    if ($user !== null) return $user;
    $q = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $q->execute([$_SESSION['user_id']]);
    $user = $q->fetch() ?: null;
    return $user;
}
function cartCount(): int {
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $row) { $count += (int)($row['qty'] ?? 1); }
    return $count;
}
function cartItems(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $q = db()->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ') AND is_active = 1');
    $q->execute($ids);
    $items = [];
    foreach ($q->fetchAll() as $p) {
        $qty = (int)($cart[$p['id']]['qty'] ?? 1);
        $items[] = ['product' => $p, 'qty' => $qty];
    }
    return $items;
}
function addToCart(int $productId, int $qty = 1): void {
    $cart = $_SESSION['cart'] ?? [];
    $cart[$productId] = ['qty' => ($cart[$productId]['qty'] ?? 0) + $qty];
    $_SESSION['cart'] = $cart;
}
function updateCartQty(int $productId, int $qty): void {
    $cart = $_SESSION['cart'] ?? [];
    if ($qty <= 0) { unset($cart[$productId]); }
    else { $cart[$productId] = ['qty' => $qty]; }
    $_SESSION['cart'] = $cart;
}
function clearCart(): void { $_SESSION['cart'] = []; }
function cartTotal(): float {
    $total = 0.0;
    foreach (cartItems() as $row) {
        $total += (float)$row['product']['price'] * (int)$row['qty'];
    }
    return $total;
}
function orderStatuses(): array { return ['pending','confirmed','processing','shipped','delivered','cancelled']; }
