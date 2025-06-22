<?php
session_start();
require('connect.php');

if (!isset($_GET['id'])) {
  echo "Invalid product ID.";
  exit();
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("UPDATE product SET status = 'sold' WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cash Payment</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 50px;
      background-color: #f7f7f7;
    }

    h2 {
      color: #333;
    }

    .success {
      font-size: 22px;
      color: #28a745;
      margin-top: 20px;
    }

    .instructions {
      font-size: 16px;
      color: #555;
      margin-top: 15px;
    }

    .btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: #ee3e75;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
    }

    .btn:hover {
      background-color: #d13568;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 30px;
      flex-wrap: wrap;
    }
  </style>
</head>
<body>

  <h2>Cash Payment Selected</h2>
  <p class="instructions">Please pay the seller directly using cash upon delivery or meet-up.</p>
  <p class="success">Payment confirmed. The product has been marked as sold.</p>

  <div class="button-group">
    <a href="home.php" class="btn">← Back to Home</a>
    <a href="message.php?product_id=<?= $product_id ?>" class="btn">Message Seller</a>
  </div>

</body>
</html>
