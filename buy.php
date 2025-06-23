<?php
require('connect.php');

if (!isset($_GET['id'])) {
    echo "Invalid product ID.";
    exit();
}

$product_id = $_GET['id'];


$update = $conn->prepare("UPDATE product SET status = 'sold' WHERE id = ?");
$update->bind_param("i", $product_id);
$update->execute();

$conn->close();


header("Location: home.php");
exit();

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Buy <?= htmlspecialchars($product['name']) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 30px;
      display: flex;
      justify-content: center;
    }

    .box {
      background: white;
      padding: 25px;
      width: 400px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    img.product {
      width: 100%;
      height: auto;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .price {
      color: #ee3e75;
      font-weight: bold;
      font-size: 18px;
    }

    button {
      background: #ee3e75;
      border: none;
      color: white;
      padding: 12px 20px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background: #d13568;
    }

    .qr-image {
      display: none;
      margin-top: 15px;
    }

    .qr-image img {
      width: 200px;
    }
  </style>
</head>
<body>

<div class="box">
  <h2><?= htmlspecialchars($product['name']) ?></h2>
  <img class="product" src="<?= htmlspecialchars($product['image']) ?>" alt="Product">
  <p class="price">RM <?= htmlspecialchars($product['price']) ?></p>

  <form action="confirm_buy.php" method="POST">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    <p>Select payment:</p>
    <label><input type="radio" name="payment" value="qr"> QR Pay</label><br>
    <label><input type="radio" name="payment" value="cash"> Cash</label><br><br>

    <button type="submit">Buy Now</button>
  </form>
</div>

</body>
</html>
