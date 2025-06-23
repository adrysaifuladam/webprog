<?php
$servername = "localhost";
$username = "d032310150";   
$password = "1234";          
$dbname = "student_d032310150";  

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
