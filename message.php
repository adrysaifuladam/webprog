<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to chat.";
    exit();
}

$sender = $_SESSION['username'];
$receiver = '';
$product_name = '';
$product_id = $_GET['product_id'] ?? null;

if ($product_id) {
    $stmt = $conn->prepare("SELECT name, seller FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    $product_name = $product['name'] ?? '';
    $receiver = $product['seller'] ?? '';

    if ($sender === $receiver) {
        $stmt = $conn->prepare("SELECT DISTINCT sender FROM messages WHERE product_id = ? AND receiver = ?");
        $stmt->bind_param("is", $product_id, $sender);
        $stmt->execute();
        $res = $stmt->get_result();
        $receiver = $res->fetch_assoc()['sender'] ?? '';
    }
} elseif (isset($_GET['with'])) {
    $receiver = $_GET['with'];
    $product_id = null;
} else {
    echo "Missing chat target.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (product_id, sender, receiver, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $product_id, $sender, $receiver, $message);
    $stmt->execute();
}

$stmt = $conn->prepare("
    SELECT sender, message, created_at 
    FROM messages 
    WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
    " . ($product_id ? "AND product_id = ?" : "") . "
    ORDER BY created_at ASC
");

if ($product_id) {
    $stmt->bind_param("ssssi", $sender, $receiver, $receiver, $sender, $product_id);
} else {
    $stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
}

$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Chat <?= $product_name ? "- " . htmlspecialchars($product_name) : "with $receiver" ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 30px;
      margin: 0;
    }
    .back-button {
      display: inline-block;
      margin-bottom: 20px;
      background-color: #888;
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
    }
    .back-button:hover {
      background-color: #555;
    }
    .chat-container {
      max-width: 600px;
      margin: auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      height: 85vh;
      overflow: hidden;
    }
    .chat-header {
      background-color: #ee3e75;
      color: white;
      padding: 15px;
      font-size: 18px;
      font-weight: bold;
      text-align: center;
    }
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background-color: #f7f7f7;
    }
    .chat-message {
      max-width: 70%;
      padding: 12px 16px;
      border-radius: 15px;
      margin-bottom: 12px;
      clear: both;
      position: relative;
      font-size: 14px;
    }
    .left {
      background-color: #eeeeee;
      float: left;
      border-bottom-left-radius: 0;
    }
    .right {
      background-color: #ee3e75;
      color: white;
      float: right;
      border-bottom-right-radius: 0;
    }
    .sender {
      font-size: 12px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .timestamp {
      font-size: 10px;
      color: #666;
      margin-top: 5px;
      text-align: right;
    }
    .chat-input {
      display: flex;
      border-top: 1px solid #ccc;
      padding: 15px;
      background: white;
    }
    .chat-input textarea {
      flex: 1;
      resize: none;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-right: 10px;
    }
    .chat-input button {
      background-color: #ee3e75;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
    }
    .chat-input button:hover {
      background-color: #d13568;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      font-weight: bold;
      color: #ee3e75;
    }
    .review-btn {
      display: block;
      margin: 20px auto;
      background-color: #576ce0;
      color: white;
      text-align: center;
      padding: 10px 15px;
      border-radius: 6px;
      width: 180px;
      text-decoration: none;
      font-weight: bold;
    }
    .review-btn:hover {
      background-color: #4054c4;
    }
  </style>
</head>
<body>

<a href="home.php" class="back-button">← Back to Home</a>

<div class="chat-container">
  <div class="chat-header">
    Chat <?= $product_name ? "About: " . htmlspecialchars($product_name) : "with: $receiver" ?>
  </div>

  <div class="chat-messages">
    <?php while ($row = $messages->fetch_assoc()): ?>
      <?php $align = ($row['sender'] === $sender) ? 'right' : 'left'; ?>
      <div class="chat-message <?= $align ?>">
        <div class="sender"><?= htmlspecialchars($row['sender']) ?></div>
        <?= nl2br(htmlspecialchars($row['message'])) ?>
        <div class="timestamp"><?= $row['created_at'] ?></div>
      </div>
    <?php endwhile; ?>
  </div>

  <form class="chat-input" method="POST">
    <textarea name="message" placeholder="Type a message..." rows="1" required></textarea>
    <button type="submit">Send</button>
  </form>
</div>

<?php if ($product_id): ?>
<a class="review-btn" href="review.php?user=<?= urlencode($receiver) ?>&product_id=<?= $product_id ?>">Leave a Review</a>
<a class="back-link" href="product.php?id=<?= $product_id ?>">← Back to Product</a>
<?php endif; ?>

</body>
</html>
