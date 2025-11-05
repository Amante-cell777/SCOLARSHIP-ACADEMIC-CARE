<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connect.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['full_name'];

            $redirect = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'student_dashboard.php';
            header("Location: $redirect");
            exit;
        } else {
            $err = "Invalid email or password.";
        }
    } else {
        $err = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Scholarship Academic Care</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f8f9fa;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0;
    }

    /* Green Header Bar */
    header {
      background: #006400;
      color: white;
      width: 100%;
      padding: 15px 20px;
      text-align: center;
      font-weight: bold;
      font-size: 1.1rem;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    header .container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-size: 0.95rem;
    }

    /* Center the form */
    .page-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      padding: 40px 20px;
    }

    /* Uiverse Form */
    .form_container {
      width: 100%;
      max-width: 380px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      padding: 50px 40px 20px;
      background-color: #ffffff;
      box-shadow: 0px 106px 42px rgba(0,0,0,0.01),
                  0px 59px 36px rgba(0,0,0,0.05),
                  0px 26px 26px rgba(0,0,0,0.09),
                  0px 7px 15px rgba(0,0,0,0.1);
      border-radius: 11px;
      font-family: "Inter", sans-serif;
    }

    .logo_container {
      width: 80px;
      height: 80px;
      background: linear-gradient(180deg, rgba(248,248,248,0) 50%, #F8F8F888 100%);
      border: 1px solid #F7F7F8;
      filter: drop-shadow(0px 0.5px 0.5px #EFEFEF) drop-shadow(0px 1px 0.5px rgba(239,239,239,0.5));
      border-radius: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .title_container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      text-align: center;
    }
    .title { font-size: 1.25rem; font-weight: 700; color: #212121; margin: 0; }
    .subtitle { font-size: 0.725rem; color: #8B8E98; line-height: 1.1rem; }

    .input_container {
      width: 100%;
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    .input_label { font-size: 0.75rem; color: #8B8E98; font-weight: 600; }
    .icon { width: 20px; position: absolute; left: 12px; bottom: 10px; z-index: 99; pointer-events: none; }
    .input_field {
      width: 100%;
      height: 40px;
      padding: 0 0 0 40px;
      border-radius: 7px;
      border: 1px solid #e5e5e5;
      outline: none;
      filter: drop-shadow(0px 1px 0px #efefef) drop-shadow(0px 1px 0.5px rgba(239,239,239,0.5));
      transition: all 0.3s cubic-bezier(0.15,0.83,0.66,1);
      font-size: 0.95rem;
    }
    .input_field:focus {
      border: 1px solid transparent;
      box-shadow: 0px 0px 0px 2px #006400;
    }

    .sign-in_btn {
      width: 100%;
      height: 40px;
      background: #006400;
      color: white;
      border: 0;
      border-radius: 7px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }
    .sign-in_btn:hover { background: #004d00; }

    .separator {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 30px;
      color: #8B8E98;
      font-size: 0.8rem;
    }
    .separator .line { flex: 1; height: 1px; background: #e8e8e8; }

    .sign-in_ggl, .sign-in_apl {
      width: 100%;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      border-radius: 7px;
      border: 1px solid #e5e5e5;
      filter: drop-shadow(0px 1px 0px #efefef) drop-shadow(0px 1px 0.5px rgba(239,239,239,0.5));
      cursor: pointer;
      font-weight: 600;
    }
    .sign-in_ggl { background: #fff; color: #242424; }
    .sign-in_ggl:hover { background: #f7f7f7; }
    .sign-in_apl { background: #212121; color: #fff; }
    .sign-in_apl:hover { background: #000; }

    .note {
      font-size: 0.75rem;
      color: #8B8E98;
      text-decoration: underline;
      text-align: center;
      margin-top: 10px;
      cursor: pointer;
    }

    .error {
      background: #ffebee;
      color: #c62828;
      padding: 12px;
      border-radius: 7px;
      font-size: 0.9rem;
      text-align: center;
      margin-bottom: 15px;
      border: 1px solid #ffcdd2;
      width: 100%;
    }

    @media (max-width: 480px) {
      .form_container { padding: 40px 25px 20px; }
    }
  </style>
</head>
<body>

  <!-- Green Header Bar -->
  <header>
    <div class="container">
      <span>SCHOLARSHIP ACADEMIC CARE</span>
      <nav>
  <a href="index.html">Home</a>
  <a href="login.php">Login</a>
  <a href="register.php">Register</a>
</nav>
    </div>
  </header>

  <!-- Centered Page Container -->
  <div class="page-container">

    <!-- Uiverse Login Form -->
    <div class="form_container">

      <!-- Logo -->
      <div class="logo_container">
        <svg width="60" height="60" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="80" height="80" rx="11" fill="url(#paint0_linear)"/>
          <path d="M40 20L55 35L40 50L25 35L40 20Z" fill="#006400"/>
          <defs>
            <linearGradient id="paint0_linear" x1="0" y1="0" x2="80" y2="80">
              <stop stop-color="#F8F8F8"/>
              <stop offset="1" stop-color="#E0E0E0"/>
            </linearGradient>
          </defs>
        </svg>
      </div>

      <!-- Title -->
      <div class="title_container">
        <p class="title">Welcome Back</p>
        <span class="subtitle">Log in to continue to your account.</span>
      </div>

      <!-- Error Message -->
      <?php if ($err): ?>
        <div class="error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST">
        <!-- Email -->
        <div class="input_container">
          <label class="input_label" for="email_field">Email</label>
          <svg class="icon" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke="#8B8E98" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z"/>
            <path stroke="#8B8E98" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M3 7L12 13L21 7"/>
          </svg>
          <input type="email" name="email" id="email_field" class="input_field"
                 placeholder="you@example.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
        </div>

        <!-- Password -->
        <div class="input_container">
          <label class="input_label" for="password_field">Password</label>
          <svg class="icon" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke="#8B8E98" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  d="M12 14.5V16.5M7.5 10.5V8.5C7.5 6.01472 9.51472 4 12 4C14.4853 4 16.5 6.01472 16.5 8.5V10.5M6.5 20H17.5C18.8807 20 20 18.8807 20 17.5V12.5C20 11.1193 18.8807 10 17.5 10H6.5C5.11929 10 4 11.1193 4 12.5V17.5C4 18.8807 5.11929 20 6.5 20Z"/>
          </svg>
          <input type="password" name="password" id="password_field" class="input_field"
                 placeholder="••••••••" required>
        </div>

        <!-- Submit -->
        <button type="submit" class="sign-in_btn">Sign In</button>
      </form>

      <!-- Separator -->
      <div class="separator">
        <hr class="line"><span>or</span><hr class="line">
      </div>

      <!-- Google -->
      <button class="sign-in_ggl">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.501 12.2332C22.501 11.3676 22.4295 10.5339 22.2976 9.7334H12.001V14.4332H17.501C17.2383 15.8666 16.3644 17.0332 15.1226 17.7889V20.2882H18.501C20.501 18.4514 22.501 15.5879 22.501 12.2332Z" fill="#4285F4"/>
          <path d="M12.001 23.0001C15.251 23.0001 17.9883 21.5113 18.501 20.2882L15.1226 17.7889C14.0718 18.4882 12.726 18.9001 12.001 18.9001C8.86805 18.9001 6.24835 16.7663 5.31372 13.9666H1.81104V16.5666C2.311 18.5666 4.37372 22.0001 12.001 22.0001Z" fill="#34A853"/>
          <path d="M5.31372 13.9666C5.061 13.2666 4.926 12.5001 4.926 11.7001C4.926 10.9001 5.061 10.1336 5.31372 9.43359V6.83359H1.81104C1.01104 8.43359 0.501 10.3001 0.501 12.2001C0.501 14.1001 1.01104 15.9666 1.81104 17.5666L5.31372 13.9666Z" fill="#FBBC05"/>
          <path d="M12.001 4.50006C13.601 4.50006 15.0637 5.16673 16.1264 6.30006L18.501 3.80006C17.2383 2.60006 15.251 1.50006 12.001 1.50006C4.37372 1.50006 2.311 4.93339 1.81104 6.83339L5.31372 9.43359C6.24835 6.63387 8.86805 4.50006 12.001 4.50006Z" fill="#EA4335"/>
        </svg>
        <span>Continue with Google</span>
      </button>

      <!-- Forgot Password -->
      <p class="note">Forgot password?</p>

    </div>
  </div>

  <?php require 'footer.php'; ?>
</body>
</html>