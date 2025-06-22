<?php
$servername = "localhost";
$username = "d032310150";     // Your MySQL username
$password = "1234";           // Your MySQL password
$dbname = "student_d032310150";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
