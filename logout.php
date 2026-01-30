<?php
session_start();
require 'config.php'; // Ini penting untuk mendapatkan $web_url

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: $web_url/login");
exit;
?>
