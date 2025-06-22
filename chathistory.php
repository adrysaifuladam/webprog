<?php
session_start();
require('connect.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT m.*, p.name AS product_name 
    FROM messages m 
    LEFT JOIN product p ON m.product_id = p.id 
    ORDER BY m.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat History - BELIBALIK</title>
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
    .message-card {
      background-color: #fff;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }
    .message-card p {
      margin: 5px 0;
      font-size: 14px;
    }
    .timestamp {
      font-size: 12px;
      color: #999;
      text-align: right;
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
    <h2>Chat History</h2>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($msg = $result->fetch_assoc()): ?>
        <div class="message-card">
          <p><strong>Product:</strong> <?= htmlspecialchars($msg['product_name'] ?? 'Deleted Product') ?></p>
          <p><strong>From:</strong> <?= htmlspecialchars($msg['sender']) ?> → <strong>To:</strong> <?= htmlspecialchars($msg['receiver']) ?></p>
          <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
          <div class="timestamp"><?= htmlspecialchars($msg['created_at']) ?></div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No chat messages found.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
