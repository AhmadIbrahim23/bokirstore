<?php
session_start();
require "../config.php";


if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    exit("Akses ditolak.");
}

// Ambil akun Sakurupiah
$q = $conn->query("SELECT * FROM payment_gateway WHERE kode='sakurupiah' LIMIT 1");
if (!$q || $q->num_rows == 0) {
    exit("API Key tidak ditemukan.");
}
$row = $q->fetch_assoc();
$api_id = $row['api_id'];
$api_key = $row['api_key'];

// Request ke API Sakurupiah
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://sakurupiah.id/api/list-payment.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array('api_id' => $api_id, 'method' => 'list'),
    CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $api_key)
));

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);
if (!isset($data['data']) || !is_array($data['data'])) {
    exit("Gagal mengambil data dari API.");
}

$count = 0;
foreach ($data['data'] as $m) {
    $kode = $conn->real_escape_string($m['kode']);
    $nama = $conn->real_escape_string($m['nama']);
    $minimal = floatval($m['minimal']);
    $maksimal = floatval($m['maksimal']);
    $biaya = $conn->real_escape_string($m['biaya']);
    $percent_biaya = $conn->real_escape_string($m['percent']);
    $tambahan_biaya = isset($m['addition']['tambahan_biaya']) ? $conn->real_escape_string($m['addition']['tambahan_biaya']) : '0';
    $tipe_tambahan = isset($m['addition']['jenis']) ? $conn->real_escape_string($m['addition']['jenis']) : 'Nominal';
    $status = $conn->real_escape_string($m['status']);
    $metode = isset($m['metode']) ? $conn->real_escape_string($m['metode']) : '';
    $logo = $conn->real_escape_string($m['logo']);
    $guide = isset($m['guide']['payment_guide']) ? $conn->real_escape_string($m['guide']['payment_guide']) : '';

    // Cek apakah sudah ada kode tersebut
    $cek = $conn->query("SELECT id FROM metode_pembayaran WHERE kode='$kode' LIMIT 1");
    if ($cek->num_rows > 0) {
        // Update
        $conn->query("UPDATE metode_pembayaran SET 
            nama='$nama', 
            minimal=$minimal, 
            maksimal=$maksimal, 
            biaya='$biaya', 
            percent_biaya='$percent_biaya', 
            tambahan_biaya='$tambahan_biaya', 
            tipe_tambahan='$tipe_tambahan', 
            status='$status', 
            metode='$metode', 
            logo='$logo', 
            guide='$guide' 
            WHERE kode='$kode'");
    } else {
        // Insert
        $conn->query("INSERT INTO metode_pembayaran 
            (kode, nama, minimal, maksimal, biaya, percent_biaya, tambahan_biaya, tipe_tambahan, status, metode, logo, guide) VALUES (
            '$kode', '$nama', $minimal, $maksimal, '$biaya', '$percent_biaya', '$tambahan_biaya', '$tipe_tambahan', '$status', '$metode', '$logo', '$guide'
        )");
    }

    $count++;
}

echo "Berhasil sinkronisasi $count metode pembayaran.";
