<?php
session_start();
require '../config.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: $web_url/login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$limit = 10; // jumlah data per halaman
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_stmt = $conn->prepare("SELECT COUNT(*) FROM deposit WHERE user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_stmt->bind_result($total_rows);
$total_stmt->fetch();
$total_stmt->close();

$total_pages = ceil($total_rows / $limit);

// Ambil data deposit
$stmt = $conn->prepare("SELECT id, kode_transaksi, metode, amount, total_transfer, status, created_at 
                        FROM deposit 
                        WHERE user_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT ?, ?");
$stmt->bind_param("iii", $user_id, $offset, $limit);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $kode_transaksi, $metode, $amount, $total_transfer, $status, $created_at);

require '../lib/header.php';
?>

<div class="section-container py-5">
  <div class="container">
    <h3 class="mb-4">Riwayat Deposit</h3>

    <?php if ($stmt->num_rows === 0): ?>
      <div class="alert alert-info">Belum ada riwayat deposit.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-nowrap">
          <thead class="table-light">
            <tr>
              <th>Kode Transaksi</th>
              <th>Metode</th>
              <th>Nominal</th>
              <th>Total Transfer</th>
              <th>Status</th>
              <th>Waktu</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $statusColor = [
              'pending' => 'warning',
              'paid' => 'success',
              'expired' => 'danger',
              'failed' => 'secondary'
            ];

            while ($stmt->fetch()):
              $badge = $statusColor[strtolower($status)] ?? 'dark';
            ?>
              <tr>
                <td><?= htmlspecialchars($kode_transaksi) ?></td>
                <td><?= htmlspecialchars($metode) ?></td>
                <td>Rp <?= number_format($amount, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($total_transfer, 0, ',', '.') ?></td>
                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($created_at)) ?> WIB</td>
                <td>
                    <a href="<?= $web_url ?>/deposit/deposit-invoice.php?ref=<?= urlencode(encrypt($kode_transaksi)) ?>" class="btn btn-outline-primary">Detail</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav class="mt-4">
        <ul class="pagination justify-content-center flex-wrap">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page - 1 ?>">«</a>
            </li>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <?php if ($page < $total_pages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page + 1 ?>">»</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>

  </div>
</div>

<?php
$stmt->close();
require '../lib/footer.php';
?>
