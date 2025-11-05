<?php
header('Content-Type: application/json');
require 'db_connect.php';
$list = $pdo->query("SELECT id,title,amount,deadline FROM scholarships ORDER BY deadline ASC")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($list);
?>