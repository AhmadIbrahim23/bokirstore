<?php
require '../config.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Content-Type: text/html; charset=utf-8");

$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

$results = [];
$input_too_short = false;

// Validasi input
if (strlen($search) > 50 || !preg_match('/^[a-z0-9\s]+$/i', $search)) {
    $search = '';
} elseif (strlen($search) < 2) {
    $input_too_short = true;
    $search = '';
}

// Proses pencarian jika valid
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT 
            MIN(id) AS id, 
            ANY_VALUE(nama_produk) AS nama_produk,
            kategori, 
            brand, 
            ANY_VALUE(tipe) AS tipe, 
            ANY_VALUE(deskripsi) AS deskripsi, 
            ANY_VALUE(logo) AS logo 
        FROM layanan 
        WHERE 
            LOWER(brand) LIKE CONCAT('%', ?, '%') 
            OR LOWER(kategori) LIKE CONCAT('%', ?, '%') 
        AND status = 'normal'
        GROUP BY brand, kategori 
        ORDER BY id DESC 
        LIMIT 30
    ");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nama_produk, $kategori, $brand, $tipe, $deskripsi, $logo);

    while ($stmt->fetch()) {
        $results[] = [
            'id' => $id,
            'nama_produk' => $nama_produk,
            'kategori' => $kategori,
            'brand' => $brand,
            'tipe' => $tipe,
            'deskripsi' => $deskripsi,
            'logo' => $logo
        ];
    }

    $stmt->close();
}
?>

<?php if ($input_too_short): ?>
    <div class="text-muted text-center"><small>Ketik minimal 2 huruf untuk mencari produk.</small></div>

<?php elseif (empty($results)): ?>
    <div class="text-muted text-center"><small>Tidak ada produk ditemukan.</small></div>

<?php else: ?>
    <div class="live-sreach px-2 px-md-3">
        <div class="row g-3">
            <?php foreach ($results as $produk): ?>
                <?php
                    $kategori = htmlspecialchars($produk['kategori']);
                    $brand = htmlspecialchars($produk['brand']);
            
                    // Ubah ke lowercase + strip spasi agar file tujuan bisa cocok (pulsa.php, paket_data.php, dsb)
                    $slug = strtolower(preg_replace('/[^a-z0-9]/', '', $kategori));
                    $targetUrl = "".$web_url."/order/{$slug}.php?kategori=" . urlencode($kategori) . "&brand=" . urlencode($brand);
                ?>
                <div class="col-6 col-md-4 col-lg-2 product-item"
                     data-nama="<?= $brand ?>"
                     data-keywords="<?= $kategori ?>">
            
                    <a href="<?= $targetUrl ?>" class="text-decoration-none text-dark">
                        <div class="product-live-sreach d-flex align-items-center p-2 border rounded h-100 bg-white">
                            <div class="img-wrapper bg-light d-flex align-items-center justify-content-center me-2">
                                <img src="<?= !empty($produk['logo']) ? htmlspecialchars($produk['logo']) : 'https://img.icons8.com/fluency/48/gift.png' ?>" 
                                     alt="<?= $brand ?> <?= $kategori ?>" 
                                     class="product-img">
                            </div>
                            <div class="flex-grow-1 text-start">
                                <div class="fw-semibold small mb-1"><?= $brand ?></div>
                                <div class="type-sreach">
                                    <span class="detail-class-produk text-muted small"><?= $kategori ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
            
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
