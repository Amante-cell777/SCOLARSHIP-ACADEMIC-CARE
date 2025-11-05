<?php
session_start();

/* -------------------------------------------------
   1. Receive data from step 2 (POST)
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim everything and keep the raw password
    $_SESSION['reg_step2'] = array_map('trim', $_POST);
} else {
    // If we arrived directly, make sure step 1 exists
    if (empty($_SESSION['reg_step1'])) {
        header('Location: register.php');
        exit;
    }
}

/* -------------------------------------------------
   2. Merge step 1 + step 2
   ------------------------------------------------- */
$all = array_merge($_SESSION['reg_step1'], $_SESSION['reg_step2'] ?? []);

/* -------------------------------------------------
   3. Make sure the password field is present
   ------------------------------------------------- */
if (empty($all['password'])) {
    die('Password field is missing. Please go back and fill the form correctly.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Confirm Your Data – Scholarship Academic Care</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-green:#006400; }
        .navbar { background:var(--primary-green); }
        .card-header { background:#0d6efd; }
        .table th { background:#f8f9fa; }
        .btn-success { background:var(--primary-green); border:none; }
        .btn-success:hover { background:#004d00; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.html">Scholarship Academic Care</a>
    </div>
</nav>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h4 class="mb-0">Confirm Your Data</h4>
        </div>
        <div class="card-body">

            <!-- -------------- NICE TABLE -------------- -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // === ORDERED LABELS – PASSWORD COMES RIGHT AFTER EMAIL ===
                        $labels = [
                            'firstName'        => 'First Name',
                            'middleName'       => 'Middle Name',
                            'lastName'         => 'Last Name',
                            'dob'              => 'Date of Birth',
                            'sex'              => 'Sex',
                            'email'            => 'Email',
                            'password'         => 'Password',        // ← NEW: AFTER EMAIL
                            'contactNumber'    => 'Contact Number',
                            'program'          => 'Program',
                            'term'             => 'Term',
                            'applyingAs'       => 'Applying As',
                            'guardian_name'    => 'Guardian Name',
                            'guardian_contact' => 'Guardian Contact',
                            'gpa'              => 'GPA',
                            'year_level'       => 'Year Level',
                        ];

                        foreach ($labels as $key => $label) {
                            $value = $all[$key] ?? '';

                            // Format DOB
                            if ($key === 'dob' && $value) {
                                $value = date('F j, Y', strtotime($value));
                            }

                            // Mask password (show only asterisks)
                            if ($key === 'password') {
                                $value = str_repeat('•', 12); // or use: '********'
                            }

                            echo "<tr><th>{$label}</th><td>" . htmlspecialchars($value) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- -------------- ACTION BUTTONS -------------- -->
            <form method="post" action="register_save.php" class="mt-4 d-flex justify-content-between flex-wrap gap-2">
                <!-- Hidden fields – everything (including real password) is sent -->
                <?php foreach ($all as $k => $v): ?>
                    <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                <?php endforeach; ?>

                <a href="register_step2.php" class="btn btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-success">Submit Registration</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>