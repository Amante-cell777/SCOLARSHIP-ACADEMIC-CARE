<?php
session_start();
if($_SESSION['role']!=='admin'){ exit; }
require 'db_connect.php';
$id = (int)$_GET['id'];
$status = $_GET['s'] === 'approved' ? 'approved' : 'rejected';
$pdo->prepare("UPDATE applications SET status=? WHERE id=?")->execute([$status,$id]);
header('Location: check_new_applications.php');
exit;
?>