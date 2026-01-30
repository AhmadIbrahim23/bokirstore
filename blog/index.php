<?php
session_start();
require '../config.php';
require '../lib/header.php';

// Ambil dan dekripsi ID
$id = isset($_GET['id']) ? decrypt($_GET['id']) : false;
$data = null;
$error = null;

if ($id) {
    $stmt = $conn->prepare("SELECT judul, konten, logo, waktu_dibuat FROM blog WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($judul, $konten, $logo, $waktu);
        $stmt->fetch();
        $data = [
            'judul' => $judul,
            'konten' => $konten,
            'logo' => $logo,
            'waktu' => $waktu
        ];
    } else {
        $error = "Data blog tidak ditemukan.";
    }
    $stmt->close();
} else {
    $error = "ID blog tidak valid.";
}
?>

<div class="section-container py-5">
  <div class="container">
    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($data): ?>
      <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
          <div class="card border-0 shadow rounded-4 p-4">
            <div class="row align-items-center flex-column flex-md-row">
              <div class="col-md-5 text-center mb-4 mb-md-0">
                <img src="<?= $web_url ?>/blog/img/<?= htmlspecialchars($data['logo']) ?>" alt="<?= htmlspecialchars($data['judul']) ?>" class="img-fluid rounded shadow" style="max-height: 300px; object-fit: contain;">
              </div>
              <div class="col-md-7">
                <h2 class="fw-bold mb-2"><?= htmlspecialchars($data['judul']) ?></h2>
                <p class="text-muted">Dipublikasikan pada <?= date('d M Y H:i', strtotime($data['waktu'])) ?></p>
              </div>
            </div>
            <hr class="my-4">
            <div class="fs-5" style="white-space: pre-wrap;"><?= htmlspecialchars($data['konten']) ?></div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require '../lib/footer.php'; ?>
