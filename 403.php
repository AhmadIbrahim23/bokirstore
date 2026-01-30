<?php
session_start();
require 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 - Halaman Tidak Ditemukan</title>
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      color: #333;
    }

    .container {
      text-align: center;
    }

    h1 {
      font-size: 6rem;
      margin-bottom: 0.5rem;
      color: #dc3545;
    }

    h2 {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
    }

    a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #0d6efd;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    a:hover {
      background-color: #084298;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>403</h1>
    <h2>Halaman yang kamu cari tidak ditemukan.</h2>
    <a href="<?php echo $web_url;?>">Kembali ke Beranda</a>
  </div>
</body>
</html>
