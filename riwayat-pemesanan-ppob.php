<?php
session_start();
require 'config.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: $web_url/login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil filter dari form (GET)
$kode_transaksi = $_GET['kode_transaksi'] ?? '';
$status_pembayaran = $_GET['status_pembayaran'] ?? '';
$status_pengiriman = $_GET['status_pengiriman'] ?? '';
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Query dasar dan parameter
$where = "user_id = ?";
$params = [$user_id];
$types = "i";

// Tambah filter dinamis
if (!empty($kode_transaksi)) {
    $where .= " AND kode_transaksi LIKE ?";
    $params[] = '%' . $kode_transaksi . '%';
    $types .= "s";
}
if (!empty($status_pembayaran)) {
    $where .= " AND status_pembayaran = ?";
    $params[] = $status_pembayaran;
    $types .= "s";
}
if (!empty($status_pengiriman)) {
    $where .= " AND status_pengiriman = ?";
    $params[] = $status_pengiriman;
    $types .= "s";
}
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $where .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params[] = $tanggal_awal;
    $params[] = $tanggal_akhir;
    $types .= "ss";
}

// Hitung total data
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM pemesanan_ppob WHERE $where");
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$count_stmt->bind_result($total_rows);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_rows / $limit);

// Ambil data transaksi
$sql = "SELECT id, kode_transaksi, no_hp, harga_total, status_pembayaran, status_pengiriman, created_at 
        FROM pemesanan_ppob 
        WHERE $where 
        ORDER BY id DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$params[] = $limit;
$params[] = $offset;
$types .= "ii";
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $kode_transaksi, $no_hp, $harga_total, $status_pembayaran, $status_pengiriman, $created_at);

require 'lib/header.php';
?>

<div class="section-container py-5">
  <div class="container">
    <h4 class="mb-4">Riwayat Pemesanan PPOB</h4>

    <form method="get" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="kode_transaksi" value="<?= htmlspecialchars($kode_transaksi) ?>" class="form-control" placeholder="ID Transaksi">
      </div>
      <div class="col-md-2">
        <select name="status_pembayaran" class="form-select">
          <option value="">Status Bayar</option>
          <option value="pending" <?= $status_pembayaran === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="paid" <?= $status_pembayaran === 'paid' ? 'selected' : '' ?>>Paid</option>
          <option value="expired" <?= $status_pembayaran === 'expired' ? 'selected' : '' ?>>Expired</option>
          <option value="failed" <?= $status_pembayaran === 'failed' ? 'selected' : '' ?>>Failed</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="status_pengiriman" class="form-select">
          <option value="">Status Kirim</option>
          <option value="pending" <?= $status_pengiriman === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="proses" <?= $status_pengiriman === 'proses' ? 'selected' : '' ?>>Proses</option>
          <option value="berhasil" <?= $status_pengiriman === 'berhasil' ? 'selected' : '' ?>>Berhasil</option>
          <option value="gagal" <?= $status_pengiriman === 'gagal' ? 'selected' : '' ?>>Gagal</option>
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" class="form-control">
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Cari</button>
      </div>
    </form>

    <?php if ($stmt->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped text-center small">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>No. Tujuan</th>
              <th>Total</th>
              <th>Status Bayar</th>
              <th>Status Pengiriman</th>
              <th>Waktu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($stmt->fetch()): ?>
              <tr>
                <td><?= htmlspecialchars($kode_transaksi) ?></td>
                <td><?= htmlspecialchars($no_hp) ?></td>
                <td>Rp <?= number_format($harga_total, 0, ',', '.') ?></td>
                <td>
                  <?php
                  $warna = [
                      'pending' => 'warning',
                      'paid' => 'success',
                      'expired' => 'danger',
                      'failed' => 'secondary'
                  ];
                  ?>
                  <span class="badge bg-<?= $warna[$status_pembayaran] ?? 'dark' ?>"><?= ucfirst($status_pembayaran) ?></span>
                </td>
                <td>
                  <?php
                  $warna2 = [
                      'pending' => 'secondary',
                      'proses' => 'info',
                      'berhasil' => 'success',
                      'gagal' => 'danger'
                  ];
                  ?>
                  <span class="badge bg-<?= $warna2[$status_pengiriman] ?? 'dark' ?>"><?= ucfirst($status_pengiriman) ?></span>
                </td>
                <td><?= htmlspecialchars($created_at) ?></td>
                <td>
                  <a href="<?= $web_url ?>/invoice.php?order_id=<?= urlencode(encrypt($id)) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">«</a></li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $total_pages): ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">»</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php else: ?>
      <div class="alert alert-warning">Tidak ada data ditemukan.</div>
    <?php endif; ?>
  </div>
</div>

<?php
$stmt->close();
require 'lib/footer.php';
?>
