<?php
$host = "localhost";   // kung WAMP/XAMPP, ito na yun
$user = "root";        // default sa XAMPP
$pass = "";            // lagay mo kung may password ang MySQL mo
$db   = "hr2_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
