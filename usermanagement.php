<?php
session_start();
require('connect.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: index.php");
    exit();
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ?");
    $param = "%$search%";
    $stmt->bind_param("ss", $param, $param);
} else {
    $stmt = $conn->prepare("SELECT * FROM users");
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management - BELIBALIK</title>
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
    .search-bar {
      display: flex;
      margin-bottom: 20px;
    }
    .search-bar input {
      padding: 10px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .search-bar button {
      padding: 10px;
      margin-left: 10px;
      background-color: #ee3e75;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .user-card {
      background-color: #fff;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }
    .user-info h4 {
      margin: 0;
      font-size: 16px;
    }
    .user-info p {
      margin: 5px 0;
      font-size: 14px;
      color: #666;
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
    <?= htmlspecialchars($_SESSION['admin_email']) ?>
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
    <h2>User Management</h2>
    <form method="get" class="search-bar">
      <input type="text" name="search" placeholder="Search User" value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Search</button>
    </form>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($user = $result->fetch_assoc()): ?>
        <div class="user-card">
          <div class="user-info">
            <h4><?= htmlspecialchars($user['username']) ?></h4>
            <p><?= htmlspecialchars($user['email']) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No users found.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
