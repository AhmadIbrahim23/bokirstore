<?php
session_start();
require 'config.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: $web_url/dashboard");
    exit;
}

// Tangkap notifikasi
$success = isset($_GET['success']) ? urldecode($_GET['success']) : '';
$error   = isset($_GET['error']) ? urldecode($_GET['error']) : '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $nomor_hp  = preg_replace('/\D/', '', trim($_POST['nomor_hp'])); // hanya angka

    if (!$username || !$email || !$password || !$nomor_hp) {
        $_SESSION['popup_error'] = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['popup_error'] = "Format email tidak valid.";
    } elseif (!preg_match('/^08[0-9]{8,11}$/', $nomor_hp)) {
        $_SESSION['popup_error'] = "Format nomor WhatsApp tidak valid. Harus mulai dengan 08 dan 10–13 digit.";
    } elseif (strlen($password) < 6) {
        $_SESSION['popup_error'] = "Password minimal 6 karakter.";
    } else {
        // Cek username
        $stmt_users = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $stmt_users->bind_param("s", $username);
        $stmt_users->execute();
        $stmt_users->store_result();
        if ($stmt_users->num_rows > 0) {
            $_SESSION['popup_error'] = "Username sudah digunakan.";
            $stmt_users->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $stmt_users->close();

        // Cek email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['popup_error'] = "Email sudah digunakan.";
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $stmt->close();

        // Simpan user
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password, nomor_hp) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $email, $hashed, $nomor_hp);

        if ($insert->execute()) {
            $insert->close();
            $_SESSION['popup_success'] = "Pendaftaran berhasil. Silakan login.";
            header("Location: $web_url/login");
            exit;
        } else {
            $insert->close();
            $_SESSION['popup_error'] = "Gagal mendaftar. Silakan coba lagi.";
        }
    }

    // Redirect kembali ke form (PRG)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

require 'lib/header.php';
?>

<div class="section-container py-5">
  <div class="container" style="max-width: 500px;">
    <h3 class="text-center mb-4">Daftar Akun</h3>

    <form method="POST" class="card p-4 shadow-sm rounded">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">No HP</label>
        <input type="text" name="nomor_hp" class="form-control" value="<?= htmlspecialchars($_POST['nomor_hp'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>

    <p class="text-center mt-3">Sudah punya akun? <a href="<?= $web_url ?>/login">Masuk</a></p>
  </div>
</div>
<?php require 'lib/footer.php'; ?>
