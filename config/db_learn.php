<?php
// config/db_learn.php - Database connection configuration
$host = "localhost";
$dbname = "hr2_db";
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (usually empty for XAMPP)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Optional: Test connection
    // echo "Database connection successful!";
    
} catch(PDOException $e) {
    // Log the error
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}
?>