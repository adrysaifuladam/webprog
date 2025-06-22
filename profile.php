<?php 
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// ✅ Fixed column name: profile_picture (not profile_pic)
$stmt = $conn->prepare("SELECT id, username, email, phone, profile_picture FROM users WHERE username = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error); // Help debug if it fails again
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$review_stmt = $conn->prepare("
    SELECT r.rating, r.comment, r.created_at, u.username AS reviewer_name
    FROM reviews r
    JOIN users u ON r.reviewer_id = u.id
    WHERE r.reviewed_id = ?
    ORDER BY r.created_at DESC
");
$review_stmt->bind_param("i", $user_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();

$product_stmt = $conn->prepare("SELECT * FROM product WHERE seller_username = ? ORDER BY id DESC");
$product_stmt->bind_param("s", $username);
$product_stmt->execute();
$seller_products = $product_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 30px;
    }
    .profile-box {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    h2, h3 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    .info p {
      margin: 10px 0;
      font-size: 16px;
    }
    .btn {
      display: block;
      text-align: center;
      background-color: #ee3e75;
      color: white;
      padding: 10px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      margin: 20px auto;
      width: 50%;
    }
    .btn:hover {
      background-color: #d13568;
    }
    .review, .listing {
      border-top: 1px solid #ddd;
      padding-top: 10px;
      margin-top: 10px;
    }
    .review strong, .listing strong {
      color: #333;
    }
    .review em {
      color: #555;
      display: block;
      margin: 5px 0;
    }
    .review small {
      color: #999;
    }
    .listing {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    .listing img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    .back {
      text-align: center;
      margin-top: 20px;
    }
    .back a {
      text-decoration: none;
      color: #ee3e75;
      font-weight: bold;
    }
    .profile-pic {
      text-align: center;
      margin-bottom: 20px;
    }
    .profile-pic img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body>

<div class="profile-box">
  <h2>My Profile</h2>

  <?php if (!empty($user['profile_picture'])): ?>
    <div class="profile-pic">
      <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
    </div>
  <?php endif; ?>

  <div class="info">
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
  </div>

  <a href="edit_profile.php" class="btn">Edit Profile</a>

  <div class="reviews">
    <h3>User Reviews</h3>
    <?php if ($reviews->num_rows > 0): ?>
      <?php while ($row = $reviews->fetch_assoc()): ?>
        <div class="review">
          <strong><?= htmlspecialchars($row['reviewer_name']) ?> rated <?= $row['rating'] ?>/5</strong>
          <em>"<?= htmlspecialchars($row['comment']) ?>"</em>
          <small><?= $row['created_at'] ?></small>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No reviews yet.</p>
    <?php endif; ?>
  </div>

  <div class="listings">
    <h3>My Listings</h3>
    <?php if ($seller_products->num_rows > 0): ?>
      <?php while ($item = $seller_products->fetch_assoc()): ?>
        <div class="listing">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="Product">
          <div>
            <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
            <p>RM<?= htmlspecialchars($item['price']) ?></p>
            <p>Status: <?= htmlspecialchars($item['status']) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No items listed yet.</p>
    <?php endif; ?>
  </div>

  <div class="back">
    <a href="home.php">← Back to Home</a>
  </div>
</div>

</body>
</html>
