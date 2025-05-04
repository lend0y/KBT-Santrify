<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'santriify_db');

// Create connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Base URL
define('BASE_URL', 'http://localhost/KBT-Santrify/');

// Site settings
define('SITE_NAME', 'Santrify');
define('SITE_DESC', 'Platform Edukasi Agama Islam');
?>