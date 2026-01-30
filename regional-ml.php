<?php
session_start();
require 'config.php';
require 'lib/header.php';

?>

<div class="section-container py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="fw-bold">Cek Regional Mobile Legends</h3>
      <p class="text-muted">Masukkan ID dan Server MLBB Anda untuk mengetahui estimasi wilayah.</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card p-4 shadow-sm rounded-4">
          <div class="mb-3">
            <label for="ml_id" class="form-label">User ID</label>
            <input type="number" class="form-control" id="ml_id" placeholder="Contoh: 12345678">
          </div>
          <div class="mb-3">
            <label for="server_id" class="form-label">Server ID</label>
            <input type="number" class="form-control" id="server_id" placeholder="Contoh: 1234">
          </div>
          <button class="btn btn-custom-primary w-100" onclick="cekRegional()">Cek Regional</button>
          <div id="hasilRegional" class="mt-4 alert alert-danger d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
function cekRegional() {
  const userId = document.getElementById("ml_id").value.trim();
  const serverId = document.getElementById("server_id").value.trim();
  const hasil = document.getElementById("hasilRegional");

  if (!userId || !serverId) {
    Swal.fire({
      icon: 'warning',
      title: 'Data Tidak Lengkap',
      text: 'Harap isi User ID dan Server ID terlebih dahulu!',
    });
    hasil.classList.add("d-none");
    return;
  }

  // Tampilkan SweetAlert loading
  Swal.fire({
    title: 'Memproses...',
    html: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading(); // icon loading
    }
  });

  // Gabungkan userId + serverId (jika memang dibutuhkan API, sesuaikan)
  const fullUserId = `${userId}${serverId}`;

  // Kirim request ke backend
  fetch('<?php echo $web_url; ?>/ajax/cek-akun-ml.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `user_id=${encodeURIComponent(fullUserId)}`
  })
  .then(res => res.json())
  .then(response => {
    Swal.close(); // Tutup loading setelah respon diterima

    if (response.status === 1 && response.data && response.data.is_valid) {
      // Deteksi regional
      const server = parseInt(serverId);
      let regional = "Tidak diketahui";
      if (server >= 2000 && server <= 2999) regional = "Indonesia";
      else if (server >= 1200 && server <= 1299) regional = "Malaysia / Singapura";
      else if (server >= 1300 && server <= 1399) regional = "Filipina / Thailand";
      else if (server >= 1400 && server <= 1499) regional = "Amerika Selatan";
      else if (server >= 1500 && server <= 1599) regional = "Eropa / Timur Tengah";
      else if (server >= 1600 && server <= 1999) regional = "Server Global (Internasional)";

      hasil.innerHTML = `
        <strong>Nama Akun:</strong> ${response.data.username}<br>
        <strong>Status Akun:</strong> Valid<br>
        <strong>Perkiraan Regional:</strong> ${regional}
      `;
      hasil.classList.remove("d-none");

    } else {
      Swal.fire({
        icon: 'error',
        title: 'Akun Tidak Ditemukan',
        text: 'Data tidak valid atau nama akun tidak ditemukan.',
      });
      hasil.classList.add("d-none");
    }
  })
  .catch(err => {
    console.error(err);
    Swal.close();
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Terjadi kesalahan saat menghubungi server.',
    });
    hasil.classList.add("d-none");
  });
}
</script>




<?php require 'lib/footer.php'; ?>