<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Content Moderation - BELIBALIK</title>
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

    .latest-listings {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .listing-card {
      background-color: #fff;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
      width: 150px;
      text-align: center;
    }

    .listing-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 8px;
    }

    .listing-card h4 {
      margin: 5px 0;
      font-size: 14px;
      color: #333;
    }

    .listing-card p {
      font-size: 13px;
      color: #576ce0;
      font-weight: bold;
      margin: 0;
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
    <?= htmlspecialchars($_SESSION['admin_email'] ?? 'Admin') ?>
  </div>
</header>

<div class="container">
  <div class="sidebar">
    <h3>Welcome, Admin.</h3>
    <a href="admin_home.php">Home</a>
    <a href="usermanagement.php">User Management</a>
    <a href="#">Content Moderation</a>
    <a href="chathistory.php">Chat History</a>
    <a href="index.php">Logout</a>
  </div>
  <div class="main-content">
    <h2>Content Moderation</h2>

 <h3>Latest Listings</h3>

<div class="latest-listings">
<?php
  require('connect.php');

  // ✅ Only select products with status 'available'
  $sql = "SELECT name, price, image FROM product WHERE status = 'available' ORDER BY id DESC";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo '<div class="listing-card">';
      echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
      echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
      echo '<p>RM' . number_format($row['price'], 2) . '</p>';
      echo '</div>';
    }
  } else {
    echo "<p>No available product listings yet.</p>";
  }

  $conn->close();
?>
</div>


  </div>
</div>

</body>
</html>

