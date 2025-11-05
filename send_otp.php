<?php
session_start();
require 'db_connect.php';
$email = $_POST['email'] ?? '';
if(!$email) exit(json_encode(['ok'=>false]));

$code = sprintf("%06d",rand(0,999999));
$exp  = date('Y-m-d H:i:s',time()+600);

$stmt = $pdo->prepare("DELETE FROM otps WHERE email=?");
$stmt->execute([$email]);

$stmt = $pdo->prepare("INSERT INTO otps (email,code,expires_at) VALUES(?,?,?)");
$stmt->execute([$email,$code,$exp]);

// ----> Replace with your SMTP / mail function <----
mail($email, "Your OTP", "Your verification code is $code. Valid for 10 minutes.");
// ------------------------------------------------

echo json_encode(['ok'=>true]);
?>