<?php
session_start();
require 'config.php';
require 'lib/header.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
  header('Location: '.$web_url.'/login');
  exit;
}
// Ambil informasi user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, nomor_hp, saldo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $nomor_hp, $saldo);
$stmt->fetch();
$stmt->close();
?>

<div class="section-container py-5">
  <div class="container">
    <h2 class="text-center mb-4">Dashboard</h2>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow rounded">
          <div class="card-body">
            <h5 class="card-title mb-3">Selamat datang, <strong><?= htmlspecialchars($username) ?></strong>!</h5>
            <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p class="mb-2"><strong>Saldo Anda:</strong> Rp <?= number_format($saldo, 0, ',', '.') ?></p>

            <div class="d-grid gap-2 mt-4">
              <a href="<?= $web_url ?>/deposit/" class="btn btn-success">
                <i class="bi bi-wallet2 me-1"></i> Deposit Saldo
              </a>
              <a href="<?= $web_url ?>/ubah-password" class="btn btn-warning">
                <i class="bi bi-lock me-1"></i> Ganti Password
              </a>
              <a href="<?= $web_url ?>/deposit/riwayat-deposit" class="btn btn-secondary">
                <i class="bi bi-clock-history me-1"></i> Riwayat Deposit
              </a>
              <a href="<?= $web_url ?>/riwayat-pemesanan-ppob" class="btn btn-primary">
                <i class="bi bi-clock-history me-1"></i> Riwayat Pemesanan
              </a>
              <a href="<?= $web_url ?>/logout" class="btn btn-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require 'lib/footer.php'; ?>
