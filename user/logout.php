<?php
session_start();
if (!empty($_SESSION['user_id'])) { unset($_SESSION['user_id']); }
header('Location: login.php');
