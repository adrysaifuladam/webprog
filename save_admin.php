<?php
require('connect.php');

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    echo "✅ Admin account created. <a href='index.php'>Log in</a>";
} else {
    echo "❌ Failed to register admin. Email might already be used.";
}

$stmt->close();
$conn->close();
?>
