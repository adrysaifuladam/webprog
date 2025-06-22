<?php
session_start();
require('connect.php');

if (!isset($_GET['id']) || !isset($_SESSION['username'])) {
    echo "Invalid request.";
    exit();
}

$product_id = $_GET['id'];
$buyer = $_SESSION['username'];
$current_time = date("Y-m-d H:i:s"); // Get current date and time
$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();
$stmt->close();


$stmt = $conn->prepare("UPDATE product SET status = 'sold', buyer = ?, created_at = ? WHERE id = ?");
$stmt->bind_param("ssi", $buyer, $current_time, $product_id);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Payment Confirmed</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      text-align: center;
      padding: 50px;
    }
    .qr-container {
      margin-top: 30px;
    }
    .qr-container img {
      width: 200px;
      height: 200px;
    }
    h2 {
      color: #333;
    }
    .success {
      font-size: 20px;
      color: #28a745;
      margin-top: 20px;
    }
    .product-info {
      font-size: 16px;
      margin-top: 15px;
    }
    .home-btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: #ee3e75;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
    }
    .home-btn:hover {
      background-color: #d13568;
    }
  </style>
</head>
<body>

  <h2>QR Payment Successful</h2>

  <div class="qr-container">
    <img src="qr.png" alt="Scan this QR code">
  </div>

  <p class="success">Thank you for your payment.</p>

  <div class="product-info">
    <p>Product: <?= htmlspecialchars($product['name']) ?></p>
    <p>Price: RM<?= htmlspecialchars($product['price']) ?></p>
    <p>Purchased at: <?= $current_time ?></p>
  </div>

  <a href="home.php" class="home-btn">← Back to Home</a>

</body>
</html>
