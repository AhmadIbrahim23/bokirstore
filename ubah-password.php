<?php
session_start();
require 'config.php';

// Cek jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: '.$web_url.'/login');
    exit;
}

$success = $error = "";

// Proses ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id       = $_SESSION['user_id'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi    = $_POST['konfirmasi'];

    if (!$password_lama || !$password_baru || !$konfirmasi) {
        $error = "Semua field wajib diisi.";
    } elseif ($password_baru !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek password lama
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($password_lama, $hashed)) {
            $error = "Password lama salah.";
        } else {
            // Update password baru
            $baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $baru_hash, $user_id);
            if ($update->execute()) {
                $success = "Password berhasil diubah.";
            } else {
                $error = "Gagal mengubah password.";
            }
            $update->close();
        }
    }
}
require 'lib/header.php';
?>

<div class="section-container py-5">
  <div class="container" style="max-width: 500px;">
    <h3 class="text-center mb-4">Ubah Password</h3>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm rounded">
      <div class="mb-3">
        <label class="form-label">Password Lama</label>
        <input type="password" name="password_lama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password_baru" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Konfirmasi Password Baru</label>
        <input type="password" name="konfirmasi" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
    </form>
  </div>
</div>

<?php require 'lib/footer.php'; ?>
