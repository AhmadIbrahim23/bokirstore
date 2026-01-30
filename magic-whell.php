<?php
session_start();
require 'config.php';
require 'lib/header.php';

?>

<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="fw-bold"><i class="bi bi-stars me-2"></i>Kalkulator Magic Wheel</h3>
      <p class="text-muted">Hitung estimasi Diamond yang dibutuhkan untuk mendapatkan Skin Epic Limited.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow-sm rounded-4">
          <div class="mb-3">
            <label for="frag" class="form-label">Jumlah Fragment Saat Ini</label>
            <input type="number" class="form-control" id="frag" placeholder="Contoh: 80">
          </div>
          <div class="mb-3">
            <label for="spin" class="form-label">Jumlah Spin Saat Ini</label>
            <input type="number" class="form-control" id="spin" placeholder="Contoh: 20">
          </div>
          <button class="btn btn-warning w-100 fw-semibold" onclick="hitungMW()">Hitung Estimasi</button>
          <div id="hasilMW" class="alert alert-danger mt-4 d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function hitungMW() {
  const frag = parseInt(document.getElementById("frag").value);
  const spin = parseInt(document.getElementById("spin").value);
  const hasil = document.getElementById("hasilMW");

  if (isNaN(frag) || isNaN(spin) || frag < 0 || spin < 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Input Tidak Valid',
      text: 'Harap isi fragment dan spin dengan angka yang benar!',
    });
    hasil.classList.add('d-none');
    return;
  }

  const fragTarget = 200;
  const sisaFrag = fragTarget - frag;
  const estimasiSpin = sisaFrag * 2; // rata-rata 1 spin dapat 0.5 frag
  const totalSpin = Math.max(estimasiSpin, 0);

  const diamondPerSpin = 20;
  const diamond5xSpin = 90;
  const diamondTotal = Math.ceil(totalSpin / 5) * diamond5xSpin;

  hasil.innerHTML = `
    <strong>Sisa Fragment Dibutuhkan:</strong> ${sisaFrag} frag<br>
    <strong>Estimasi Spin Tambahan:</strong> ${totalSpin} spin<br>
    <strong>Estimasi Diamond Diperlukan:</strong> ${diamondTotal} 💎
  `;
  hasil.classList.remove('d-none');
}
</script>



<?php require 'lib/footer.php'; ?>