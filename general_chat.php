<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username']) || !isset($_GET['user'])) {
    echo "Access denied.";
    exit();
}

$current_user = $_SESSION['username'];
$chat_with = $_GET['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $current_user, $chat_with, $msg);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT sender, message, created_at FROM messages 
                        WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
                        ORDER BY created_at ASC");
$stmt->bind_param("ssss", $current_user, $chat_with, $chat_with, $current_user);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Chat with <?= htmlspecialchars($chat_with) ?></title>
  <style>
    body { font-family: Arial; padding: 30px; background: #f7f7f7; }
    .chat-box {
      max-width: 600px; margin: auto;
      background: white; border-radius: 10px;
      padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .msg { margin-bottom: 15px; }
    .msg.you { text-align: right; }
    .msg .bubble {
      display: inline-block; padding: 10px 15px;
      border-radius: 10px; max-width: 70%;
    }
    .msg.you .bubble {
      background: #ee3e75; color: white;
      border-bottom-right-radius: 0;
    }
    .msg.other .bubble {
      background: #e0e0e0;
      border-bottom-left-radius: 0;
    }
    form { display: flex; margin-top: 20px; }
    textarea {
      flex: 1; padding: 10px;
      border-radius: 8px; border: 1px solid #ccc;
      resize: none;
    }
    button {
      margin-left: 10px;
      background: #ee3e75; color: white;
      padding: 10px 20px; border: none; border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="chat-box">
  <h3>Chat with <?= htmlspecialchars($chat_with) ?></h3>
  <?php while ($row = $messages->fetch_assoc()): ?>
    <div class="msg <?= $row['sender'] === $current_user ? 'you' : 'other' ?>">
      <div class="bubble">
        <?= nl2br(htmlspecialchars($row['message'])) ?><br>
        <small><?= $row['created_at'] ?></small>
      </div>
    </div>
  <?php endwhile; ?>

  <form method="POST">
    <textarea name="message" placeholder="Type your message..." required></textarea>
    <button type="submit">Send</button>
  </form>
</div>

</body>
</html>
