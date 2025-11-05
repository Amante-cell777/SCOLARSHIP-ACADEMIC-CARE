<?php
require 'header.php';
if($_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

require 'db_connect.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name']);
    $pdo->prepare("UPDATE users SET full_name=? WHERE id=?")->execute([$name,$_SESSION['user_id']]);
    $_SESSION['name'] = $name;
    $msg = "Name updated.";
}
?>
<h2>Update Admin Name</h2>
<?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post">
    <div class="mb-3"><input name="name" class="form-control" value="<?=htmlspecialchars($_SESSION['name'])?>" required></div>
    <button class="btn btn-primary">Save</button>
</form>
<?php require 'footer.php'; ?>