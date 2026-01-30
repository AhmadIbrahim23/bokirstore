<?php
session_start();
require '../config.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: $web_url/login");
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount  = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $kode    = $_POST['metode'] ?? '';

    if ($amount <= 0 || !$kode) {
        $_SESSION['popup_error'] = "Form tidak lengkap.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Ambil metode pembayaran
    $stmt = $conn->prepare("SELECT kode, minimal, maksimal, biaya, percent_biaya, tambahan_biaya, tipe_tambahan 
                            FROM metode_pembayaran 
                            WHERE kode = ? AND status = 'Aktif' LIMIT 1");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION['popup_error'] = "Metode pembayaran tidak ditemukan.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt->bind_result($kode_metode, $min, $max, $biaya_raw, $tipe_biaya, $tambahan_raw, $tipe_tambahan);
    $stmt->fetch();
    $stmt->close();

    if ($amount < $min || $amount > $max) {
        $_SESSION['popup_error'] = "Nominal harus antara Rp" . number_format($min, 0, ',', '.') . " - Rp" . number_format($max, 0, ',', '.');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Hitung biaya
    $biaya    = ($tipe_biaya === 'Percent') ? $amount * (floatval($biaya_raw) / 100) : floatval($biaya_raw);
    $tambahan = ($tipe_tambahan === 'Percent') ? $amount * (floatval($tambahan_raw) / 100) : floatval($tambahan_raw);
    $total_transfer = $amount + $biaya + $tambahan;

    // Simpan deposit
    $kode_transaksi = 'DP' . time() . rand(100, 999);
    $payment_gateway = 'sakurupiah';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $insert = $conn->prepare("INSERT INTO deposit (
        user_id, kode_transaksi, metode, payment_gateway, amount, biaya, total_transfer,
        status, ip_address, user_agent
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
    $insert->bind_param(
        "isssddsss",
        $user_id,
        $kode_transaksi,
        $kode_metode,
        $payment_gateway,
        $amount,
        $biaya,
        $total_transfer,
        $ip_address,
        $user_agent
    );
    $insert->execute();
    $insert->close();

    // Redirect ke invoice
    $encrypted = encrypt($kode_transaksi);
    header("Location: $web_url/deposit/deposit-invoice.php?ref=$encrypted");
    exit;
}

// Ambil metode pembayaran
$stmt = $conn->prepare("SELECT kode, nama, minimal, maksimal, logo FROM metode_pembayaran WHERE status = 'Aktif' ORDER BY nama ASC");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($kode, $nama, $minimal, $maksimal, $logo);

$metodeList = [];
while ($stmt->fetch()) {
    $metodeList[] = [
        'kode' => $kode,
        'nama' => $nama,
        'minimal' => $minimal,
        'maksimal' => $maksimal,
        'logo' => $logo
    ];
}
$stmt->close();

require '../lib/header.php';
?>

<style>
  .form-check-label, .form-label {
    word-wrap: break-word;
    white-space: normal;
  }

  .form-check {
    flex-wrap: wrap;
  }

  .form-check img {
    max-height: 24px;
    object-fit: contain;
  }

  @media (max-width: 576px) {
    .form-check-label small {
      display: block;
      margin-top: 4px;
      font-size: 0.75rem;
    }
  }
</style>


<div class="section-container py-5">
  <div class="container" style="max-width: 600px;">
    <div class="card shadow rounded-4 border-0">
      <div class="card-body p-4">
        <h4 class="text-center mb-4"><i class="bi bi-wallet2 me-1"></i> Deposit Saldo</h4>

        <?php if (isset($_GET['error'])): ?>
          <script>
            Swal.fire({
              icon: 'error',
              title: 'Oops!',
              text: <?= json_encode(urldecode($_GET['error'])) ?>,
              confirmButtonText: 'OK'
            });
          </script>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label for="amount" class="form-label fw-semibold">Nominal Deposit</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" name="amount" id="amount" class="form-control" placeholder="Contoh: 10000" min="1000" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Metode Pembayaran</label>

            <?php if (count($metodeList) === 0): ?>
              <div class="alert alert-warning">Tidak ada metode pembayaran tersedia.</div>
            <?php else: ?>
              <?php foreach ($metodeList as $item): ?>
                <div class="form-check border rounded p-2 mb-2 d-flex align-items-center gap-2">
                  <input class="form-check-input mt-0" type="radio" name="metode" id="metode_<?= $item['kode'] ?>" value="<?= $item['kode'] ?>" required>
                  <label class="form-check-label w-100" for="metode_<?= $item['kode'] ?>">
                    <div class="d-flex justify-content-between align-items-center pe-2">
                      <div class="d-flex align-items-center gap-2">
                        <img src="<?= $item['logo'] ?>" alt="<?= htmlspecialchars($item['nama']) ?>">
                      </div>
                      <div class="text-end" style="max-width: 70%; padding-right: 10px;">
                        <div class="fw-semibold">
                          <?= htmlspecialchars(trim(preg_replace('/\b(E-Wallet|Virtual-Account)\b/i', '', $item['nama']))) ?>
                        </div>
                        <small class="text-muted">Min: Rp<?= number_format($item['minimal']) ?> – Max: Rp<?= number_format($item['maksimal']) ?></small>
                      </div>
                    </div>
                  </label>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-arrow-right-circle me-1"></i> Lanjutkan ke Pembayaran
          </button>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require '../lib/footer.php'; ?>
