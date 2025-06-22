<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$is_admin = false;

$stmt = $conn->prepare("SELECT is_admin FROM users WHERE username = ?");
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($admin_flag);
    if ($stmt->fetch()) {
        $is_admin = ($admin_flag == 1);
    }
    $stmt->close();
} else {
    die("Prepare failed: " . $conn->error);
}

$pic_stmt = $conn->prepare("SELECT profile_picture FROM users WHERE username = ?");
if (!$pic_stmt) {
    die("Prepare failed: " . $conn->error);
}
$pic_stmt->bind_param("s", $username);
$pic_stmt->execute();
$pic_result = $pic_stmt->get_result();
$pic_row = $pic_result->fetch_assoc();
$profile_pic = $pic_row['profile_picture'] ?? 'gambar.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Homepage</title>
  <link rel="stylesheet" href="home.css" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      background-color: #fff;
      transition: transform 0.5s ease-in-out;
    }
    body.slide-out { transform: translateX(-100%); }

    header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 15px 30px; background-color: #fff; border-bottom: 1px solid #ddd;
    }

    .logo-section {
      display: flex; align-items: center; gap: 20px;
    }

    .logo img { height: 40px; }

    nav {
      display: flex; gap: 20px;
    }

    nav a {
      text-decoration: none; color: #333; font-weight: bold;
    }

    nav a.active { color: #ee3e75; }

    .right-section {
      display: flex; align-items: center; gap: 15px;
    }

    .user-info {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none; color: #000;
    }

    .user-info img {
      width: 32px; height: 32px; border-radius: 50%;
      object-fit: cover; border: 1px solid #ccc;
    }

    .logout-btn {
      background-color: #ee3e75; color: white;
      padding: 8px 14px; text-decoration: none;
      border-radius: 6px; font-weight: bold;
    }

    .logout-btn:hover { background-color: #d13568; }

    .search-bar {
      margin: 20px; display: flex; justify-content: center; gap: 10px;
    }

    .search-bar input {
      padding: 10px; width: 200px;
      border: 1px solid #ccc; border-radius: 5px;
    }

    .search-bar button {
      padding: 10px 15px; background-color: #ee3e75;
      color: white; border: none; border-radius: 5px; cursor: pointer;
    }

    .listings {
      display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;
      padding: 20px;
    }

    .item {
      width: 150px; text-align: center;
      text-decoration: none; color: black;
      border: 2px solid transparent;
    }

    .item img {
      width: 100%; height: auto; border-radius: 10px;
    }

    h3 {
      text-align: center;
      margin-top: 50px;
      font-size: 20px;
      color: #333;
    }
  </style>
</head>
<body>

  <header>
    <div class="logo-section">
      <div class="logo">
        <img src="logo.jpg" alt="Logo">
      </div>
      <nav>
        <a href="home.php" class="active">Shopping</a>
        <a href="#" id="sell-link">Sell</a>
        <a href="chat_list.php">Chat</a>
      </nav>
    </div>

    <div class="right-section">
      <a href="profile.php" class="user-info">
        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture">
        <span><?= htmlspecialchars($username) ?></span>
      </a>
      <?php if ($is_admin): ?>
        <a href="admin_home.php" class="logout-btn" style="background-color: #28a745;">⬅ Admin Dashboard</a>
      <?php endif; ?>
      <a href="index.php" class="logout-btn">Logout</a>
    </div>
  </header>

  <main>
    <div class="search-bar">
      <input type="text" placeholder="Search" id="searchInput">
      <button id="searchBtn">Go</button>
    </div>

    <div class="listings" id="productList">
      <?php
      $sql = "SELECT * FROM product WHERE status != 'sold' ORDER BY id DESC";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $name = htmlspecialchars($row['name']);
          $price = htmlspecialchars($row['price']);
          $image = htmlspecialchars($row['image']);
          echo '<a href="product.php?id=' . $row['id'] . '" class="item" data-name="' . strtolower($name) . '">';
          echo '<img src="' . $image . '" alt="' . $name . '">';
          echo '<p>' . $name . '</p>';
          echo '<span>RM' . $price . '</span>';
          echo '</a>';
        }
      } else {
        echo "<p>No available products.</p>";
      }
      ?>
    </div>

    <h3>Purchased Products (Chat Again)</h3>
    <div class="listings">
      <?php
      $stmt = $conn->prepare("SELECT * FROM product WHERE buyer = ? ORDER BY id DESC");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $purchased = $stmt->get_result();
      if ($purchased->num_rows > 0) {
        while ($row = $purchased->fetch_assoc()) {
          $name = htmlspecialchars($row['name']);
          $price = htmlspecialchars($row['price']);
          $image = htmlspecialchars($row['image']);
          $id = $row['id'];
          echo '<a href="message.php?product_id=' . $id . '" class="item" data-name="' . strtolower($name) . '">';
          echo '<img src="' . $image . '" alt="' . $name . '">';
          echo '<p>' . $name . '</p>';
          echo '<span>RM' . $price . '</span>';
          echo '</a>';
        }
      } else {
        echo "<p style='text-align:center;'>No purchased products found.</p>";
      }
      $stmt->close();
      $conn->close();
      ?>
    </div>
  </main>

  <script>
    const sellLink = document.getElementById("sell-link");
    sellLink.addEventListener("click", function (e) {
      e.preventDefault();
      document.body.classList.add("slide-out");
      setTimeout(() => {
        window.location.href = "sell.php";
      }, 500);
    });

    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");

    searchBtn.addEventListener("click", () => {
      const keyword = searchInput.value.trim().toLowerCase();
      const items = document.querySelectorAll(".item");
      items.forEach(item => {
        const itemName = item.getAttribute("data-name");
        item.style.display = itemName.includes(keyword) ? "block" : "none";
      });
    });

    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        searchBtn.click();
      }
    });
  </script>
</body>
</html>
