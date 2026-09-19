<?php
require __DIR__ . '/config/config.php';
session_start();

function siteName(): string { return setting('site_name', 'GadgetMart'); }
function siteTagline(): string { return setting('site_tagline', 'সেরা গ্যাজেটস সেরা দামে'); }
function userLoggedIn(): bool { return !empty($_SESSION['user_id']); }
function currentUser(): ?array { if (!userLoggedIn()) return null; static $u = null; if ($u !== null) return $u; $u = db()->prepare('SELECT * FROM users WHERE id = ?'); $u->execute([$_SESSION['user_id']]); $row = $u->fetch(); return $row ?: null; }

function cartCount(): int { $cart = $_SESSION['cart'] ?? []; $count = 0; foreach ($cart as $item) $count += (int)($item['qty'] ?? 1); return $count; }

function cartItems(): array { $items = []; $cart = $_SESSION['cart'] ?? []; if (!$cart) return $items; $ids = array_keys($cart); if (!$ids) return $items; $placeholders = implode(',', array_fill(0, count($ids), '?')); $stmt = db()->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ') AND is_active = 1'); $stmt->execute($ids); foreach ($stmt->fetchAll() as $product) { $qty = (int)($cart[$product['id']]['qty'] ?? 1); $items[] = ['product' => $product, 'qty' => $qty]; } return $items; }

function addToCart(int $productId, int $qty = 1): void { $cart = $_SESSION['cart'] ?? []; $cart[$productId] = ['qty' => ($cart[$productId]['qty'] ?? 0) + $qty]; $_SESSION['cart'] = $cart; }

function updateCartQty(int $productId, int $qty): void { $cart = $_SESSION['cart'] ?? []; if ($qty <= 0) unset($cart[$productId]); else $cart[$productId] = ['qty' => $qty]; $_SESSION['cart'] = $cart; }

function clearCart(): void { $_SESSION['cart'] = []; }

function cartTotal(): float { $total = 0; foreach (cartItems() as $row) { $total += (float)$row['product']['price'] * (int)$row['qty']; } return $total; }

function orderStatuses(): array { return ['pending','confirmed','processing','shipped','delivered','cancelled']; }
