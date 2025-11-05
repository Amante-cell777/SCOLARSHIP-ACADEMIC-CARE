<?php
require 'header.php';
if($_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

require 'db_connect.php';
$id = (int)$_GET['id'];
$app = $pdo->prepare("SELECT * FROM applications WHERE id=?")->execute([$id]) ? $pdo->fetch() : null;
if(!$app) die('Not found');

if($_SERVER['REQUEST_METHOD']==='POST'){
    $gpa = (float)$_POST['gpa'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE applications SET gpa=?, status=? WHERE id=?")->execute([$gpa,$status,$id]);
    $msg = "Updated.";
}
?>
<h2>Edit Application #<?=$id?></h2>
<?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post">
    <div class="mb-3"><input type="number" step="0.01" name="gpa" class="form-control" value="<?=$app['gpa']?>" required></div>
    <div class="mb-3">
        <select name="status" class="form-select">
            <option value="pending"   <?= $app['status']==='pending'?'selected':'' ?>>Pending</option>
            <option value="approved"  <?= $app['status']==='approved'?'selected':'' ?>>Approved</option>
            <option value="rejected"  <?= $app['status']==='rejected'?'selected':'' ?>>Rejected</option>
        </select>
    </div>
    <button class="btn btn-primary">Save</button>
</form>
<?php require 'footer.php'; ?>