<?php
require 'config.php';

// Hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

// Cek token keamanan
if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    exit("Akses ditolak.");
}

// Ambil dan validasi JSON
$json = file_get_contents("php://input");
$data = json_decode($json, true);


// Simpan log isi data callback
//file_put_contents(__DIR__ . '/log-callback-digiflazz.log',
//    "=== " . date("Y-m-d H:i:s") . " ===\n" .
//    "RAW JSON:\n" . $json . "\n\n" .
 //   "DECODED:\n" . print_r($data, true) . "\n\n",
 //   FILE_APPEND);

$sn_token = $data['data']['sn'] ?? null;


if (!$data || !isset($data['data']['ref_id'], $data['data']['status'])) {
    http_response_code(400);
    exit("Invalid payload");
}

$ref_id = $data['data']['ref_id'];
$status = strtolower(trim($data['data']['status']));


$digiflazz_response = json_encode($data, JSON_UNESCAPED_UNICODE);

// Cek apakah transaksi ada
$stmt = $conn->prepare("SELECT id, user_id, harga_total, metode_pembayaran, status_pembayaran FROM pemesanan_ppob WHERE kode_transaksi = ? LIMIT 1");
$stmt->bind_param("s", $ref_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    exit("Transaksi tidak ditemukan");
}

$stmt->bind_result($order_id, $user_id, $harga_total, $metode_pembayaran, $status_pembayaran);
$stmt->fetch();
$stmt->close();

// Status mapping ke sistem
switch ($status) {
    case 'sukses':
        $status_pengiriman = 'berhasil';
        $status_digiflazz = 'sukses';
        break;
    case 'gagal':
        $status_pengiriman = 'gagal';
        $status_digiflazz = 'gagal';
        break;
    case 'pending':
    default:
        $status_pengiriman = 'proses';
        $status_digiflazz = 'proses';
        break;
}

// Update ke tabel transaksi
$update = $conn->prepare("UPDATE pemesanan_ppob SET digiflazz_response = ?, status_pengiriman = ?, status_digiflazz = ?, updated_at = NOW() WHERE kode_transaksi = ?");
$update->bind_param("ssss", $digiflazz_response, $status_pengiriman, $status_digiflazz, $ref_id);
$update->execute();
$update->close();

// Refund saldo jika pakai saldo dan transaksi gagal
if ($status_digiflazz === 'gagal' && $metode_pembayaran === 'SALDO' && $status_pembayaran === 'paid' && !empty($user_id)) {
    // Kembalikan saldo
    $refund = $conn->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
    $refund->bind_param("di", $harga_total, $user_id);
    $refund->execute();
    $refund->close();

    // Catat ke log riwayat
    $keterangan = "Refund otomatis karena transaksi gagal (Ref: $ref_id)";
    $catat = $conn->prepare("INSERT INTO riwayat_saldo (user_id, tipe, jumlah, keterangan, created_at) VALUES (?, 'refund', ?, ?, NOW())");
    $catat->bind_param("ids", $user_id, $harga_total, $keterangan);
    $catat->execute();
    $catat->close();
}

echo "OK";
