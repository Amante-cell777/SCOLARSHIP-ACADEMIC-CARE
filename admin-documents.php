<?php
session_start();
if (empty($_SESSION['is_admin'])) {
    header('Location: admin-dashboard.php');
    exit;
}
require 'db_connect.php';

// Handle status update
if ($_POST['action'] ?? '' === 'update_status') {
    $id = (int)$_POST['doc_id'];
    $status = $_POST['status']; // approved / rejected
    $stmt = $pdo->prepare("UPDATE student_documents SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    $_SESSION['msg'] = "Status updated to <strong>$status</strong>.";
    header('Location: admin-documents.php');
    exit;
}

// Fetch all documents with student info
$stmt = $pdo->query("
    SELECT d.*, u.full_name, u.email 
    FROM student_documents d
    JOIN users u ON d.student_id = u.id
    ORDER BY d.uploaded_at DESC
");
$docs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin - Documents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>:root{--primary:#006400} .badge-approved{background:#d4edda;color:#155724} .badge-rejected{background:#f8d7da;color:#721c24} .badge-pending{background:#fff3cd;color:#856404}</style>
</head>
<body class="bg-light">
  <?php include 'admin-nav.php'; ?>

  <div class="container mt-4">
    <h2>📄 Student Document Submissions</h2>
    <?php if (isset($_SESSION['msg'])): ?>
      <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Student</th>
              <th>File</th>
              <th>Type</th>
              <th>Uploaded</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($docs as $d): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($d['full_name']) ?></strong><br>
                <small class="text-muted"><?= htmlspecialchars($d['email']) ?></small>
              </td>
              <td>
                <a href="serve_file.php?f=<?= urlencode($d['file_path']) ?>" target="_blank">
                  📎 <?= htmlspecialchars($d['original_name']) ?>
                </a>
              </td>
              <td><?= ucfirst(str_replace('_', ' ', $d['upload_type'])) ?></td>
              <td><?= date('M j, Y g:i A', strtotime($d['uploaded_at'])) ?></td>
              <td>
                <span class="badge badge-<?= $d['status'] ?>">
                  <?= ucfirst($d['status']) ?>
                </span>
              </td>
              <td>
                <?php if ($d['status'] !== 'approved'): ?>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                    <input type="hidden" name="action" value="update_status">
                    <button type="submit" name="status" value="approved" class="btn btn-success btn-sm">✓ Approve</button>
                  </form>
                <?php endif; ?>
                <?php if ($d['status'] !== 'rejected'): ?>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
                    <input type="hidden" name="action" value="update_status">
                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">✗ Reject</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>