<?php
session_start();

// Simple demo credentials — replace with real auth / DB check in production
$DEMO_ADMIN_EMAIL = 'admin@example.com';
$DEMO_ADMIN_PASS  = 'admin123';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
    header('Location: admin-dashboard.php');
    exit;
}

// Handle login attempt (POST)
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if ($email === $DEMO_ADMIN_EMAIL && $pass === $DEMO_ADMIN_PASS) {
        $_SESSION['is_admin'] = 1;
        header('Location: admin-dashboard.php');
        exit;
    } else {
        $loginError = 'Invalid credentials.';
    }
}

$isAdmin = !empty($_SESSION['is_admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo $isAdmin ? 'Admin Dashboard' : 'Admin Login'; ?> — Scholarship Academic Care</title>
  <style>
    :root{--primary-green:#006400;--secondary-green:#228B22}
    *{box-sizing:border-box}
    body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#f4f6f8;color:#222}
    .navbar{background:var(--primary-green);color:#fff;padding:14px 20px;display:flex;justify-content:space-between;align-items:center}
    .logo{color:#fff;text-decoration:none;font-weight:700;font-size:1.15rem}
    .nav-actions{display:flex;gap:10px;align-items:center}
    .btn{display:inline-block;padding:8px 12px;border-radius:6px;text-decoration:none;font-weight:600}
    .btn-ghost{background:transparent;color:#fff;border:1px solid rgba(255,255,255,0.12)}
    .btn-danger{background:#c62828;color:#fff}
    .container{max-width:1100px;margin:28px auto;padding:0 20px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
    .card{background:#fff;border-radius:8px;padding:18px;box-shadow:0 6px 18px rgba(15,15,15,0.06)}
    .card h3{margin:0 0 8px;font-size:1.05rem;color:var(--primary-green)}
    .card p{margin:0;color:#555;font-size:0.95rem}
    .actions{margin-top:12px;display:flex;gap:8px}
    .login-wrap{max-width:520px;margin:48px auto;padding:28px;background:#fff;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,0.06)}
    .form-row{margin-bottom:14px}
    label{display:block;font-weight:600;margin-bottom:6px}
    input[type="email"],input[type="password"]{width:100%;padding:10px;border-radius:6px;border:1px solid #ccc;font-size:1rem}
    .error{color:#b71c1c;margin-bottom:10px}
    .muted{color:#666}
    .nav-link{color:#fff;text-decoration:none;font-weight:600}
    @media (max-width:600px){.navbar{padding:12px}.container{margin:16px auto}}
  </style>
</head>
<body>
  <nav class="navbar" role="navigation" aria-label="main navigation">
    <a class="logo" href="index.html">Scholarship Academic Care</a>
    <div class="nav-actions">
      <ul style="display:flex;gap:12px;list-style:none;margin:0;padding:0;align-items:center">
        <!-- Home link added here -->
        <li><a href="index.html" class="nav-link">Home</a></li>

        <?php if (!$isAdmin): ?>
          <li><a href="login.php" class="nav-link">Login</a></li>
        <?php endif; ?>

        <li><a href="register.php" class="nav-link">Register</a></li>
      </ul>

      <?php if ($isAdmin): ?>
        <a class="btn btn-danger" href="?action=logout">Logout</a>
      <?php endif; ?>
    </div>
  </nav>

  <main class="container" role="main">
<?php if (!$isAdmin): ?>
    <!-- Inline admin login -->
    <section class="login-wrap" aria-labelledby="admin-login-title">
      <h2 id="admin-login-title">Admin Login</h2>
      <p class="muted">Enter your administrator credentials to access the dashboard.</p>

      <?php if ($loginError): ?>
        <div class="error" role="alert"><?php echo htmlspecialchars($loginError); ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="form-row">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required placeholder="admin@example.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />
        </div>
        <div class="form-row">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required />
        </div>
        <div class="form-row" style="display:flex;gap:10px;align-items:center">
          <button type="submit" name="login" class="btn" style="background:var(--primary-green);color:#fff;border-radius:6px;padding:10px 16px">Login</button>
          <a href="index.html" class="muted">Back to site</a>
        </div>
        <p style="margin-top:12px;font-size:0.9rem;color:#666">For demo use <strong>admin@example.com</strong> / <strong>admin123</strong></p>
      </form>
    </section>

<?php else: ?>
    <!-- Admin dashboard content -->
    <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
      <div>
        <h1 style="margin:0;font-size:1.5rem">Admin Dashboard</h1>
        <p style="margin:6px 0 0;color:#666">Welcome, administrator. Use the quick actions below to manage the system.</p>
      </div>
      <div style="text-align:right;color:#666;font-size:0.95rem">
        <div>Server time: <?php echo date('Y-m-d H:i'); ?></div>
      </div>
    </header>

    <section class="grid" aria-label="quick actions">
      <div class="card" role="region" aria-labelledby="manage-users">
        <h3 id="manage-users">Users</h3>
        <p>Manage user accounts, assign roles, and review access.</p>
        <div class="actions">
          <a class="btn" href="admin-users.php">Open Users</a>
          <a class="btn btn-ghost" href="admin-users.php?action=new">Add User</a>
        </div>
      </div>

      <div class="card" role="region" aria-labelledby="manage-scholarships">
        <h3 id="manage-scholarships">Scholarships</h3>
        <p>Create and manage scholarship programs, eligibility, and quotas.</p>
        <div class="actions">
          <a class="btn" href="admin-scholarships.php">Open Scholarships</a>
          <a class="btn btn-ghost" href="admin-scholarships.php?action=new">New Scholarship</a>
        </div>
      </div>

<div class="card" role="region" aria-labelledby="manage-documents">
  <h3 id="manage-documents">Documents</h3>
  <p>Review and approve student-uploaded files.</p>
  <div class="actions">
    <a class="btn" href="admin-documents.php">View All Documents</a>
  </div>
</div>

      <div class="card" role="region" aria-labelledby="manage-apps">
        <h3 id="manage-apps">Applications</h3>
        <p>Review incoming student scholarship applications and documents.</p>
        <div class="actions">
          <a class="btn" href="admin-applications.php">View Applications</a>
        </div>
      </div>

      <div class="card" role="region" aria-labelledby="reports">
        <h3 id="reports">Reports</h3>
        <p>Generate reports: enrollment, scholarship awards, and statistics.</p>
        <div class="actions">
          <a class="btn" href="admin-reports.php">Open Reports</a>
        </div>
      </div>
    </section>

    <section style="margin-top:22px" aria-label="recent activity">
      <div class="card">
        <h3>Recent Applications</h3>
        <p>Latest submissions from applicants (placeholder).</p>
        <table aria-describedby="recent-applications">
          <thead>
            <tr><th>Applicant</th><th>Student_ID</th><th>Program</th><th>Term</th><th>Status</th></tr>
          </thead>
          <tbody>
            <tr><td>Juan Dela Cruz</td><td>BSIT</td><td>First Semester</td><td>Pending</td></tr>
            <tr><td>Maria Santos</td><td>ACT</td><td>Second Semester</td><td>Reviewed</td></tr>
            <tr><td>Pedro Reyes</td><td>BSCE</td><td>First Semester</td><td>Awarded</td></tr>
          </tbody>
        </table>
      </div>
    </section>
<?php endif; ?>

  </main>
</body>
</html>