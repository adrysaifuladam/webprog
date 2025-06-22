<?php
session_start();
require('connect.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $_SESSION['username'] = $row['username'];
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['admin_mode'] = true;
    header("Location: home.php");
    exit();
} else {
    echo "No matching user account found for admin.";
}
