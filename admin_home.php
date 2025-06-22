<?php
session_start();
require('connect.php');

if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

$user_count = 0;
$sold_count = 0;
$available_count = 0;

$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $user_count = $row['total'];
}

$sql = "SELECT COUNT(*) AS sold FROM product WHERE status = 'sold'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $sold_count = $row['sold'];
}

$sql = "SELECT COUNT(*) AS available FROM product WHERE status = 'available'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $available_count = $row['available'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Home - BELIBALIK</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f6f9;
    }

    header {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      padding: 10px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 30px;
      margin-right: 10px;
    }

    .logo span {
      font-size: 20px;
      font-weight: bold;
      color: #ee3e75;
    }

    .admin-info {
      font-size: 14px;
      color: #333;
    }

    .container {
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #fff;
      padding: 20px;
      border-right: 1px solid #ddd;
      height: 100vh;
    }

    .sidebar h3 {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .sidebar a {
      display: block;
      padding: 10px;
      background-color: #576ce0;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
    }

    .sidebar a:hover {
      background-color: #4054c4;
    }

    .main-content {
      flex: 1;
      padding: 30px;
    }

    .main-content h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .info-box {
      background-color: #e1e4ea;
      padding: 20px;
      width: 300px;
      border-radius: 8px;
      margin-bottom: 30px;
    }

    .action-btn {
      display: inline-block;
      background-color: #f87c9b;
      color: white;
      padding: 10px 20px;
      margin-right: 10px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="belibalik.jpg" alt="BELIBALIK Logo">
    <span>BELIBALIK</span>
  </div>
  <div class="admin-info">
    <?= htmlspecialchars($_SESSION['admin_email']) ?> (Admin)
  </div>
</header>

<div class="container">
  <div class="sidebar">
    <h3>Welcome, Admin.</h3>
    <a href="admin_home.php">Home</a>
    <a href="usermanagement.php">User Management</a>
    <a href="contentmoderation.php">Content Moderation</a>
    <a href="chathistory.php">Chat History</a>
    <a href="index.php">Logout</a>
  </div>

  <div class="main-content">
    <h2>Dashboard</h2>

    <div class="info-box">
      <p>Total Users: <strong><?= $user_count ?></strong></p>
      <p>Items Sold: <strong><?= $sold_count ?></strong></p>
      <p>Items in Market: <strong><?= $available_count ?></strong></p>
    </div>

    <a href="home.php" class="action-btn">User Home Page</a>
  </div>
</div>

</body>
</html>
