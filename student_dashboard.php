<?php
require 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

require 'db_connect.php';
$user_id = $_SESSION['user_id'];

// === HANDLE DOCUMENT UPLOAD ===
$upload_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documents'])) {
    $upload_dir = "uploads/documents/$user_id/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB

    foreach ($_FILES['documents']['name'] as $key => $name) {
        if ($_FILES['documents']['error'][$key] !== 0) continue;

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $upload_msg .= "Invalid file type: $name<br>";
            continue;
        }
        if ($_FILES['documents']['size'][$key] > $max_size) {
            $upload_msg .= "File too large: $name (max 5MB)<br>";
            continue;
        }

        $filename = uniqid('doc_') . '.' . $ext;
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['documents']['tmp_name'][$key], $filepath)) {
            $type = $_POST['doc_type'][$key] ?? 'other';
            $stmt = $pdo->prepare("INSERT INTO student_documents (student_id, file_path, original_name, upload_type) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $filepath, $name, $type]);
            $upload_msg .= "Uploaded: $name<br>";
        }
    }
    if ($upload_msg) $upload_msg = '<div class="alert alert-info">' . $upload_msg . '</div>';
}

// === FETCH APPLICATIONS ===
$stmt = $pdo->prepare("
    SELECT a.*, s.title
    FROM applications a
    JOIN scholarships s ON a.scholarship_id = s.id
    WHERE a.student_id = ?
    ORDER BY a.submitted_at DESC
");
$stmt->execute([$user_id]);
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === FETCH UPLOADED DOCUMENTS ===
$doc_stmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY uploaded_at DESC");
$doc_stmt->execute([$user_id]);
$documents = $doc_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mt-4">Student Dashboard</h2>
<a href="index.html" class="btn btn-outline-primary mb-3">Home</a>

<!-- DOCUMENT UPLOAD SECTION -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Upload Documents for Scholarship & Academic Verification</h5>
    </div>
    <div class="card-body">
        <?= $upload_msg ?? '' ?>
        <form method="POST" enctype="multipart/form-data">
            <div id="file-container">
                <div class="row align-items-end mb-2 file-row">
                    <div class="col-md-5">
                        <input type="file" name="documents[]" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-4">
                        <select name="doc_type[]" class="form-select" required>
                            <option value="transcript">Transcript</option>
                            <option value="recommendation">Recommendation Letter</option>
                            <option value="id_proof">ID Proof</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger btn-sm remove-file" style="display:none;">Remove</button>
                    </div>
                </div>
            </div>
            <button type="button" id="add-more" class="btn btn-outline-secondary btn-sm mb-3">+ Add More Files</button><br>
            <button type="submit" class="btn btn-success">Upload Documents</button>
        </form>
    </div>
</div>

<!-- UPLOADED DOCUMENTS LIST -->
<?php if (!empty($documents)): ?>
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Your Uploaded Documents</h5>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>File</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td>
                        <a href="serve_file.php?f=<?= urlencode($doc['file_path']) ?>" target="_blank">
                            <?= htmlspecialchars($doc['original_name']) ?>
                        </a>
                    </td>
                    <td><?= ucfirst(str_replace('_', ' ', $doc['upload_type'])) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $doc['status']==='approved' ? 'success' :
                            ($doc['status']==='rejected' ? 'danger' : 'warning')
                        ?>">
                            <?= ucfirst($doc['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y g:i A', strtotime($doc['uploaded_at'])) ?></td>
                    <td>
                        <?php if ($doc['status'] !== 'approved'): ?>
                            <button class="btn btn-sm btn-warning reupload-btn" 
                                    data-id="<?= $doc['id'] ?>" 
                                    data-name="<?= htmlspecialchars($doc['original_name']) ?>">
                                Re-upload
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- MY APPLICATIONS -->
<h2>My Applications</h2>
<?php if (empty($apps)): ?>
    <p class="text-muted">You haven't applied for any scholarships yet.</p>
    <a href="student_application.php" class="btn btn-success">Browse Scholarships</a>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Scholarship</th>
                <th>GPA</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Documents</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($apps as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= number_format($a['gpa'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?=
                            $a['status']==='approved' ? 'success' :
                            ($a['status']==='rejected' ? 'danger' : 'warning')
                        ?>">
                            <?= ucfirst($a['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($a['submitted_at'])) ?></td>
                    <td>
                        <?php
                        $docs = json_decode($a['documents'], true);
                        if ($docs && is_array($docs)):
                            foreach ($docs as $d):
                        ?>
                                <a href="serve_file.php?f=<?= urlencode($d) ?>" target="_blank">
                                    <?= htmlspecialchars(basename($d)) ?>
                                </a><br>
                            <?php endforeach;
                        else: ?>
                            <em>No files</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="student_application.php" class="btn btn-success">Apply for More</a>
<?php endif; ?>

<!-- RE-UPLOAD MODAL -->
<div class="modal fade" id="reuploadModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" id="reuploadForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Re-upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="doc_id" id="reuploadDocId">
                    <p>Replace: <strong id="reuploadFileName"></strong></p>
                    <input type="file" name="new_document" class="form-control" required 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload New Version</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>

<!-- JavaScript for Dynamic File Inputs & Re-upload -->
<script>
document.getElementById('add-more').addEventListener('click', function() {
    const container = document.getElementById('file-container');
    const row = document.querySelector('.file-row').cloneNode(true);
    row.querySelector('input[type="file"]').value = '';
    row.querySelector('select').selectedIndex = 0;
    row.querySelector('.remove-file').style.display = 'inline-block';
    container.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-file')) {
        if (document.querySelectorAll('.file-row').length > 1) {
            e.target.closest('.file-row').remove();
        }
    }

    if (e.target.classList.contains('reupload-btn')) {
        const modal = new bootstrap.Modal(document.getElementById('reuploadModal'));
        document.getElementById('reuploadDocId').value = e.target.dataset.id;
        document.getElementById('reuploadFileName').textContent = e.target.dataset.name;
        modal.show();
    }
});
</script>

<?php
// === HANDLE RE-UPLOAD ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doc_id'])) {
    $doc_id = (int)$_POST['doc_id'];
    $check = $pdo->prepare("SELECT * FROM student_documents WHERE id = ? AND student_id = ?");
    $check->execute([$doc_id, $user_id]);
    $doc = $check->fetch();

    if ($doc && !empty($_FILES['new_document']['name'])) {
        $upload_dir = dirname($doc['file_path']) . '/';
        $ext = strtolower(pathinfo($_FILES['new_document']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('doc_') . '.' . $ext;
        $new_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['new_document']['tmp_name'], $new_path)) {
            // Delete old file
            @unlink($doc['file_path']);
            // Update DB
            $update = $pdo->prepare("UPDATE student_documents SET file_path = ?, original_name = ?, status = 'pending', uploaded_at = NOW() WHERE id = ?");
            $update->execute([$new_path, $_FILES['new_document']['name'], $doc_id]);
            echo "<script>alert('Document re-uploaded successfully.'); location.reload();</script>";
        }
    }
}
?>