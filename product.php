<?php
require('connect.php');

if (!isset($_GET['id'])) {
  echo "Invalid product ID.";
  exit();
}

$id = $_GET['id'];

$sql = "SELECT p.*, u.username AS seller_name
        FROM product p
        LEFT JOIN users u ON p.seller_username = u.username
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  echo "Product not found.";
  exit();
}

$product = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($product['name']) ?> - Details</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      padding: 40px;
      background: #f4f6f9;
    }

    .product-detail {
      max-width: 550px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 0 12px rgba(0,0,0,0.06);
      text-align: left;
    }

    .product-detail img {
      width: 100%;
      max-height: 320px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 10px;
    }

    .price {
      text-align: center;
      font-size: 22px;
      color: #ee3e75;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .meta-label {
      font-weight: bold;
      color: #555;
      margin-top: 10px;
    }

    .meta-value {
      margin-bottom: 15px;
      color: #333;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }

    .buttons a {
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      transition: 0.3s;
      font-size: 14px;
    }

    .buy-btn {
      background-color: #ee3e75;
      color: white;
    }

    .buy-btn:hover {
      background-color: #d13568;
    }

    .message-btn {
      background-color: #eee;
      color: #333;
    }

    .message-btn:hover {
      background-color: #ddd;
    }

    .back-link {
      display: block;
      margin-top: 25px;
      text-align: center;
      text-decoration: none;
      color: #ee3e75;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="product-detail">
  <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

  <h2><?= htmlspecialchars($product['name']) ?></h2>
  <p class="price">RM<?= htmlspecialchars($product['price']) ?></p>

  <p class="meta-label">Seller:</p>
  <p class="meta-value"><?= htmlspecialchars($product['seller'] ?? 'Unknown') ?></p>

  <p class="meta-label">Condition:</p>
  <p class="meta-value"><?= htmlspecialchars($product['condition'] ?? 'Not specified') ?></p>

  <p class="meta-label">Description:</p>
  <p class="meta-value"><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>

  <div class="buttons">
    <a href="payment.php?id=<?= $product['id'] ?>" class="buy-btn">Buy</a>
    <a href="message.php?product_id=<?= $product['id'] ?>" class="message-btn">Message</a>
  </div>

  <a href="home.php" class="back-link">← Back to Home</a>
</div>

</body>
</html>
