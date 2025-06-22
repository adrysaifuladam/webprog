<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$current_user = $_SESSION['username'];

$stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE username != ?");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Chat with Users</title>
  <style>
    body { font-family: Arial; padding: 30px; background: #f7f7f7; }
    .user-list { max-width: 600px; margin: auto; }
    .user-box {
      display: flex; align-items: center;
      background: white; padding: 12px 16px;
      margin-bottom: 10px; border-radius: 10px;
      box-shadow: 0 0 6px rgba(0,0,0,0.1);
    }
    .user-box img {
      width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; object-fit: cover;
    }
    .user-box a {
      text-decoration: none; color: #333; font-weight: bold;
    }
    .back-button {
      display: block;
      width: fit-content;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #888;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .back-button:hover {
      background-color: #555;
    }
  </style>
</head>
<body>

<h2 style="text-align: center;">Start a Chat</h2>

<a href="home.php" class="back-button">← Back to Home</a>

<div class="user-list">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="user-box">
      <img src="<?= htmlspecialchars($row['profile_picture'] ?? 'gambar.png') ?>" alt="Profile">
      <a href="general_chat.php?user=<?= urlencode($row['username']) ?>">
        <?= htmlspecialchars($row['username']) ?>
      </a>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
