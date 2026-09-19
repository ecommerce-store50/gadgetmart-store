<?php
require __DIR__ . '/../config/config.php';
session_start();
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') { $s=db()->prepare('SELECT * FROM admins WHERE email=?');$s->execute([trim($_POST['email']??'')]);$a=$s->fetch();if($a&&password_verify($_POST['password']??'',$a['password_hash'])){$_SESSION['admin_id']=$a['id'];header('Location: index.php');exit;}$error='ইমেইল অথবা পাসওয়ার্ড সঠিক নয়।'; }
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="../assets/style.css"></head><body><main><section class="hero"><h1>Admin Login</h1><?php if($error):?><p><?=e($error)?></p><?php endif;?><form method="post"><input name="email" type="email" placeholder="Email" required><br><input name="password" type="password" placeholder="Password" required><br><button class="button">Login</button></form></section></main></body></html>
