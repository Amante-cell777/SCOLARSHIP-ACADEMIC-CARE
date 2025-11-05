<?php
require 'header.php';
if($_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

require 'db_connect.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $stmt = $pdo->prepare("INSERT INTO scholarships (title,description,amount,gpa_min,gpa_max,deadline) VALUES(?,?,?,?,?,?)");
    $stmt->execute([$_POST['title'],$_POST['desc'],$_POST['amount'],$_POST['gpa_min'],$_POST['gpa_max'],$_POST['deadline']]);
    $msg = "Scholarship added.";
}
?>
<h2>Add Scholarship</h2>
<?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post">
    <div class="mb-3"><input name="title" class="form-control" placeholder="Title" required></div>
    <div class="mb-3"><textarea name="desc" class="form-control" placeholder="Description" rows="3"></textarea></div>
    <div class="mb-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount"></div>
    <div class="mb-3"><input type="number" step="0.01" name="gpa_min" class="form-control" value="1.00" required></div>
    <div class="mb-3"><input type="number" step="0.01" name="gpa_max" class="form-control" value="155.00" required></div>
    <div class="mb-3"><input type="date" name="deadline" class="form-control" required></div>
    <button class="btn btn-primary">Save</button>
</form>
<?php require 'footer.php'; ?>