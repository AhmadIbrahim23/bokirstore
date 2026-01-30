<?php
session_start();
require 'config.php';

$data = null;
$error = null;

// STEP 1: Jika ada POST dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_transaksi'])) {
  $kode = trim($_POST['kode_transaksi']);

  if ($kode) {
    $_SESSION['kode_transaksi_cari'] = $kode;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  } else {
    $_SESSION['error_transaksi'] = "Harap masukkan kode transaksi.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}

// STEP 2: Jika redirect setelah POST
if (isset($_SESSION['kode_transaksi_cari'])) {
  $kode = $_SESSION['kode_transaksi_cari'];
  unset($_SESSION['kode_transaksi_cari']);

  $stmt = $conn->prepare("SELECT 
    kode_transaksi, no_hp, produk_id, metode_pembayaran, harga_produk, biaya_admin, harga_total,
    kontak, tipe_produk, status_pembayaran, status_pengiriman, created_at, payment_gateway,
    kode_pembayaran, ref_id, expired_at, json_invoice, callback_response, digiflazz_response
    FROM pemesanan_ppob WHERE kode_transaksi = ? LIMIT 1");

  $stmt->bind_param("s", $kode);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result(
      $kode_transaksi, $no_hp, $produk_id, $metode_pembayaran, $harga_produk,
      $biaya_admin, $harga_total, $kontak, $tipe_produk, $status_pembayaran,
      $status_pengiriman, $created_at, $payment_gateway, $kode_pembayaran,
      $ref_id, $expired_at, $json_invoice, $callback_response, $digiflazz_response
    );
    $stmt->fetch();

    $data = [
      'kode_transaksi' => $kode_transaksi,
      'no_hp' => $no_hp,
      'produk_id' => $produk_id,
      'metode_pembayaran' => $metode_pembayaran,
      'harga_produk' => $harga_produk,
      'biaya_admin' => $biaya_admin,
      'harga_total' => $harga_total,
      'kontak' => $kontak,
      'tipe_produk' => $tipe_produk,
      'status_pembayaran' => $status_pembayaran,
      'status_pengiriman' => $status_pengiriman,
      'created_at' => $created_at,
      'payment_gateway' => $payment_gateway,
      'kode_pembayaran' => $kode_pembayaran,
      'ref_id' => $ref_id,
      'expired_at' => $expired_at,
      'json_invoice' => $json_invoice,
      'digiflazz_response' => $digiflazz_response
    ];
  } else {
    $error = "Transaksi tidak ditemukan.";
  }

  $stmt->close();
  
  $metode_stmt = $conn->prepare("SELECT metode, guide FROM metode_pembayaran WHERE kode = ? LIMIT 1");
    $metode_stmt->bind_param("s", $metode_pembayaran);
    $metode_stmt->execute();
    $metode_stmt->store_result();

    $jenis_metode = '';
    $guide = 'Panduan belum tersedia.';

    if ($metode_stmt->num_rows > 0) {
      $metode_stmt->bind_result($jenis_metode, $guide);
      $metode_stmt->fetch();
    }
    $metode_stmt->close();

    // Masukkan ke array $data biar bisa dipakai di tampilan
    $data['jenis_metode'] = $jenis_metode;
    $data['guide'] = $guide;
    
}

// STEP 3: Ambil pesan error jika ada
if (isset($_SESSION['error_transaksi'])) {
  $error = $_SESSION['error_transaksi'];
  unset($_SESSION['error_transaksi']);
}

require 'lib/header.php';
?>
<style>
    .custom-invoice {
    background: linear-gradient(145deg, #ad2b70, #080104);
    border: 2px solid #311d21;
    border-radius: 12px;
    box-shadow: 0 0 20px rgb(97 60 76 / 20%);
    padding: 1rem;
  overflow: hidden;
}

.custom-invoice-header {
  background: linear-gradient(135deg, #6f42c1, #3f0e75);
  color: white;
  text-align: center;
}

.custom-invoice-body p {
  margin-bottom: 0.75rem;
}

.custom-invoice .badge {
  font-size: 0.9rem;
  padding: 0.5em 0.8em;
  border-radius: 0.5rem;
}

@media (max-width: 576px) {
  .custom-invoice-body p {
    font-size: 0.95rem;
  }
}
.payment-code-box {
  font-size: 1.2rem;
  letter-spacing: 1px;
}

</style>
<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="fw-bold">Cari Detail Transaksi</h3>
      <p class="text-muted">Lacak transaksi kamu dengan cara memasukkan ID Transaksi dibawah ini:.</p>
    </div>
    
<form method="POST">
  <div class="row justify-content-center mb-4">
    <div class="col-md-6">
      <input type="text" name="kode_transaksi" class="form-control mb-2" placeholder="Contoh: INV175XXXX" required>
      <button type="submit" class="btn btn-custom-primary w-100">Cek Transaksi</button>
    </div>
  </div>
</form>

<?php if ($error): ?>
  <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
<?php elseif ($data): ?>
  <div class="custom-invoice card shadow rounded-4">
    <div class="custom-invoice-header bg-gradient text-white p-4 rounded-top-4">
      <h2 class="mb-0 text-white">INVOICE</h2>
    </div>
    <div class="custom-invoice-body card-body p-4">
      <?php
        $digiflazz = json_decode($data['digiflazz_response'] ?? '{}', true);
        $df = $digiflazz['data'] ?? null;

        if ($df && isset($df['status'])) {
          $status = strtolower($df['status']);
          if ($status === 'sukses') {
            echo '<div class="alert alert-success">✅ Transaksi berhasil diproses.</div>';
          } elseif ($status === 'gagal') {
            echo '<div class="alert alert-danger">❌ Transaksi gagal: ' . htmlspecialchars($df['message'] ?? '-') . '</div>';
          }
        }
      ?>

      <div class="row gy-3">
        <div class="col-12 col-md-6 text-white">
          <p><strong>ID Transaksi:</strong><br><?= htmlspecialchars($data['kode_transaksi']) ?></p>
          <p><strong>No HP:</strong><br><?= htmlspecialchars($data['no_hp']) ?></p>
          <p><strong>Produk:</strong><br><?= htmlspecialchars($data['tipe_produk']) ?> - <?= htmlspecialchars($data['produk_id']) ?></p>
          <p><strong>Tanggal:</strong><br><?= htmlspecialchars($data['created_at']) ?></p>
          <p><strong>Status Pembayaran:</strong><br>
            <span class="badge bg-<?= $data['status_pembayaran'] === 'paid' ? 'success' : 'warning' ?>">
              <?= ucfirst($data['status_pembayaran']) ?>
            </span>
          </p>
        </div>
        <div class="col-12 col-md-6 text-white">
          <p><strong>Harga Produk:</strong><br>Rp <?= number_format($data['harga_produk'], 0, ',', '.') ?></p>
          <p><strong>Fee Admin:</strong><br>Rp <?= number_format($data['biaya_admin'], 0, ',', '.') ?></p>
          <p><strong>Total Bayar:</strong><br><strong class="text-warning">Rp <?= number_format($data['harga_total'], 0, ',', '.') ?></strong></p>
          <p><strong>Status Pemesanan:</strong><br>
            <span class="badge bg-<?= $data['status_pengiriman'] === 'berhasil' ? 'success' : ($data['status_pengiriman'] === 'gagal' ? 'danger' : 'secondary') ?>">
              <?= ucfirst($data['status_pengiriman']) ?>
            </span>
          </p>
        </div>
      </div>

      <?php
          // Ambil data JSON invoice dari kolom
          $json = json_decode($data['json_invoice'] ?? '{}', true);
        
          // Coba ambil dari struktur API Sakurupiah
          $json_data = $json['data'][0] ?? [];
        
          // Ambil data QR, payment_no, dan fallback jika kosong
          $qr         = $json_data['qr'] ?? null;
          $payment_no = $json_data['payment_no'] ?? null;
        
          // Ambil metode dari tabel metode_pembayaran yang sudah dimasukkan ke $data sebelumnya
          $jenis_metode = strtolower($data['jenis_metode'] ?? $json_data['via'] ?? '');
        
          // Ambil guide dari DB atau fallback default
          $guide = $data['guide'] ?? $json_data['guide'] ?? 'Panduan belum tersedia.';
        ?>
        
        <div class="qr-box text-center mt-4">
          <?php if ($jenis_metode === 'qris' || $jenis_metode === 'qris2'): ?>
            <p><strong>Scan QR untuk Pembayaran:</strong></p>
            <img src="<?= htmlspecialchars($qr) ?>" alt="QR Code" class="img-fluid rounded border shadow-sm" style="max-width: 200px;"><br>
        
          <?php elseif ($jenis_metode === 'e-wallet' && $payment_no): ?>
            <a href="<?= htmlspecialchars($payment_no) ?>" class="btn btn-custom-primary mt-3" target="_blank">Bayar Sekarang</a>
        
          <?php elseif ($jenis_metode === 'virtual-account' || $jenis_metode === 'convenience store'): ?>
            <p><strong>Kode Pembayaran Anda:</strong></p>
            <div class="payment-code-box bg-light p-3 rounded text-center mx-auto" style="max-width: 300px;">
              <strong><?= htmlspecialchars($payment_no ?? '-') ?></strong>
            </div>
        
          <?php else: ?>
            <div class="alert alert-warning">Metode pembayaran tidak dikenali atau data belum tersedia.</div>
          <?php endif; ?>
        
          <?php if (!empty($guide)): ?>
            <!-- Accordion Panduan -->
            <div class="accordion mt-3" id="accordionPanduanQRIS">
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingPanduanQR">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePanduanQR" aria-expanded="false" aria-controls="collapsePanduanQR">
                    Panduan Pembayaran
                  </button>
                </h2>
                <div id="collapsePanduanQR" class="accordion-collapse collapse" aria-labelledby="headingPanduanQR" data-bs-parent="#accordionPanduanQRIS">
                  <div class="accordion-body text-start">
                    <?= nl2br(htmlspecialchars($guide)) ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>


      <?php if ($df): ?>
        <div class="mt-4 border-top pt-3">
          <h4 class="mb-4 text-white text-center">Informasi Pengiriman</h4>
          <div class="row gy-3">
            <div class="col-12 col-md-6 text-white">
              <p><strong>Ref ID:</strong><br><?= htmlspecialchars($df['ref_id'] ?? '-') ?></p>
              <p><strong>No Tujuan:</strong><br><?= htmlspecialchars($df['customer_no'] ?? '-') ?></p>
              <p><strong>Status:</strong><br>
                <span class="badge bg-<?= strtolower($df['status'] ?? '') === 'sukses' ? 'success' : (strtolower($df['status'] ?? '') === 'gagal' ? 'danger' : 'secondary') ?>">
                  <?= ucfirst($df['status'] ?? '-') ?>
                </span>
              </p>
            </div>
            <div class="col-12 col-md-6 text-white">
              <p><strong>Respon:</strong><br><?= htmlspecialchars($df['message'] ?? '-') ?></p>
              <p><strong>Kode RC:</strong><br><?= htmlspecialchars($df['rc'] ?? '-') ?></p>
              <?php if (!empty($df['sn'])): ?>
                <p><strong>Token / SN:</strong><br>
                  <span class="text-monospace fs-5"><?= htmlspecialchars($df['sn']) ?></span>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php elseif (!empty($data['digiflazz_response'])): ?>
        <div class="mt-4 border-top pt-3">
          <div class="alert alert-warning">📦 Data pengiriman tidak dapat ditampilkan. Mohon hubungi admin jika ini terjadi berulang.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
</div>
</div>


<?php require 'lib/footer.php'; ?>