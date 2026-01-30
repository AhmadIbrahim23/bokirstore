<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: $web_url/login");
    exit;
}

$ref = $_GET['ref'] ?? '';
$kode_transaksi = decrypt($ref);

if (!$kode_transaksi) {
    $_SESSION['popup_error'] = "Kode transaksi tidak valid.";
    header("Location: $web_url/deposit");
    exit;
}

// Ambil data deposit
$stmt = $conn->prepare("SELECT id, user_id, metode, amount, biaya, total_transfer, status, payment_no, reference_id, trx_id, expired_at, json_invoice 
                        FROM deposit 
                        WHERE kode_transaksi = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("si", $kode_transaksi, $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['popup_error'] = "Transaksi tidak ditemukan.";
    header("Location: $web_url/deposit");
    exit;
}

$stmt->bind_result($deposit_id, $user_id, $metode, $amount, $biaya, $total_transfer, $status, $payment_no, $reference_id, $trx_id, $expired_at, $json_invoice);
$stmt->fetch();
$stmt->close();

// Ambil info metode
$stmt = $conn->prepare("SELECT nama, metode, guide, tambahan_biaya, tipe_tambahan FROM metode_pembayaran WHERE kode = ? LIMIT 1");
$stmt->bind_param("s", $metode);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nama_metode, $jenis_metode, $guide, $tambahan_biaya, $tipe_tambahan);
$stmt->fetch();
$stmt->close();

if ($tipe_tambahan == "Percent") {
    $tambahan_fee =  ($amount * $tambahan_biaya / 100);
} else {
    $tambahan_fee = $tambahan_biaya;
}

// Jika belum ada invoice, request ke API Sakurupiah
if (!$reference_id) {
    $stmt = $conn->prepare("SELECT nomor_hp FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nomor_hp);
    $stmt->fetch();
    $stmt->close();

    $api = $conn->query("SELECT api_id, api_key FROM payment_gateway WHERE kode = 'sakurupiah' LIMIT 1");
    [$api_id, $api_key] = $api->fetch_row();

    $signature = hash_hmac('sha256', $api_id . $metode . $kode_transaksi . (int)$total_transfer, $api_key);

    $data = [
        'api_id' => $api_id,
        'method' => $metode,
        'phone' => $nomor_hp ?: '-',
        'amount' => (int)$total_transfer,
        'merchant_fee' => 1,
        'merchant_ref' => $kode_transaksi,
        'expired' => 10,
        'callback_url' => "$web_url/deposit/callback.php?token=$secret",
        'return_url' => "$web_url/deposit/deposit-invoice.php?ref=" . urlencode($ref),
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://sakurupiah.id/api/create.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $api_key"]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result && $result['status'] === '200') {
        $d = $result['data'][0];
        $reference_id = $d['trx_id'];
        $payment_no   = $d['payment_no'] ?? '-';
        $trx_id       = $d['merchant_ref'] ?? '';
        $expired_at   = $d['expired'];
        $json_invoice = $response;
        $qr           = $d['qr'] ?? null;
        $via          = $d['via'] ?? $nama_metode;

        $up = $conn->prepare("UPDATE deposit 
                              SET reference_id = ?, payment_no = ?, trx_id = ?, expired_at = ?, json_invoice = ?, updated_at = NOW() 
                              WHERE id = ?");
        $up->bind_param("sssssi", $reference_id, $payment_no, $trx_id, $expired_at, $json_invoice, $deposit_id);
        $up->execute();
        $up->close();
    } else {
        $_SESSION['popup_error'] = "Gagal membuat invoice. Silakan coba lagi.";
        header("Location: $web_url/deposit");
        exit;
    }
} else {
    $kelola = json_decode($json_invoice, true);
    $qr     = $kelola['data'][0]['qr'] ?? null;
    $via    = $kelola['data'][0]['via'] ?? $nama_metode;
}
?>

<?php require '../lib/header.php'; ?>
<div class="section-container py-5">
  <div class="container" style="max-width: 600px;">
    <h3 class="text-center mb-4">Pembayaran Deposit</h3>

    <div class="card shadow-sm rounded p-4">
      <p><strong>ID Transaksi:</strong> <?= htmlspecialchars($kode_transaksi) ?></p>
      <p><strong>Metode:</strong> <?= htmlspecialchars($via ?? $nama_metode) ?></p>
      <p><strong>Nominal:</strong> Rp<?= number_format($amount, 0, ',', '.') ?></p>
      <p><strong>Biaya Admin:</strong> Rp<?= number_format($biaya + $tambahan_fee, 0, ',', '.') ?></p>
      <p><strong>Total Transfer:</strong> <span class="text-success fw-bold">Rp<?= number_format($total_transfer, 0, ',', '.') ?></span></p>
      <?php
        $statusColor = [
            'pending' => 'warning',
            'paid' => 'success',
            'expired' => 'danger',
            'failed' => 'secondary'
        ];
        $color = $statusColor[strtolower($status)] ?? 'dark';
        ?>
        
        <p><strong>Status:</strong> 
           <span class="badge bg-<?= $color ?> text-<?= $color === 'warning' ? 'dark' : 'white' ?>">
               <?= ucfirst($status) ?>
           </span>
        </p>

      <p><strong>Kadaluarsa:</strong> <?= $expired_at ? date("d/m/Y H:i", strtotime($expired_at)) : '-' ?></p>

      <hr>

      <!-- Tampilan Dinamis berdasarkan Jenis -->
      <?php if ($jenis_metode === 'QRIS'): ?>
        <div class="text-center">
          <p><strong>Scan QR untuk Pembayaran:</strong></p>
          <img src="<?= htmlspecialchars($qr ?? '') ?>" alt="QR Code" class="img-fluid rounded border" width="300">
        </div>

      <?php elseif ($jenis_metode === 'E-Wallet'): ?>
        <p><strong>Bayar melalui link berikut:</strong></p>
        <a href="<?= htmlspecialchars($payment_no) ?>" class="btn btn-primary" target="_blank">Bayar Sekarang</a>

      <?php elseif (in_array($jenis_metode, ['Virtual-Account', 'Convenience Store'])): ?>
        <p><strong>Kode Pembayaran:</strong></p>
        <div class="bg-light p-3 rounded text-center fs-5 fw-bold"><?= htmlspecialchars($payment_no ?? '-') ?></div>

      <?php else: ?>
        <div class="alert alert-danger">Jenis pembayaran tidak dikenali.</div>
      <?php endif; ?>

      <!-- Panduan Pembayaran -->
      <?php if (!empty($guide)): ?>
        <div class="accordion mt-4" id="accordionPanduan">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingPanduan">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePanduan" aria-expanded="false" aria-controls="collapsePanduan">
                Panduan Pembayaran
              </button>
            </h2>
            <div id="collapsePanduan" class="accordion-collapse collapse" aria-labelledby="headingPanduan" data-bs-parent="#accordionPanduan">
              <div class="accordion-body text-start">
                <?= nl2br(htmlspecialchars($guide)) ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="text-center mt-4">
        <a href="<?= $web_url ?>/dashboard" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
<?php require '../lib/footer.php'; ?>
