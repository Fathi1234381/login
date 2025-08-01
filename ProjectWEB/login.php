<?php
session_start(); // Untuk simpan data login sementara
$errors = [];

// Proses login saat form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $errors[] = "Semua field wajib diisi.";
    } else {
        $userFile = "data/users.txt";
        $found = false;

        if (file_exists($userFile)) {
            $lines = file($userFile, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                [$storedUser, $storedHashedPassword] = explode("|", $line);

                if ($storedUser === $username && password_verify($password, $storedHashedPassword)) {
                    $found = true;
                    $_SESSION["username"] = $username;
                    header("Location: kalibrasi.php");
                    exit;
                }
            }
        }

        if (!$found) {
            $errors[] = "Email atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="css/style-login.css" />
</head>
<body class="login-page">
  <div class="background"></div>

  <div class="container">
    <h2>Login</h2>

    <?php if ($errors): ?>
      <div style="color: red; text-align: left;">
        <?php foreach ($errors as $err): ?>
          <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="index.php">
      <input type="text" name="username" placeholder="Email/username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>

    <a href="register.php">Don't have an account? Register</a>
  </div>
</body>
</html>
