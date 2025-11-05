<?php
session_start();

// ---------- VALIDATE THAT STEP 1 WAS COMPLETED ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store Step 1 data (sanitize)
    $_SESSION['reg_step1'] = array_map('trim', $_POST);
} else {
    // If someone opens this page directly → send back
    if (empty($_SESSION['reg_step1'])) {
        header('Location: register.php');
        exit;
    }
}

// Pre-fill if user came back from step 3
$step2 = $_SESSION['reg_step2'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Register – Additional Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--primary-green:#006400}
    body{background:#f8f9fa;font-family:'Segoe UI',Arial,sans-serif}
    .navbar{background:var(--primary-green);color:#fff}
    .reg-wrapper{padding:50px 20px;min-height:100vh}
    .reg-progress .reg-step{background:#999;color:#fff;padding:8px 14px;border-radius:20px;font-size:.85rem}
    .reg-progress .reg-step.reg-active{background:#2196f3}
    .reg-card{background:#fff;max-width:720px;margin:auto;padding:30px 40px;border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,.1)}
    .reg-next{float:right;background:#2196f3;color:#fff;border:none;padding:12px 24px;border-radius:6px;font-size:1.1rem}
    .reg-next:disabled{background:#888;cursor:not-allowed}
  </style>
</head>
<body>
  <nav class="navbar navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="index.html">Scholarship Academic Care</a>
    </div>
  </nav>

  <section class="reg-wrapper">
    <nav class="reg-progress d-flex justify-content-center gap-2 mb-4" aria-label="steps">
      <div class="reg-step">1 Applicant Details</div>
      <div class="reg-step reg-active">2 Additional Information</div>
      <div class="reg-step">3 Confirm</div>
      <div class="reg-step">4 Terms</div>
      <div class="reg-step">5 Success</div>
    </nav>

    <form class="reg-card" method="post" action="register_step3.php" novalidate>
      <h2>Additional Information</h2>
      <p>Please fill in the fields below. <span style="color:red">*</span> = required.</p>

      <!-- ==== STEP 1 FIELDS (PRE-FILLED FROM SESSION) ==== -->
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">First Name <span style="color:red">*</span></label>
          <input name="firstName" type="text" class="form-control" required
                 value="<?= htmlspecialchars($_SESSION['reg_step1']['firstName'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Last Name <span style="color:red">*</span></label>
          <input name="lastName" type="text" class="form-control" required
                 value="<?= htmlspecialchars($_SESSION['reg_step1']['lastName'] ?? '') ?>">
        </div>
      </div>

      <div class="row g-3 mt-2">
        <div class="col-md-6">
          <label class="form-label">Email <span style="color:red">*</span></label>
          <input name="email" type="email" class="form-control" required
                 value="<?= htmlspecialchars($_SESSION['reg_step1']['email'] ?? '') ?>">
        </div>

        <!-- PASSWORD FIELD ADDED HERE -->
        <div class="col-md-6">
          <label class="form-label">Password <span style="color:red">*</span></label>
          <input type="password" name="password" class="form-control" required placeholder="Choose a strong password">
        </div>
      </div>

      <!-- ==== STEP 2 FIELDS ==== -->
      <div class="row g-3 mt-2">
        <div class="col-md-6">
          <label class="form-label">Guardian Name <span style="color:red">*</span></label>
          <input name="guardian_name" type="text" class="form-control" required
                 value="<?= htmlspecialchars($step2['guardian_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Guardian Contact <span style="color:red">*</span></label>
          <input name="guardian_contact" type="tel" class="form-control" pattern="[0-9]{10,15}" required
                 value="<?= htmlspecialchars($step2['guardian_contact'] ?? '') ?>">
          <small class="text-muted">10–15 digits</small>
        </div>
      </div>

      <div class="row g-3 mt-2">
        <div class="col-md-6">
          <label class="form-label">Current GPA (if any)</label>
          <input name="gpa" type="number" step="0.01" min="0" max="4" class="form-control"
                 value="<?= htmlspecialchars($step2['gpa'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Year Level <span style="color:red">*</span></label>
          <select name="year_level" class="form-select" required>
            <option value="">-- Select --</option>
            <option <?= ($step2['year_level'] ?? '') === '1' ? 'selected' : '' ?>>1st Year</option>
            <option <?= ($step2['year_level'] ?? '') === '2' ? 'selected' : '' ?>>2nd Year</option>
            <option <?= ($step2['year_level'] ?? '') === '3' ? 'selected' : '' ?>>3rd Year</option>
            <option <?= ($step2['year_level'] ?? '') === '4' ? 'selected' : '' ?>>4th Year</option>
          </select>
        </div>
      </div>

      <!-- ==== ACTION BUTTONS ==== -->
      <div class="mt-4 d-flex justify-content-between">
        <a href="register.php" class="btn btn-outline-secondary">Back</a>
        <button type="submit" id="nextBtn" class="reg-next">Next</button>
      </div>
    </form>
  </section>

  <!-- ==== CLIENT-SIDE VALIDATION ==== -->
  <script>
    const f = document.querySelector('form');
    const b = document.getElementById('nextBtn');
    function chk() { b.disabled = !f.checkValidity(); }
    f.addEventListener('input', chk);
    chk();
  </script>
</body>
</html>