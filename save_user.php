<?php
require('connect.php');

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$isAdmin = isset($_POST['is_admin']);
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';

if ($isAdmin) {
    $sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
} else {
    $sql = "INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $phone, $password);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Registration Result</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      text-align: center;
      padding: 50px;
    }
    .message-box {
      max-width: 500px;
      margin: auto;
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .success {
      color: #28a745;
      font-size: 18px;
    }
    .error {
      color: #dc3545;
      font-size: 18px;
    }
    a {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #007bff;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="message-box">
    <?php
    if ($stmt->execute()) {
        echo "<div class='success'>✅ Registration successful.</div>";
        echo "<a href='index.php'>Click here to log in</a>";
    } else {
        echo "<div class='error'>❌ Registration failed. The email might already be used.</div>";
        echo "<a href='register.php'>Go back to Register</a>";
    }

    $stmt->close();
    $conn->close();
    ?>
  </div>
</body>
</html>
