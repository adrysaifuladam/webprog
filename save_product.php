<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('connect.php');

if (!isset($_SESSION['username'])) {
    echo "❌ You must be logged in to post a product.";
    exit();
}

$seller = $_SESSION['username']; 
$name = $_POST['name'];
$price = $_POST['price'];
$imagePath = "";
$stmt = $conn->prepare("INSERT INTO product (name, price, image, seller, seller_username, status) VALUES (?, ?, ?, ?, ?, 'available')");
$stmt->bind_param("sdsss", $name, $price, $imagePath, $seller, $seller_username);

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $imagePath = $targetDir . uniqid() . "_" . basename($_FILES["image"]["name"]);

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        echo "❌ Failed to upload image.";
        exit;
    }
}

// Insert product with seller
$sql = "INSERT INTO product (name, price, image, seller) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ SQL Prepare failed: " . $conn->error);
}

$stmt->bind_param("sdss", $name, $price, $imagePath, $seller);

if ($stmt->execute()) {
   
} else {
    echo "❌ Error: " . $stmt->error;
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Posted</title>
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

  <h2>Product Posted Successfully</h2>
  <p class="instructions">Your product has been listed and is now available in the marketplace.</p>
  <p class="success">Thank you for selling with us, <?= htmlspecialchars($seller) ?>!</p>

  <div class="button-group">
    <a href="home.php" class="btn">← Back to Home</a>
  </div>

</body>
</html>
