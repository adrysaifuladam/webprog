<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $seller_username = $_SESSION['username'];
    $seller = $seller_username;

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $sql = "INSERT INTO product (name, price, image, seller, seller_username, `condition`, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'available')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssss", $name, $price, $imagePath, $seller, $seller_username, $condition, $description);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sell a Product</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 40px 20px;
    }

    .container {
      max-width: 500px;
      margin: auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #2c3e50;
      font-size: 24px;
    }

    form input[type="text"],
    form input[type="number"],
    form input[type="file"],
    form select,
    form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }

    form textarea {
      resize: vertical;
      min-height: 100px;
    }

    form button {
      width: 100%;
      padding: 12px;
      background-color: #ee3e75;
      color: #fff;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    form button:hover {
      background-color: #d13568;
    }

    .back-btn {
      display: block;
      text-align: center;
      margin-top: 25px;
      color: #ee3e75;
      font-weight: bold;
      text-decoration: none;
      font-size: 15px;
    }

    .back-btn:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Sell a Product</h2>

    <form action="sell.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Product Name" required>
      <input type="number" name="price" placeholder="Price (RM)" step="0.01" required>

      <label for="condition">Condition:</label>
      <select name="condition" required>
        <option value="">-- Select Condition --</option>
        <option value="Brand New">Brand New</option>
        <option value="Lightly Used">Lightly Used</option>
      </select>

      <label for="description">Description:</label>
      <textarea name="description" placeholder="Description" required></textarea>

      <label for="image">Choose photo for item:</label><br>
      <input type="file" name="image" id="image" accept="image/*" required>

      <button type="submit">Post Product</button>
    </form>

    <a href="home.php" class="back-btn">← Back to Home</a>
  </div>

</body>
</html>
