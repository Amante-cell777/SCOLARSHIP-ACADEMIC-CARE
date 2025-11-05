<?php
session_start();
if($_SESSION['role']!=='admin'){ exit; }
require 'db_connect.php';
$id = (int)$_GET['id'];
$pdo->prepare("DELETE FROM users WHERE id=? AND role='student'")->execute([$id]);
header('Location: admin_dashboard.php');
exit;
?>