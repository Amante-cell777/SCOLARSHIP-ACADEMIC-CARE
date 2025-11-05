<?php
require 'header.php';
if($_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

require 'db_connect.php';
$apps = $pdo->query("
    SELECT a.id, u.full_name, s.title, a.gpa, a.status, a.submitted_at
    FROM applications a
    JOIN users u ON a.student_id=u.id
    JOIN scholarships s ON a.scholarship_id=s.id
    WHERE a.status='pending'
    ORDER BY a.submitted_at DESC
")->fetchAll();
?>
<h2>Pending Applications</h2>
<table class="table table-bordered">
    <thead><tr><th>Student</th><th>Scholarship</th><th>GPA</th><th>Submitted</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($apps as $a): ?>
        <tr>
            <td><?=htmlspecialchars($a['full_name'])?></td>
            <td><?=htmlspecialchars($a['title'])?></td>
            <td><?=$a['gpa']?></td>
            <td><?=date('M j, Y',strtotime($a['submitted_at']))?></td>
            <td>
                <a href="update_status.php?id=<?=$a['id']?>&s=approved" class="btn btn-sm btn-success">Approve</a>
                <a href="update_status.php?id=<?=$a['id']?>&s=rejected" class="btn btn-sm btn-danger">Reject</a>
                <a href="update_application.php?id=<?=$a['id']?>" class="btn btn-sm btn-primary">Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require 'footer.php'; ?>