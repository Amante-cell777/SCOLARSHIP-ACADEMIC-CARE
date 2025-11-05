<?php
// ==============================================================
//  Database Connection – Scholarship Academic Care
// ==============================================================

$host = 'localhost';
$db   = 'scholarship_db';
$user = 'root';          // CHANGE THIS in production!
$pass = '';              // CHANGE THIS in production!

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Hide raw error in production
    error_log("DB Connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Optional: Reuse the $pdo object globally if needed
// return $pdo; // if you include this in functions
?>