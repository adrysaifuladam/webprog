<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BELIBALIK Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <div class="logo-section">
      <img src="belibalik.jpg" alt="BELIBALIK Logo" class="logo" />
    </div>

    <div class="login-box">
      <h2>Welcome</h2>
      <p>Log in to your account</p>

      <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />

        <div class="options">
          <label><input type="checkbox" name="is_admin"> Log in as Admin</label>
        </div>

        <button type="submit">Continue</button>
      </form>

      <div class="footer-links">
        <p>Don't have an account? <a href="register.html">Sign up</a></p>
      </div>
    </div>
  </div>
</body>
</html>
