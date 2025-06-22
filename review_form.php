<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username']) || !isset($_GET['user'])) {
    header("Location: index.php");
    exit();
}

$reviewer_username = $_SESSION['username'];
$reviewed_username = $_GET['user'];

if ($reviewer_username === $reviewed_username) {
    exit("You cannot review yourself.");
}

$getUserId = function($username, $conn) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['id'] ?? null;
};

$reviewer_id = $getUserId($reviewer_username, $conn);
$reviewed_id = $getUserId($reviewed_username, $conn);

if (!$reviewed_id || !$reviewer_id) {
    exit("Invalid user.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $check = $conn->prepare("SELECT id FROM reviews WHERE reviewer_id = ? AND reviewed_id = ?");
    $check->bind_param("ii", $reviewer_id, $reviewed_id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        exit("You already reviewed this user.");
    }

    $stmt = $conn->prepare("INSERT INTO reviews (reviewer_id, reviewed_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $reviewer_id, $reviewed_id, $rating, $comment);
    if ($stmt->execute()) {
        header("Location: chat.php?user=" . urlencode($reviewed_username));
        exit();
    } else {
        exit("Failed to submit review.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Write Review</title>
  <style>
    body {
      font-family: Arial;
      background: #f7f7f7;
      padding: 30px;
    }

    .box {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input, textarea, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #ee3e75;
      color: white;
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-weight: bold;
    }

    button:hover {
      background-color: #d13568;
    }
  </style>
</head>
<body>

<div class="box">
  <h2>Write a Review for <?= htmlspecialchars($reviewed_username) ?></h2>
  <form method="POST">
    <label>Rating (1–5)</label>
    <select name="rating" required>
      <option value="">Select</option>
      <option value="5">5 - Excellent</option>
      <option value="4">4 - Good</option>
      <option value="3">3 - Fair</option>
      <option value="2">2 - Poor</option>
      <option value="1">1 - Bad</option>
    </select>

    <label>Review</label>
    <textarea name="comment" rows="4" required></textarea>

    <button type="submit">Submit Review</button>
  </form>
</div>

</body>
</html>
