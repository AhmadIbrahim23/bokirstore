<?php
session_start();
require 'config.php';
require 'lib/header.php';

?>

<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="fw-bold"><i class="bi bi-gem me-2"></i>Kalkulator Zodiac Summon</h3>
      <p class="text-muted">Hitung berapa diamond yang dibutuhkan untuk menebus skin Zodiac favorit kamu!</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow-sm rounded-4">
          <div class="mb-3">
            <label for="starPower" class="form-label">Star Power Saat Ini</label>
            <input type="number" class="form-control" id="starPower" placeholder="Contoh: 40">
          </div>
          <button class="btn btn-primary w-100 fw-semibold" onclick="hitungZodiac()">Hitung Estimasi</button>
          <div id="hasilZodiac" class="alert alert-danger mt-4 d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function hitungZodiac() {
  const starPower = parseInt(document.getElementById("starPower").value);
  const hasil = document.getElementById("hasilZodiac");

  if (isNaN(starPower) || starPower < 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Input Tidak Valid',
      text: 'Masukkan jumlah Star Power dengan benar.',
    });
    hasil.classList.add('d-none');
    return;
  }

  const targetPower = 100;
  const sisaPower = targetPower - starPower;

  if (sisaPower <= 0) {
    hasil.innerHTML = `🎉 <strong>Selamat!</strong> Kamu sudah bisa klaim skin Zodiac.`;
    hasil.classList.remove('d-none');
    return;
  }

  // Rata-rata 1 summon = 1 Star Power (tidak selalu pasti)
  const diamondPerSummon = 20;
  const estimasiSummon = sisaPower;
  const estimasiDiamond = estimasiSummon * diamondPerSummon;

  hasil.innerHTML = `
    <strong>Sisa Star Power Dibutuhkan:</strong> ${sisaPower}<br>
    <strong>Estimasi Summon Tambahan:</strong> ${estimasiSummon}<br>
    <strong>Estimasi Diamond Diperlukan:</strong> ${estimasiDiamond} 💎
  `;
  hasil.classList.remove('d-none');
}
</script>


<?php require 'lib/footer.php'; ?>