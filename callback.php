<?php
require 'config.php';

// Ambil raw JSON
$json = file_get_contents('php://input');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    exit("Akses ditolak.");
}

// Ambil signature & event
$callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
$callbackEvent     = $_SERVER['HTTP_X_CALLBACK_EVENT'] ?? '';
$data              = json_decode($json);

// Ambil API Key dari database
$pg = $conn->query("SELECT api_key FROM payment_gateway WHERE kode = 'sakurupiah' LIMIT 1");
$api_key = ($pg->num_rows > 0) ? $pg->fetch_assoc()['api_key'] : null;

// Validasi signature
$expectedSignature = hash_hmac('sha256', $json, $api_key);
if ($callbackSignature !== $expectedSignature) {
    http_response_code(200);
    exit(json_encode(['success' => false, 'message' => 'Invalid signature']));
}

// Validasi JSON
if (JSON_ERROR_NONE !== json_last_error() || !isset($data->merchant_ref, $data->status, $data->status_kode)) {
    http_response_code(200);
    exit(json_encode(['success' => false, 'message' => 'Invalid JSON or missing fields']));
}

// Pastikan event-nya payment_status
if ($callbackEvent !== 'payment_status') {
    http_response_code(200);
    exit(json_encode(['success' => false, 'message' => 'Unrecognized callback event']));
}

// Ambil data penting
$kode_transaksi = $data->merchant_ref;
$status_api     = strtolower($data->status);        // berhasil / expired / pending
$status_kode    = (int)$data->status_kode;          // 1 / 2 / 0
$trx_id         = $data->trx_id ?? null;
$callback_json  = json_encode($data);
$now            = date("Y-m-d H:i:s");

// Cek status sekarang di DB
$cek = $conn->prepare("SELECT status_pembayaran FROM pemesanan_ppob WHERE kode_transaksi = ? LIMIT 1");
$cek->bind_param("s", $kode_transaksi);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    http_response_code(200);
    exit(json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']));
}

$cek->bind_result($status_db);
$cek->fetch();
$cek->close();

// Jika sudah paid, tidak perlu update ulang
if ($status_db === 'paid') {
    exit(json_encode(['success' => true, 'message' => 'Transaksi sudah paid. Tidak perlu update ulang.']));
}

// Tentukan status_pembayaran final untuk disimpan ke DB
switch ($status_api) {
    case 'berhasil':
        $status_final = 'paid';
        $paid_at      = $now;
        break;
    case 'expired':
        $status_final = 'expired';
        $paid_at      = null;
        break;
    case 'pending':
    default:
        $status_final = 'pending';
        $paid_at      = null;
        break;
}

// Update data pembayaran
$update = $conn->prepare("
    UPDATE pemesanan_ppob 
    SET status_pembayaran = ?, paid_at = ?, ref_id = ?, callback_response = ?, updated_at = ?
    WHERE kode_transaksi = ? LIMIT 1
");
$update->bind_param("ssssss", $status_final, $paid_at, $trx_id, $callback_json, $now, $kode_transaksi);
$update->execute();
$update->close();

// TODO: Jika status_final == 'paid', kirim ke DigiFlazz di sini
if ($status_final === 'paid') {
    // Ambil API Key DigiFlazz
    $digiflazz = $conn->query("SELECT username, api_key FROM provider WHERE nama = 'digiflazz' AND status = 'active' LIMIT 1");
    if ($digiflazz->num_rows === 0) {
        exit(json_encode(['success' => false, 'message' => 'API DigiFlazz tidak tersedia']));
    }

    $api_conf = $digiflazz->fetch_assoc();
    $username = $api_conf['username'];
    $apiKey   = $api_conf['api_key'];

    // Ambil data pesanan
    $stmt = $conn->prepare("SELECT no_hp, produk_id FROM pemesanan_ppob WHERE kode_transaksi = ? LIMIT 1");
    $stmt->bind_param("s", $kode_transaksi);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        exit(json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']));
    }

    $stmt->bind_result($no_hp, $produk_id);
    $stmt->fetch();
    $stmt->close();
    
    // Ambil kode_produk dari layanan
    $stmt2 = $conn->prepare("SELECT kode_produk FROM layanan WHERE id = ? AND status = 'normal' LIMIT 1");
    $stmt2->bind_param("i", $produk_id);
    $stmt2->execute();
    $stmt2->store_result();
    
    if ($stmt2->num_rows === 0) {
        exit(json_encode(['success' => false, 'message' => 'Layanan tidak ditemukan atau tidak aktif']));
    }
    
    $stmt2->bind_result($kode_produk);
    $stmt2->fetch();
    $stmt2->close();


    // Kirim ke DigiFlazz
    $signature = md5($username . $apiKey . $kode_transaksi);
    $payload = [
        "username" => $username,
        "buyer_sku_code" => $kode_produk,
        "customer_no" => $no_hp,
        "ref_id" => $kode_transaksi,
        "sign" => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.digiflazz.com/v1/transaction");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek respons
    $digiflazzStatus = 'gagal';
    $statusPengiriman = 'gagal';

    if ($httpCode === 200 && $response) {
        $resData = json_decode($response, true);
        if (isset($resData['data']['status']) && strtolower($resData['data']['status']) === 'pending') {
            $digiflazzStatus = 'proses';
            $statusPengiriman = 'proses';
        } elseif (isset($resData['data']['status']) && strtolower($resData['data']['status']) === 'sukses') {
            $digiflazzStatus = 'sukses';
            $statusPengiriman = 'berhasil';
        }
    }

    // Simpan respon DigiFlazz
    $stmt = $conn->prepare("UPDATE pemesanan_ppob SET digiflazz_response = ?, status_pengiriman = ?, status_digiflazz = ? WHERE kode_transaksi = ? LIMIT 1");
    $stmt->bind_param("ssss", $response, $statusPengiriman, $digiflazzStatus, $kode_transaksi);
    $stmt->execute();
    $stmt->close();
}


exit(json_encode([
    'success' => true,
    'message' => "Callback processed, status: $status_final"
]));
