<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username']) || !isset($_GET['user'])) {
    echo "Unauthorized access.";
    exit();
}

$reviewer_username = $_SESSION['username'];
$reviewed_username = $_GET['user'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $reviewer_username);
$stmt->execute();
$reviewer_result = $stmt->get_result();
$reviewer = $reviewer_result->fetch_assoc();
$reviewer_id = $reviewer['id'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $reviewed_username);
$stmt->execute();
$reviewed_result = $stmt->get_result();
$reviewed = $reviewed_result->fetch_assoc();
$reviewed_id = $reviewed['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = trim($_POST['comment'] ?? '');

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO reviews (reviewer_id, reviewed_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $reviewer_id, $reviewed_id, $rating, $comment);
        if ($stmt->execute()) {
            header("Location: home.php");
            exit();
        } else {
            die("Error saving review: " . $stmt->error);
        }
    } else {
        die("Invalid rating or comment.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave a Review</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 30px;
    }
    .review-box {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      background-color: #ee3e75;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #d13568;
    }
    .back-link {
      text-align: center;
      margin-top: 15px;
    }
    .back-link a {
      color: #ee3e75;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="review-box">
    <h2>Leave a Review for <?= htmlspecialchars($reviewed_username) ?></h2>
    <form method="POST">
      <label for="rating">Rating:</label>
      <select name="rating" id="rating" required>
        <option value="">Select</option>
        <option value="5">★★★★★</option>
        <option value="4">★★★★☆</option>
        <option value="3">★★★☆☆</option>
        <option value="2">★★☆☆☆</option>
        <option value="1">★☆☆☆☆</option>
      </select>

      <label for="comment">Comment:</label>
      <textarea name="comment" id="comment" rows="4" required placeholder="Write your review here..."></textarea>

      <button type="submit">Submit Review</button>
    </form>
    <div class="back-link">
      <a href="home.php">← Back to Home</a>
    </div>
  </div>
</body>
</html>
