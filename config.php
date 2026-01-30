<?php
date_default_timezone_set('Asia/Jakarta');


// ==============================================//
$host = "localhost";
$dbname = "bokirstore"; // ganti dengan nama database kamu
$username = "root"; // ganti sesuai username database
$password = ""; // ganti sesuai passowrd database
// ==============================================//

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$date = date("Y-m-d");
$time = date("H:i:s");

// ==============================================//
$web_url = "https://sakurupiah.my.id"; // ganti dengan domain kamu 
// (contoh : https://domain.com tanpa tanda /  )
// ==============================================//


// ==============================================//
// Konfigurasi API pastikan alamat IPV4 atau IPV6 hosting kamu sudah di daftarkan di api games untuk menu cek regional ML
//  https://apigames.id 

$merchant_id_apiGames = "M2311XXXXXXXXX"; // merchant id
$signature_apiGames   = "71490fcd018ebafaxxxxxx"; // signature
// ==============================================//


// ==============================================//
// 🔐 Token akses url
$secret = "topupxxxxxxxxxx";

// ==============================================//


$logo_web = $conn->query("SELECT * FROM web_logo ORDER BY id DESC");
$result_logo_web = $logo_web->fetch_assoc();


// Ambil data meta dari database
$meta_stmt = $conn->prepare("SELECT nama, title, deskripsi, keyword, whatsapp, email, facebook, instagram, youtube, twitter FROM meta_data LIMIT 1");
$meta_stmt->execute();
$meta_stmt->bind_result($meta_nama, $meta_title, $meta_desc, $meta_keyword, $meta_wa, $meta_email, $meta_fb, $meta_ig, $meta_yt, $meta_tw);
$meta_stmt->fetch();
$meta_stmt->close();


// ==============================================//
// Ganti dengan kunci rahasia kamu sendiri
define('SECRET_KEY', 'SAKXXXXXXX');
define('SECRET_IV', 'UFH98353XXXXXXXX');
// ==============================================//


// Fungsi enkripsi
function encrypt($data) {
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);

    // Tambahkan tanda tangan HMAC
    $hmac = hash_hmac('sha256', $encrypted, $key);
    $final = base64_encode($iv . '::' . $hmac . '::' . $encrypted);

    return urlencode($final);
}

// Fungsi dekripsi (lebih fleksibel, tidak dibatasi harus angka)
function decrypt($data) {
    $key = hash('sha256', SECRET_KEY);

    $decoded = base64_decode(urldecode($data));
    if (!$decoded || strpos($decoded, '::') === false) {
        return false;
    }

    list($iv, $hmac, $encrypted) = explode('::', $decoded, 3);

    // Validasi IV, HMAC, dan ciphertext
    if (strlen($iv) !== 16 || empty($hmac) || empty($encrypted)) {
        return false;
    }

    // Verifikasi tanda tangan HMAC
    $calculated_hmac = hash_hmac('sha256', $encrypted, $key);
    if (!hash_equals($hmac, $calculated_hmac)) {
        return false;
    }

    // Kembalikan hasil dekripsi (bisa string apapun)
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host     = $_SERVER['HTTP_HOST'];
    $uri      = $_SERVER['REQUEST_URI'];

    return $protocol . $host . $uri;
}
?>
