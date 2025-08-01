<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kalibrasi</title>
  <link rel="stylesheet" href="css/style-kalibrasi.css" />
</head>
<body class="kalibrasi-page">
  <div class="background"></div>

  <div class="container-blur">
    <h2>Selamat datang, <?= $username ?>!</h2>
    <p>Ini adalah halaman Kalibrasi. Silakan tambahkan fitur-fitur seperti input volt, ampere, kWh, dll.</p>

    <a href="logout.php" class="logout-button">Logout</a>
  </div>
</body>
</html>
