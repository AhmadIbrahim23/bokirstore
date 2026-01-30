<?php
session_start();
require 'config.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: $web_url/dashboard");
    exit;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']);
    $password = $_POST['password'];

    if (!$login || !$password) {
        $_SESSION['popup_error'] = "Email/Username dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $stmt->close();
                header("Location: $web_url/dashboard");
                exit;
            } else {
                $_SESSION['popup_error'] = "Password salah.";
            }
        } else {
            $_SESSION['popup_error'] = "Akun tidak ditemukan.";
        }

        $stmt->close();
    }

    // Redirect agar tidak submit ulang jika di-refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

require 'lib/header.php';
?>

<div class="section-container py-5">
  <div class="container" style="max-width: 500px;">
    <h3 class="text-center mb-4">Login</h3>
    <form method="POST" class="card p-4 shadow-sm rounded">
      <div class="mb-3">
        <label class="form-label">Email atau Username</label>
        <input type="text" name="login" class="form-control" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>

    <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
  </div>
</div>

<?php require 'lib/footer.php'; ?>
