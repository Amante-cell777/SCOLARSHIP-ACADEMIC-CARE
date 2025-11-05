<?php
header('Content-Type: application/json');
require 'db_connect.php';
$id = (int)$_GET['id'];
$status = $pdo->prepare("SELECT status FROM applications WHERE id=?")->execute([$id]) ? $pdo->fetchColumn() : '';
echo json_encode(['status'=>$status]);
?>