<?php
session_start();
require('connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$success = $error = "";

$sql = "SELECT email, phone, profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $username);

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$currentProfilePic = $user['profile_picture'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $newPassword = $_POST['password'];

    
    $profilePicPath = $currentProfilePic;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $profilePicPath = $targetDir . $fileName;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profilePicPath);
    }

   
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, password = ?, profile_picture = ? WHERE username = ?");
        $stmt->bind_param("sssss", $email, $phone, $hashedPassword, $profilePicPath, $username);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, profile_picture = ? WHERE username = ?");
        $stmt->bind_param("ssss", $email, $phone, $profilePicPath, $username);
    }

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['profile_picture'] = $profilePicPath;
    } else {
        $error = "Something went wrong. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      padding: 30px;
    }

    .form-box {
      max-width: 450px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
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

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .preview {
      text-align: center;
      margin-top: 15px;
    }

    .preview img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
    }

    button {
      width: 100%;
      background-color: #ee3e75;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      margin-top: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #d13568;
    }

    .message {
      margin-top: 10px;
      text-align: center;
    }

    .message.success { color: green; }
    .message.error { color: red; }

    .back {
      text-align: center;
      margin-top: 20px;
    }

    .back a {
      text-decoration: none;
      color: #ee3e75;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h2>Edit Profile</h2>

  <?php if ($success): ?>
    <p class="message success"><?= htmlspecialchars($success) ?></p>
  <?php elseif ($error): ?>
    <p class="message error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

    <label>New Password <small>(leave blank to keep current)</small></label>
    <input type="password" name="password">

    <label>Profile Picture</label>
    <input type="file" name="profile_picture" accept="image/*">

    <?php if (!empty($user['profile_picture'])): ?>
      <div class="preview">
        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Current Picture">
      </div>
    <?php endif; ?>

    <button type="submit">Update</button>
  </form>

  <div class="back">
    <a href="profile.php">← Back to Profile</a>
  </div>
</div>

</body>
</html>
