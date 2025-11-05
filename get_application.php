<?php
header('Content-Type: application/json');
require 'db_connect.php';
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, s.title FROM applications a JOIN scholarships s ON a.scholarship_id=s.id WHERE a.id=?");
$stmt->execute([$id]);
echo json_encode($stmt->fetch() ?: []);
?>