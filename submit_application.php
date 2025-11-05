<?php
require 'header.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='student'){ header('Location: login.php'); exit; }

require 'db_connect.php';
$sch_id = (int)$_GET['id'];
$sch = $pdo->prepare("SELECT * FROM scholarships WHERE id=?")->execute([$sch_id]) ? $pdo->fetch() : null;
if(!$sch){ die('Invalid scholarship.'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $gpa = (float)$_POST['gpa'];
    if($gpa < $sch['gpa_min'] || $gpa > $sch['gpa_max']){
        $err = "GPA out of range for this scholarship.";
    }else{
        $files = [];
        $uploadDir = 'uploads/';
        if(!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
        foreach($_FILES['docs']['name'] as $k=>$name){
            if($_FILES['docs']['error'][$k]==0){
                $ext = pathinfo($name,PATHINFO_EXTENSION);
                $new = uniqid().'.'.$ext;
                move_uploaded_file($_FILES['docs']['tmp_name'][$k], $uploadDir.$new);
                $files[] = $new;
            }
        }
        $stmt = $pdo->prepare("INSERT INTO applications (student_id,scholarship_id,gpa,documents) VALUES(?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$sch_id,$gpa,json_encode($files)]);
        $msg = "Application submitted!";
    }
}
?>
<h2>Apply for: <?=htmlspecialchars($sch['title'])?></h2>
<?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
<?php if(isset($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>GPA (1.00 – 155)</label>
        <input type="number" step="0.01" name="gpa" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Upload Documents (PDF, images…)</label>
        <input type="file" name="docs[]" multiple class="form-control">
    </div>
    <button class="btn btn-primary">Submit Application</button>
</form>
<?php require 'footer.php'; ?>