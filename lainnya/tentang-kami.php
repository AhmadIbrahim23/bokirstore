<?php
session_start();
require '../config.php';
require '../lib/header.php';

?>

<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Tentang Kami</h2>
      <p class="text-muted">Layanan Digital & Topup Game Terpercaya</p>
    </div>
    <div class="row align-items-center mb-4">
      <div class="col-md-6">
        <img src="<?php echo $web_url; ?>/assets/img/tentang-kami.png" class="img-fluid rounded-4 shadow-sm" alt="Layanan PPOB">
      </div>
      <div class="col-md-6">
        <h4 class="fw-semibold">PPOB (Payment Point Online Bank)</h4>
        <p>
          Kami menyediakan layanan PPOB lengkap untuk memudahkan Anda dalam melakukan berbagai pembayaran seperti listrik PLN, PDAM, pulsa, paket data, BPJS, hingga tagihan lainnya secara cepat dan aman.
        </p>
        <ul class="list-unstyled">
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Pembayaran tagihan 24/7</li>
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Transaksi cepat & realtime</li>
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Didukung banyak metode pembayaran</li>
        </ul>
      </div>
    </div>
    <div class="row align-items-center flex-md-row-reverse">
      <div class="col-md-6">
        <img src="<?php echo $web_url; ?>/assets/img/games-online.png" class="img-fluid rounded-4 shadow-sm" alt="Topup Game">
      </div>
      <div class="col-md-6">
        <h4 class="fw-semibold">Topup Game Online</h4>
        <p>
          Nikmati kemudahan isi ulang berbagai game favorit seperti Mobile Legends, Free Fire, PUBG Mobile, Valorant, dan masih banyak lagi dengan harga bersaing dan proses instan.
        </p>
        <ul class="list-unstyled">
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Harga bersaing & diskon menarik</li>
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Tanpa registrasi akun</li>
          <li><i class="bi bi-check-circle-fill text-success me-2"></i> Layanan cepat dan aman</li>
        </ul>
      </div>
    </div>
  </div>
</div>



<?php require '../lib/footer.php'; ?>