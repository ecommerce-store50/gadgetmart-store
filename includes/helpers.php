<?php
require __DIR__ . '/../config/config.php';
session_start();
function siteName(): string { return setting('site_name','GadgetMart'); }
function siteTagline(): string { return setting('site_tagline','সেরা গ্যাজেটস সেরা দামে'); }
function userLoggedIn(): bool { return !empty($_SESSION['user_id']); }
function currentUser(): ?array { if (!userLoggedIn()) return null; static $user; if ($user !== null) return $user; $q=db()->prepare('SELECT * FROM users WHERE id=? LIMIT 1');$q->execute([$_SESSION['user_id']]);return $user=$q->fetch()?:null; }
function cartCount(): int { $n=0;foreach($_SESSION['cart']??[] as $row)$n+=(int)($row['qty']??1);return $n; }
function cartItems(): array { $cart=$_SESSION['cart']??[];if(!$cart)return []; $ids=array_keys($cart);$marks=implode(',',array_fill(0,count($ids),'?'));$q=db()->prepare("SELECT * FROM products WHERE id IN ($marks) AND is_active=1");$q->execute($ids);$items=[];foreach($q->fetchAll() as $p)$items[]=['product'=>$p,'qty'=>max(1,(int)($cart[$p['id']]['qty']??1))];return $items; }
function addToCart(int $id,int $qty=1): void { $q=db()->prepare('SELECT id,stock FROM products WHERE id=? AND is_active=1 LIMIT 1');$q->execute([$id]);$p=$q->fetch();if(!$p||$p['stock']<1)return;$cart=$_SESSION['cart']??[];$old=(int)($cart[$id]['qty']??0);$cart[$id]=['qty'=>min((int)$p['stock'],$old+max(1,$qty))];$_SESSION['cart']=$cart; }
function updateCartQty(int $id,int $qty): void { $cart=$_SESSION['cart']??[];if($qty<=0){unset($cart[$id]);}else{$q=db()->prepare('SELECT stock FROM products WHERE id=? AND is_active=1');$q->execute([$id]);$stock=(int)$q->fetchColumn();$cart[$id]=['qty'=>min($qty,$stock)];}$_SESSION['cart']=$cart; }
function cartTotal(): float { $total=0;foreach(cartItems() as $row)$total+=(float)$row['product']['price']*$row['qty'];return $total; }
function orderStatuses(): array { return ['pending','confirmed','packed','shipped','delivered','cancelled']; }
