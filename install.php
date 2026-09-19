<?php
require __DIR__ . '/config/config.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){$email=trim($_POST['email']);$pass=$_POST['password'];$q=db()->prepare('INSERT INTO admins(name,email,password_hash) VALUES(?,?,?)');try{$q->execute([trim($_POST['name']),$email,password_hash($pass,PASSWORD_DEFAULT)]);$message='Admin তৈরি হয়েছে। এখন install.php মুছে দিন এবং admin/login.php খুলুন।';}catch(Throwable $e){$message='Admin তৈরি করা যায়নি: '.$e->getMessage();}}
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><title>Install</title><link rel="stylesheet" href="assets/style.css"></head><body><main><section class="hero"><h1>প্রথম Admin তৈরি</h1><p><?=e($message)?></p><form method="post"><input name="name" placeholder="Name" required><br><input name="email" type="email" placeholder="Email" required><br><input name="password" type="password" placeholder="Password" required><br><button class="button">Create Admin</button></form></section></main></body></html>
