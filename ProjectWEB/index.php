<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="background">
    <div class="login-box">
      <h2>Login</h2>
      <form action="login.php" method="post">
        <input type="text" name="username" placeholder="Email/username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        <p class="register-link">
          Don’t have an account? <a href="register.html">Register</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
