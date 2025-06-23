<?php
session_start();
require('connect.php');

if (!isset($_GET['id']) || !isset($_SESSION['username'])) {
    echo "Invalid request.";
    exit();
}

$product_id = $_GET['id'];
$buyer = $_SESSION['username'];
$current_time = date("Y-m-d H:i:s"); 


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
  <title>Cash Payment Confirmed</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 50px;
      text-align: center;
    }
    h2 {
      color: #333;
    }
    .details {
      font-size: 18px;
      margin: 20px 0;
    }
    .success {
      color: #28a745;
      font-weight: bold;
      margin-top: 20px;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      margin: 20px auto 0;
      background-color: #ee3e75;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #d13568;
    }
  </style>
</head>
<body>

<h2>Cash Payment Confirmed</h2>

<p class="success">Thank you! Your purchase has been recorded.</p>

<div class="details">
  <p><strong>Product:</strong> <?= htmlspecialchars($product['name']) ?></p>
  <p><strong>Price:</strong> RM<?= number_format($product['price'], 2) ?></p>
  <p><strong>Purchased At:</strong> <?= $current_time ?></p>
</div>

<a href="home.php" class="btn">← Back to Home</a>

</body>
</html>
