<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship Academic Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--green:#006400;--light:#90EE90;}
        .navbar{background:var(--green);}
        .navbar-brand,.nav-link{color:#fff!important;}
        .nav-link:hover{color:var(--light)!important;}
        .btn-primary{background:var(--green);border:none;}
        .btn-primary:hover{background:#004d00;}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">SCHOLARSHIP ACADEMIC CARE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role']==='student'): ?>
                        <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">