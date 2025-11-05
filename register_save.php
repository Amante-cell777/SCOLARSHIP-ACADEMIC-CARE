<?php
session_start();
require 'db_connect.php'; // your PDO connection

// === SECURITY: Prevent direct access ===
if (empty($_SESSION['reg_step1']) || empty($_SESSION['reg_step2'])) {
    error_log("Registration save attempted without session data from " . ($_SERVER['HTTP_REFERER'] ?? 'unknown'));
    die('Invalid access. Please complete the registration form.');
}

$data = array_merge($_SESSION['reg_step1'], $_SESSION['reg_step2']);

// === VALIDATION: Required fields ===
$required = [
    'firstName', 'lastName', 'email', 'contactNumber', 
    'program', 'term', 'applyingAs', 
    'guardian_name', 'guardian_contact', 'year_level'
];
foreach ($required as $field) {
    if (empty($data[$field])) {
        die("Missing required field: $field");
    }
}

// === PASSWORD: Must exist ===
if (empty($data['password'])) {
    die('Password is required.');
}
$pass = password_hash($data['password'], PASSWORD_DEFAULT);

// === INSERT INTO DATABASE ===
try {
    $stmt = $pdo->prepare("
        INSERT INTO users 
        (first_name, middle_name, last_name, dob, sex, email, contact, program, term, applying_as,
         guardian_name, guardian_contact, gpa, year_level, password, role)
        VALUES 
        (:first,:mid,:last,:dob,:sex,:email,:contact,:prog,:term,:apply,
         :guard_name,:guard_contact,:gpa,:year,:pass,'student')
    ");

    $stmt->execute([
        ':first'        => $data['firstName'],
        ':mid'          => $data['middleName'] ?? null,
        ':last'         => $data['lastName'],
        ':dob'          => $data['dob'] ?? null,
        ':sex'          => $data['sex'] ?? null,
        ':email'        => $data['email'],
        ':contact'      => $data['contactNumber'],     // matches DB column
        ':prog'         => $data['program'],
        ':term'         => $data['term'],
        ':apply'        => $data['applyingAs'],
        ':guard_name'   => $data['guardian_name'],
        ':guard_contact'=> $data['guardian_contact'],
        ':gpa'          => $data['gpa'] ?? null,
        ':year'         => $data['year_level'],
        ':pass'         => $pass
    ]);

} catch (PDOException $e) {
    // === FRIENDLY + SECURE ERROR MESSAGES ===
    $error = $e->getMessage();

    // 1. Duplicate email?
    if (strpos($error, 'UNIQUE') !== false || stripos($error, 'email') !== false) {
        die('This email is already registered. Please use a different email or <a href="login.php">login</a>.');
    }

    // 2. Contact number too long?
    if (strpos($error, 'Data too long for column') !== false) {
        die('Contact number is too long. Please use 10–15 digits.');
    }

    // 3. Other DB issues → log + generic message
    error_log("Registration DB Error: " . $error);
    die('Registration failed. Please check your data and try again.');
}

// === SUCCESS: Store email + flag ===
$_SESSION['user_email'] = $data['email'];
$_SESSION['just_registered'] = true;

// === CLEANUP ===
unset($_SESSION['reg_step1'], $_SESSION['reg_step2']);

// === REDIRECT ===
header('Location: register_success.php');
exit;
?>