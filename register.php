<?php
session_start();

// If the user came back from step 2 we keep the data
if (!isset($_SESSION['reg_step1'])) {
    $_SESSION['reg_step1'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Register — Scholarship Academic Care</title>
  <style>
    /* ----- SAME STYLES YOU ALREADY HAVE ----- */
    *{box-sizing:border-box}
    body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;color:#333;}
    :root{--primary-green:#006400;--secondary-green:#228B22}
    .navbar{position:sticky;top:0;background:var(--primary-green);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;z-index:1000;color:#fff}
    .logo{color:#fff;text-decoration:none;font-weight:700}
    .nav-right{display:flex;align-items:center;gap:.5rem}
    .nav-links{display:flex;list-style:none;margin:0;padding:0;gap:.9rem;align-items:center}
    .nav-links a{color:#fff;text-decoration:none;font-weight:600;padding:.18rem .35rem}
    .admin-btn{background:#fff;color:var(--primary-green);padding:.45rem 1rem;border-radius:30px;text-decoration:none;font-weight:800;box-shadow:0 4px 10px rgba(0,100,0,0.12)}
    .reg-wrapper{padding:50px 20px;min-height:100vh;background:#f0f0f0}
    .reg-progress{display:flex;justify-content:center;gap:10px;margin-bottom:30px;user-select:none}
    .reg-step{padding:8px 14px;border-radius:20px;background:#999;color:#fff;font-size:.85rem}
    .reg-step.reg-active{background:#2196f3}
    .reg-card{background:#fff;max-width:700px;margin:0 auto;padding:30px 40px;border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,.1)}
    .reg-row{display:flex;gap:20px;margin-top:15px;flex-wrap:wrap}
    .reg-label{flex:1 1 30%;display:flex;flex-direction:column;font-size:.9rem}
    .reg-label-full{flex:1 1 100%;display:flex;flex-direction:column;font-size:.9rem}
    .reg-input{margin-top:6px;padding:10px;font-size:1rem;border-radius:4px;border:1px solid #ccc;transition:border-color .3s}
    .reg-input:focus{border-color:#2196f3;outline:none}
    .reg-next{margin-top:30px;background:#2196f3;color:#fff;padding:12px 24px;border-radius:6px;border:none;cursor:pointer;font-size:1.1rem;float:right}
    .reg-next:disabled{background:#888;cursor:not-allowed}
    @media(max-width:768px){.reg-row{flex-direction:column}.reg-label,.reg-label-full{flex:1 1 100%}.reg-next{width:100%;float:none}}
  </style>
</head>
<body>
  <nav class="navbar">
    <a class="logo" href="Index.html">Scholarship Academic Care</a>
    <div class="nav-right">
      <ul class="nav-links">
        <li><a href="Index.html">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
      <a href="admin-dashboard.php" class="admin-btn" aria-label="Admin dashboard">Admin</a>
    </div>
  </nav>

  <section class="reg-wrapper" aria-label="User registration form">
    <nav class="reg-progress" aria-label="Registration steps">
      <div class="reg-step reg-active">1 Applicant Details</div>
      <div class="reg-step">2 Additional Information</div>
      <div class="reg-step">3 Confirm</div>
      <div class="reg-step">4 Terms</div>
      <div class="reg-step">5 Success</div>
    </nav>

    <form id="registration-form" class="reg-card" method="post" action="register_step2.php" novalidate>
      <h2>New Account Registration</h2>
      <p>Student's information</p>
      <p style="font-size:.9rem;color:#555">Please enter required field.</p>

      <!-- ==== ALL INPUTS FROM YOUR ORIGINAL FORM ==== -->
      <div class="reg-row">
        <label class="reg-label" for="firstName">First name <span style="color:red">*</span>
          <input id="firstName" name="firstName" type="text" placeholder="ex. Juan" class="reg-input" required value="<?=htmlspecialchars($_SESSION['reg_step1']['firstName']??'')?>"/>
        </label>

        <label class="reg-label" for="middleName">Middle name
          <input id="middleName" name="middleName" type="text" placeholder="ex. Dela Cruz" class="reg-input" value="<?=htmlspecialchars($_SESSION['reg_step1']['middleName']??'')?>"/>
        </label>

        <label class="reg-label" for="lastName">Last name <span style="color:red">*</span>
          <input id="lastName" name="lastName" type="text" placeholder="ex. Dela Cruz" class="reg-input" required value="<?=htmlspecialchars($_SESSION['reg_step1']['lastName']??'')?>"/>
        </label>
      </div>

      <div class="reg-row">
        <label class="reg-label" for="dob">Date of Birth <span style="color:red">*</span>
          <input id="dob" name="dob" type="date" class="reg-input" required value="<?=htmlspecialchars($_SESSION['reg_step1']['dob']??'')?>"/>
          <small style="font-size:.8rem;color:#666">dd/mm/yyyy</small>
        </label>

        <label class="reg-label" for="sex">Sex <span style="color:red">*</span>
          <select id="sex" name="sex" class="reg-input" required>
            <option value="Male"   <?=($_SESSION['reg_step1']['sex']??'')==='Male'?'selected':''?>>Male</option>
            <option value="Female" <?=($_SESSION['reg_step1']['sex']??'')==='Female'?'selected':''?>>Female</option>
          </select>
        </label>
      </div>

      <div class="reg-row">
        <label class="reg-label" for="email">Email Address <span style="color:red">*</span>
          <input id="email" name="email" type="email" placeholder="ex. juan.delacruz@gmail.com" class="reg-input" required value="<?=htmlspecialchars($_SESSION['reg_step1']['email']??'')?>"/>
        </label>

        <label class="reg-label" for="contactNumber">Contact number <span style="color:red">*</span>
          <input id="contactNumber" name="contactNumber" type="tel" placeholder="ex. 09123456789" class="reg-input" pattern="[0-9]{10,15}" required value="<?=htmlspecialchars($_SESSION['reg_step1']['contactNumber']??'')?>"/>
          <small style="font-size:.8rem;color:#666">Numbers only, 10-15 digits</small>
        </label>
      </div>

      <div class="reg-row">
        <label class="reg-label-full" for="program"><span style="color:red">*</span> Program Applying for:
          <select id="program" name="program" class="reg-input" required>
            <option value="">-- Select a program --</option>
            <option <?=($_SESSION['reg_step1']['program']??'')==='Associate in Computer Technology'?'selected':''?>>Associate in Computer Technology</option>
            <option <?=($_SESSION['reg_step1']['program']??'')==='Bachelor of Science in Information Technology'?'selected':''?>>Bachelor of Science in Information Technology</option>
            <option <?=($_SESSION['reg_step1']['program']??'')==='Bachelor of Science in Civil Engineering'?'selected':''?>>Bachelor of Science in Civil Engineering</option>
          </select>
        </label>
      </div>

      <div class="reg-row">
        <label class="reg-label" for="term"><span style="color:red">*</span> Term Applying for:
          <select id="term" name="term" class="reg-input" required>
            <option value="">Select term</option>
            <option <?=($_SESSION['reg_step1']['term']??'')==='1st Semester'?'selected':''?>>1st Semester</option>
            <option <?=($_SESSION['reg_step1']['term']??'')==='2nd Semester'?'selected':''?>>2nd Semester</option>
            <option <?=($_SESSION['reg_step1']['term']??'')==='3rd Semester'?'selected':''?>>3rd Semester</option>
            <option <?=($_SESSION['reg_step1']['term']??'')==='4th Semester'?'selected':''?>>4th Semester</option>
          </select>
        </label>

        <label class="reg-label" for="applyingAs"><span style="color:red">*</span> Applying as:
          <select id="applyingAs" name="applyingAs" class="reg-input" required>
            <option <?=($_SESSION['reg_step1']['applyingAs']??'')==='New Student'?'selected':''?>>New Student</option>
            <option <?=($_SESSION['reg_step1']['applyingAs']??'')==='Transferee'?'selected':''?>>Transferee</option>
            <option <?=($_SESSION['reg_step1']['applyingAs']??'')==='Returning'?'selected':''?>>Returning</option>
          </select>
        </label>
      </div>

      <button type="submit" id="nextButton" class="reg-next">Next →</button>
    </form>
  </section>

  <script>
    const form = document.getElementById('registration-form');
    const btn  = document.getElementById('nextButton');

    function toggleBtn(){ btn.disabled = !form.checkValidity(); }
    form.addEventListener('input', toggleBtn);
    toggleBtn();
  </script>
</body>
</html>