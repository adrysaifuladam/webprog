<!DOCTYPE html>
<html>
<head>
  <title>Register Admin - BELIBALIK</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container" style="justify-content: center;">
    <div class="login-box">
      <h2>Create Admin Account</h2>
      <form method="POST" action="save_admin.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register Admin</button>
      </form>
    </div>
  </div>
</body>
</html>
