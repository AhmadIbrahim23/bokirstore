<?php
session_start();
require 'config.php';
require 'lib/header.php';

?>

<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="fw-bold">Kalkulator Winrate</h3>
      <p class="text-muted">Hitung presentase kemenangan kamu secara instan!</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow-sm rounded-4">
          <div class="mb-3">
            <label for="menang" class="form-label">Jumlah Menang</label>
            <input type="number" class="form-control" id="menang" placeholder="Contoh: 210">
          </div>
          <div class="mb-3">
            <label for="total" class="form-label">Total Pertandingan</label>
            <input type="number" class="form-control" id="total" placeholder="Contoh: 300">
          </div>
          <button class="btn btn-primary w-100" onclick="hitungWinrate()">Hitung Winrate</button>

          <div id="hasilWinrate" class="alert alert-danger mt-4 d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function hitungWinrate() {
  const menang = parseInt(document.getElementById('menang').value);
  const total = parseInt(document.getElementById('total').value);
  const hasil = document.getElementById('hasilWinrate');

  if (isNaN(menang) || isNaN(total) || menang < 0 || total <= 0 || menang > total) {
    Swal.fire({
      icon: 'warning',
      title: 'Input Tidak Valid',
      text: 'Mohon masukkan angka yang benar. Jumlah menang tidak boleh lebih dari total.',
    });
    hasil.classList.add('d-none');
    return;
  }

  const winrate = ((menang / total) * 100).toFixed(2);
  hasil.innerHTML = `Winrate Kamu: <strong>${winrate}%</strong>`;
  hasil.classList.remove('d-none');
}
</script>


<?php require 'lib/footer.php'; ?>