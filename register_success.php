<?php
session_start();

// === SECURITY: Prevent direct access ===
if (empty($_SESSION['just_registered'])) {
    header('Location: register.php');
    exit;
}

// === DISPLAY USER EMAIL (if available) ===
// We'll capture it now before we clear it
$registered_email = $_SESSION['user_email'] ?? '';

// === CLEAR SENSITIVE SESSION DATA AFTER USE ===
unset($_SESSION['user_email'], $_SESSION['just_registered']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Registration Successful — Scholarship Academic Care</title>
  <style>
    *{box-sizing:border-box}
    body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;color:#333;}
    :root{--primary-green:#006400;--secondary-green:#228B22}
    .navbar{position:sticky;top:0;background:var(--primary-green);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;z-index:1000;color:#fff}
    .logo{color:#fff;text-decoration:none;font-weight:700}
    .nav-right{display:flex;align-items:center;gap:.5rem}
    .nav-links{display:flex;list-style:none;margin:0;padding:0;gap:.9rem;align-items:center}
    .nav-links a{color:#fff;text-decoration:none;font-weight:600;padding:.18rem .35rem}
    .admin-btn{background:#fff;color:var(--primary-green);padding:.45rem 1rem;border-radius:30px;text-decoration:none;font-weight:800;box-shadow:0 4px 10px rgba(0,100,0,0.12)}

    /* Success Page */
    .success-wrapper{padding:60px 20px;min-height:100vh;text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);}
    .success-card{background:#fff;max-width:600px;width:100%;padding:40px 30px;border-radius:12px;box-shadow:0 10px 30px rgba(0,100,0,0.08);border-top:5px solid var(--primary-green);}
    .success-icon{font-size:4rem;color:var(--primary-green);margin-bottom:16px;}
    .success-title{font-size:1.8rem;font-weight:700;color:var(--primary-green);margin:0 0 12px}
    .success-msg{font-size:1.1rem;color:#444;line-height:1.6;margin-bottom:24px}
    .success-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
    .btn-success{background:var(--primary-green);color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;transition:background .3s}
    .btn-success:hover{background:var(--secondary-green)}
    .btn-outline{border:2px solid var(--primary-green);color:var(--primary-green);background:transparent;padding:10px 22px;border-radius:6px;text-decoration:none;font-weight:600;transition:all .3s}
    .btn-outline:hover{background:var(--primary-green);color:#fff}

    @media(max-width:576px){
      .success-card{padding:30px 20px}
      .success-title{font-size:1.5rem}
      .success-actions{flex-direction:column;align-items:center}
    }
  </style>
</head>
<body>

  <!-- Same Navbar as Register -->
  <nav class="navbar">
    <a class="logo" href="index.html">Scholarship Academic Care</a>
    <div class="nav-right">
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
      <a href="admin-dashboard.php" class="admin-btn">Admin</a>
    </div>
  </nav>

  <section class="success-wrapper">
    <div class="success-card">
      <div class="success-icon">Checkmark</div>
      <h1 class="success-title">Registration Successful!</h1>
      <p class="success-msg">
        Thank you for registering with <strong>Scholarship Academic Care</strong>.<br>
        Your account has been created and is now ready to use.
      </p>

      <!-- *** DYNAMIC EMAIL DISPLAY (Safe & Clean) *** -->
      <?php if ($registered_email): ?>
        <p class="success-msg" style="font-size:0.95rem;color:#666;">
          We sent a confirmation link to <strong><?= htmlspecialchars($registered_email) ?></strong>
        </p>
      <?php endif; ?>

      <p class="success-msg" style="font-size:0.95rem;color:#666;">
        A confirmation email has been sent to your inbox.<br>
        Please check your email to activate your account.
      </p>

      <div class="success-actions">
        <a href="login.php" class="btn-success">Login Now</a>
        <a href="index.html" class="btn-outline">Back to Home</a>
      </div>

      <p style="margin-top:30px;font-size:0.85rem;color:#888;">
        Need help? <a href="#" style="color:var(--primary-green);text-decoration:underline">Contact Support</a>
      </p>
    </div>
  </section>

</body>
</html>