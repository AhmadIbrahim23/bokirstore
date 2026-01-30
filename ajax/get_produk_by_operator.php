<?php
session_start();
require '../config.php';
header('Content-Type: application/json');

// Ambil parameter
$kategori = $_GET['kategori'] ?? '';
$brand_raw = $_GET['brand'] ?? '';

// Validasi awal
if (!$kategori || !$brand_raw) {
    echo json_encode([]);
    exit;
}

// Normalisasi brand input
$brand_input_normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $brand_raw));

// Query awal tanpa filter brand dulu
$stmt = $conn->prepare("
    SELECT id, nama_produk, kategori, deskripsi, brand, logo, harga_jual, harga_diskon, tipe_diskon
    FROM layanan
    WHERE kategori = ?
      AND status = 'normal'
      AND (
            (tipe_diskon = 'On' AND expired_diskon IS NOT NULL AND expired_diskon > NOW())
            OR tipe_diskon != 'On'
          )
");

$stmt->bind_param("s", $kategori);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nama_produk, $kategori_val, $deskripsi, $brand_db, $logo, $harga_jual, $harga_diskon, $tipe_diskon);

// Filter brand secara manual
$data = [];
while ($stmt->fetch()) {
    $brand_db_normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $brand_db));
    if ($brand_db_normalized === $brand_input_normalized) {
        $data[] = [
            'id'            => $id,
            'nama_produk'   => $nama_produk,
            'kategori'      => $kategori_val,
            'deskripsi'      => $deskripsi,
            'brand'         => $brand_db,
            'logo'          => $logo,
            'harga_jual'    => (float) $harga_jual,
            'harga_diskon'  => (float) $harga_diskon,
            'tipe_diskon'   => $tipe_diskon
        ];
    }
}

// Urutkan dari harga efektif termurah
usort($data, function ($a, $b) {
    $hargaA = ($a['tipe_diskon'] === 'On' && $a['harga_diskon'] > 0) ? $a['harga_diskon'] : $a['harga_jual'];
    $hargaB = ($b['tipe_diskon'] === 'On' && $b['harga_diskon'] > 0) ? $b['harga_diskon'] : $b['harga_jual'];
    return $hargaA <=> $hargaB;
});

$stmt->close();
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);