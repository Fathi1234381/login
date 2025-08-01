<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "login_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars($_POST["fullname"]);
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $confirm = htmlspecialchars($_POST["confirm_password"]);

    if ($password !== $confirm) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (fullname, username, password)
                VALUES ('$fullname', '$username', '$hash')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registrasi berhasil! Silakan login'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="background">
    <div class="login-box">
      <h2>Register</h2>
      <form method="post" action="">
        <input type="text" name="fullname" placeholder="Full Name" required />
        <input type="text" name="username" placeholder="Email/Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        <button type="submit">Register</button>
        <p class="register-link">
          Already have an account? <a href="index.php">Login</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
