<?php
session_start();
require('connect.php');

$email = $_POST['email'];
$password = $_POST['password'];
$isAdmin = isset($_POST['is_admin']);
$table = $isAdmin ? "admin" : "users";

$sql = "SELECT * FROM $table WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$login_successful = false;
$error_message = "";

if ($result->num_rows === 1) {
    $account = $result->fetch_assoc();

    if (password_verify($password, $account['password'])) {
        if ($isAdmin) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $account['email'];
            header("Location: admin_home.php");
        } else {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $account['email'];
            $_SESSION['username'] = $account['username'];
            header("Location: home.php");
        }
        exit();
    } else {
        $error_message = "Wrong password.";
    }
} else {
    $error_message = "Email not found.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login Failed</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 50px;
      text-align: center;
    }
    .box {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      max-width: 400px;
      margin: auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .error {
      color: #dc3545;
      font-size: 18px;
      margin-bottom: 20px;
    }
    a {
      text-decoration: none;
      color: #007bff;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="box">
    <div class="error">❌ <?= htmlspecialchars($error_message) ?></div>
    <a href="index.php">Try again</a>
  </div>
</body>
</html>
