<?php
require 'header.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='student'){ header('Location: login.php'); exit; }

require 'db_connect.php';
$scholarships = $pdo->query("SELECT * FROM scholarships ORDER BY deadline ASC")->fetchAll();
?>
<h2>Available Scholarships</h2>
<div class="row">
<?php foreach($scholarships as $s): ?>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?=htmlspecialchars($s['title'])?></h5>
                <p class="card-text"><?=nl2br(htmlspecialchars($s['description']))?></p>
                <p><strong>Amount:</strong> $<?=number_format($s['amount'],2)?><br>
                   <strong>GPA range:</strong> <?=$s['gpa_min']?> – <?=$s['gpa_max']?><br>
                   <strong>Deadline:</strong> <?=$s['deadline']?></p>
                <a href="submit_application.php?id=<?=$s['id']?>" class="btn btn-success">Apply</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>