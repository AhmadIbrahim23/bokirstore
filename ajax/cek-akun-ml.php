<?php
require '../config.php';

// === Ambil user_id dari POST ===
$user_id = $_POST['user_id'] ?? '';

if (!$user_id) {
  echo json_encode([
    "status" => 0,
    "message" => "User ID kosong"
  ]);
  exit;
}

// === API Request ===
$url = "https://v1.apigames.id/merchant/$merchant_id_apiGames/cek-username/mobilelegend?user_id=$user_id&signature=$signature_apiGames";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);

//jalankan ini untuk melihat log respone
//file_put_contents("log-api-response.txt", $response);

$err = curl_error($ch);
curl_close($ch);

// === Jika gagal koneksi ===
if ($err) {
  echo json_encode([
    "status" => 0,
    "message" => "Curl error: $err"
  ]);
  exit;
}

// === Kirim hasil JSON ke frontend ===
header('Content-Type: application/json');
echo $response;