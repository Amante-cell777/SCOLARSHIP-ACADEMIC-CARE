<?php
session_start();
require 'db_connect.php';
$email = $_POST['email'];
$code  = $_POST['code'];

$stmt = $pdo->prepare("SELECT * FROM otps WHERE email=? AND code=? AND used=0 AND expires_at > NOW()");
$stmt->execute([$email,$code]);
$otp = $stmt->fetch();

if($otp){
    $pdo->prepare("UPDATE otps SET used=1 WHERE id=?")->execute([$otp['id']]);
    echo json_encode(['ok'=>true]);
}else{
    echo json_encode(['ok'=>false,'msg'=>'Invalid or expired code.']);
}
?>