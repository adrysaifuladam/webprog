<?php
session_start();
require('connect.php');

if (!isset($_GET['id'])) {
  echo "Invalid product ID.";
  exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Choose Payment</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 50px;
      margin: 0;
    }
    .top-bar {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
    }
    .back-btn {
      text-decoration: none;
      background: #ee3e75;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: bold;
    }
    .back-btn:hover {
      background: #d13568;
    }
    .container {
      margin-top: 100px;
      text-align: center;
    }
    h2 {
      color: #333;
      margin-bottom: 30px;
    }
    .options {
      margin-top: 30px;
    }
    .btn {
      display: inline-block;
      margin: 15px;
      padding: 12px 25px;
      background: #ee3e75;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .btn:hover {
      background: #d13568;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <a href="home.php" class="back-btn">← Back to Home</a>
</div>

<div class="container">
  <h2>Choose Your Payment Method</h2>
  <div class="options">
    <a href="qr_payment_confirm.php?id=<?= $id ?>" class="btn">Pay with QR</a>
    <a href="cash_payment_confirm.php?id=<?= $id ?>" class="btn">Pay with Cash</a>
  </div>
</div>

</body>
</html>
