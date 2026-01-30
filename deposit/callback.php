<?php
require '../config.php';

// Ambil raw JSON dari body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    exit("Akses ditolak.");
}


// Header dari Sakurupiah
$header_signature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
$event            = $_SERVER['HTTP_X_CALLBACK_EVENT'] ?? '';

// Validasi dasar
if (!$data || !$header_signature) {
    http_response_code(200);
    echo json_encode(['status' => false, 'message' => 'Bad Request']);
    exit;
}

// Ambil API Key dari database
$pg_stmt = $conn->prepare("SELECT api_key FROM payment_gateway WHERE kode = 'sakurupiah' LIMIT 1");
$pg_stmt->execute();
$pg_stmt->store_result();
$pg_stmt->bind_result($api_key);
$pg_stmt->fetch();
$pg_stmt->close();

if (empty($api_key)) {
    http_response_code(200);
    echo json_encode(['status' => false, 'message' => 'API Key not configured']);
    exit;
}

// Validasi Signature
$local_signature = hash_hmac('sha256', $json, $api_key);
if (!hash_equals($header_signature, $local_signature)) {
    http_response_code(200);
    echo json_encode(['status' => false, 'message' => 'Invalid Signature']);
    exit;
}

// Lanjutkan hanya jika event = payment_status
if ($event !== 'payment_status') {
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Event ignored']);
    exit;
}

// Ambil data callback
$ref_id        = $data['trx_id'] ?? null;
$merchant_ref  = $data['merchant_ref'] ?? null;
$status_api    = strtolower($data['status'] ?? '');

if (!$merchant_ref || !$ref_id || !$status_api) {
    http_response_code(200);
    echo json_encode(['status' => false, 'message' => 'Incomplete data']);
    exit;
}

// Cek apakah transaksi deposit ada
$stmt = $conn->prepare("SELECT id, user_id, status, amount FROM deposit WHERE kode_transaksi = ? LIMIT 1");
$stmt->bind_param("s", $merchant_ref);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(200);
    echo json_encode(['status' => false, 'message' => 'Transaction not found']);
    exit;
}

$stmt->bind_result($deposit_id, $user_id, $current_status, $amount);
$stmt->fetch();
$stmt->close();

// Jika sudah processed, hentikan
if (in_array($current_status, ['paid', 'expired'])) {
    http_response_code(200);
    echo json_encode(['status' => true, 'message' => 'Already processed']);
    exit;
}

// Mapping status Sakurupiah ke internal
switch ($status_api) {
    case 'berhasil':
    case 'paid':
        $status_final = 'paid';
        $paid_at = date('Y-m-d H:i:s');
        break;
    case 'expired':
        $status_final = 'expired';
        $paid_at = null;
        break;
    case 'pending':
    default:
        $status_final = 'pending';
        $paid_at = null;
        break;
}

// Simpan response callback
$callback_json = json_encode($data);

// Update status di tabel deposit
$update = $conn->prepare("UPDATE deposit 
    SET status = ?, reference_id = ?, callback_response = ?, updated_at = NOW(), paid_at = ?
    WHERE id = ?");
$update->bind_param("ssssi", $status_final, $ref_id, $callback_json, $paid_at, $deposit_id);
$update->execute();
$update->close();

// Jika paid, tambahkan saldo user
if ($status_final === 'paid') {
    $add = $conn->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
    $add->bind_param("di", $amount, $user_id);
    $add->execute();
    $add->close();
}

http_response_code(200);
echo json_encode(['status' => true, 'message' => 'Callback processed']);
