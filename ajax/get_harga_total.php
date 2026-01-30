<?php
require '../config.php';
header('Content-Type: application/json');

$produkId = $_GET['produk_id'] ?? '';
$kode     = $_GET['pembayaran'] ?? '';

if (!$produkId || !$kode) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Ambil produk
$stmt = $conn->prepare("SELECT harga_jual, harga_diskon, tipe_diskon FROM layanan WHERE id = ?");
$stmt->bind_param("s", $produkId);
$stmt->execute();
$stmt->store_result();

$harga_jual = $harga_diskon = 0;
$tipe_diskon = '';
$stmt->bind_result($harga_jual, $harga_diskon, $tipe_diskon);

if ($stmt->num_rows > 0) {
    $stmt->fetch();
    $harga = ($tipe_diskon === 'On' && $harga_diskon > 0) ? $harga_diskon : $harga_jual;
} else {
    echo json_encode(['error' => 'Produk tidak ditemukan']);
    exit;
}
$stmt->close();

// ✅ Jika pembayaran SALDO, langsung return harga produk tanpa biaya tambahan
if (strtoupper($kode) === 'SALDO') {
    echo json_encode(['total' => ceil($harga)]);
    exit;
}

// Ambil biaya metode pembayaran lainnya
$stmt2 = $conn->prepare("SELECT biaya, percent_biaya, tambahan_biaya, tipe_tambahan FROM metode_pembayaran WHERE LOWER(kode) = ?");
$stmt2->bind_param("s", $kode);
$stmt2->execute();
$stmt2->store_result();

$biaya = $tambahan_biaya = 0;
$percent_biaya = $tipe_tambahan = '';
$stmt2->bind_result($biaya, $percent_biaya, $tambahan_biaya, $tipe_tambahan);

if ($stmt2->num_rows > 0) {
    $stmt2->fetch();

    $biayaFinal = ($percent_biaya === 'Percent') ? ($harga * ($biaya / 100)) : $biaya;
    $tambahanFinal = ($tipe_tambahan === 'Percent') ? ($harga * ($tambahan_biaya / 100)) : $tambahan_biaya;

    $total = ceil($harga + $biayaFinal + $tambahanFinal);

    echo json_encode(['total' => $total]);
} else {
    echo json_encode(['error' => 'Metode pembayaran tidak ditemukan']);
}
$stmt2->close();
