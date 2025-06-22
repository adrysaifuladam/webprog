<?php
require('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $payment = $_POST['payment'];

    if (!$payment) {
        echo "Please choose a payment method.";
        exit();
    }

    $sql = "SELECT * FROM product WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Purchase Confirmation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f8f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .confirmation-box {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }

    h2 {
      color: #333;
      margin-bottom: 20px;
    }

    .info {
      font-size: 16px;
      margin: 10px 0;
    }

    .info strong {
      color: #ee3e75;
    }

    .qr-image {
      margin-top: 20px;
    }

    .qr-image img {
      width: 200px;
      border: 3px solid #ee3e75;
      border-radius: 10px;
    }

    .back-btn {
      display: inline-block;
      margin-top: 30px;
      background-color: #ee3e75;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 6px;
    }

    .back-btn:hover {
      background-color: #d13568;
    }
  </style>
</head>
<body>

<div class="confirmation-box">
  <h2>✅ Purchase Confirmed</h2>
  <p class="info">Product: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
  <p class="info">Price: <strong>RM<?= htmlspecialchars($product['price']) ?></strong></p>
  <p class="info">Payment Method: <strong><?= strtoupper($payment) ?></strong></p>

  <?php if ($payment === 'qr'): ?>
    <div class="qr-image">
      <p>Please scan the QR code to complete your payment:</p>
      <img src="qr.png" alt="QR Code">
    </div>
  <?php else: ?>
    <p class="info">Please prepare your cash to pay upon delivery.</p>
  <?php endif; ?>

  <a href="home.php" class="back-btn">Back to Home</a>
</div>

</body>
</html>
