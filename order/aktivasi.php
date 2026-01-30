<?php
session_start();
require '../config.php';

function hitungBiayaAdmin($harga_jual, $biaya, $tipe_biaya, $tambahan_biaya, $tipe_tambahan) {
    $biaya_admin = 0;

    // Biaya utama
    if (strtolower($tipe_biaya) === 'percent') {
        $biaya_admin += ($harga_jual * $biaya / 100);
    } else {
        $biaya_admin += $biaya;
    }

    // Tambahan biaya
    if (strtolower($tipe_tambahan) === 'percent') {
        $biaya_admin += ($harga_jual * $tambahan_biaya / 100);
    } else {
        $biaya_admin += $tambahan_biaya;
    }

    return $biaya_admin;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_hp      = trim($_POST['noHp'] ?? '');
    $produk_id  = trim($_POST['produk'] ?? '');
    $pembayaran = trim($_POST['pembayaran'] ?? '');
    $kontak     = trim($_POST['kontak'] ?? '');

    if (empty($no_hp) || empty($produk_id) || empty($pembayaran) || empty($kontak)) {
        $_SESSION['popup_error'] = "Semua formulir pemesanan wajib diisi.";
    } elseif (!preg_match('/^08[0-9]{8,11}$/', $no_hp)) {
        $_SESSION['popup_error'] = "Format nomor HP tidak valid.";
    } elseif  (!preg_match('/^08[0-9]{8,11}$/', $kontak)) {
         $_SESSION['popup_error'] = "Format nomor kontak/whatsapp tidak valid. Harus diawali 08 dan 10–13 digit.";
    } else {
        // Ambil level user (0: biasa, 1: reseller)
        $level = $_SESSION['level'] ?? 0;
        // PENTING UNTUK UPDATE KE DEPAN
        

        // Ambil produk lengkap
        $stmt = $conn->prepare("SELECT kode_produk, nama_produk, harga_jual, harga_reseller, harga_diskon, tipe_diskon, expired_diskon, tipe_produk FROM layanan WHERE id = ? AND status = 'normal' LIMIT 1");
        $stmt->bind_param("i", $produk_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($kode_produk, $nama_produk, $harga_jual_default, $harga_reseller, $harga_diskon, $tipe_diskon, $expired_diskon, $tipe_produk);
            $stmt->fetch();
            $stmt->close();

            // Hitung harga final sesuai ketentuan
            $harga_jual = $harga_jual_default; // Default
            $now = date("Y-m-d H:i:s");

            if ($tipe_diskon === 'On' && $expired_diskon >= $now) {
                $harga_jual = $harga_diskon;
            } elseif ($tipe_diskon === 'Off' && $level == 1) {
                $harga_jual = $harga_reseller;
            } elseif ($tipe_diskon === 'On' && $expired_diskon < $now) {
                $harga_jual = $harga_jual_default;
            }

            // Data insert
            $kode_transaksi = 'INV' . time() . rand(100, 999);
            $user_id = $_SESSION['user_id'] ?? null;
            $payment_gateway = 'sakurupiah';
            $kode_pembayaran = null;
            $ref_id = null;
            $status_pembayaran = 'pending';
            $status_pengiriman = 'pending';
            $status_digiflazz = 'belum_dikirim';
            $digiflazz_response = null;
            $callback_response = null;
            $json_invoice = null;
            $message = null;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
            $expired_at = null;
            $paid_at = null;
            $updated_at = date("Y-m-d H:i:s");

            if ($pembayaran === 'SALDO') {
                if (!$user_id) {
                    $_SESSION['popup_error'] = "Anda harus login untuk menggunakan saldo.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }

                $cek_saldo = $conn->prepare("SELECT saldo FROM users WHERE id = ? LIMIT 1");
                $cek_saldo->bind_param("i", $user_id);
                $cek_saldo->execute();
                $cek_saldo->store_result();
                $cek_saldo->bind_result($saldo_user);
                $cek_saldo->fetch();
                $cek_saldo->close();

                $biaya_admin = 0;
                $harga_total = $harga_jual;

                if ($saldo_user < $harga_total) {
                    $_SESSION['popup_error'] = "Saldo Anda tidak cukup untuk transaksi ini.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }

                $kurangi = $conn->prepare("UPDATE users SET saldo = saldo - ? WHERE id = ?");
                $kurangi->bind_param("di", $harga_total, $user_id);
                $kurangi->execute();
                $kurangi->close();

                $status_pembayaran = 'paid';
                $paid_at = date("Y-m-d H:i:s");
                $payment_gateway = 'SALDO';
            } else {
                // Ambil metode pembayaran
                $stmt2 = $conn->prepare("SELECT biaya, tambahan_biaya, percent_biaya, tipe_tambahan FROM metode_pembayaran WHERE kode = ? AND status = 'Aktif' LIMIT 1");
                $stmt2->bind_param("s", $pembayaran);
                $stmt2->execute();
                $stmt2->store_result();

                if ($stmt2->num_rows > 0) {
                $stmt2->bind_result($biaya, $tambahan_biaya, $percent_biaya, $tipe_tambahan);
                $stmt2->fetch();
                $stmt2->close();

                // Hitung biaya
                $biaya_admin = hitungBiayaAdmin($harga_jual, $biaya, $percent_biaya, $tambahan_biaya, $tipe_tambahan);
                $harga_total = $harga_jual + $biaya_admin;
                } else {
                    $_SESSION['popup_error'] = "Metode pembayaran tidak ditemukan.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
            }

                $insert = $conn->prepare("
                    INSERT INTO pemesanan_ppob (
                        kode_transaksi, user_id, no_hp, produk_id, metode_pembayaran,
                        payment_gateway, kode_pembayaran, harga_produk, biaya_admin, harga_total,
                        kontak, tipe_produk, status_pembayaran, status_pengiriman, ref_id,
                        status_digiflazz, digiflazz_response, callback_response, json_invoice, message,
                        ip_address, user_agent, expired_at, paid_at, created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?
                    )
                ");

                if ($insert === false) {
                    $_SESSION['popup_error'] = "Gagal mempersiapkan query: " . $conn->error;
                } else {
                    $insert->bind_param(
                        "sisssssdddsssssssssssssss",
                        $kode_transaksi, $user_id, $no_hp, $produk_id, $pembayaran,
                        $payment_gateway, $kode_pembayaran, $harga_jual, $biaya_admin, $harga_total,
                        $kontak, $tipe_produk, $status_pembayaran, $status_pengiriman, $ref_id,
                        $status_digiflazz, $digiflazz_response, $callback_response, $json_invoice, $message,
                        $ip_address, $user_agent, $expired_at, $paid_at, $updated_at
                    );

                    if ($insert->execute()) {
                        $order_id = $conn->insert_id;
                        $encrypted_order_id = encrypt($order_id);

                        $_SESSION['popup_success'] = "Pesanan berhasil! Silahkan lakukan pembayaran.";
                        header("Location: " . $web_url . "/invoice.php?order_id=" . $encrypted_order_id);
                        exit;
                    } else {
                        $_SESSION['popup_error'] = "Gagal menyimpan pesanan: " . $insert->error;
                    }

                    $insert->close();
                }
        } else {
            $_SESSION['popup_error'] = "Produk tidak ditemukan atau tidak aktif.";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


require '../lib/header.php';

$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';

$stmt = $conn->prepare("
    SELECT id, nama_produk, kategori, brand, tipe, deskripsi, logo, tipe_diskon, harga_jual, harga_diskon, harga_reseller, status, expired_diskon
    FROM layanan 
    WHERE kategori = ? 
      AND brand = ? 
      AND status = 'normal'
      AND (
            (tipe_diskon = 'On' AND expired_diskon IS NOT NULL AND expired_diskon != '0000-00-00 00:00:00' AND expired_diskon > NOW())
            OR tipe_diskon != 'On'
          )
    ORDER BY 
    CASE 
        WHEN tipe_diskon = 'On' AND expired_diskon IS NOT NULL AND expired_diskon > NOW() THEN harga_diskon
        ELSE harga_jual
    END ASC
");

$stmt->bind_param("ss", $kategori, $brand);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result(
    $id,
    $nama_produk,
    $kategori_db,
    $brand_db,
    $tipe,
    $deskripsi,
    $logo,
    $tipe_diskon,
    $harga_jual,
    $harga_diskon,
    $harga_reseller,
    $status,
    $expired_diskon
);

// ✅ Tambahkan di sini: Ambil data metode pembayaran untuk script JS
$metodeQuery = $conn->query("SELECT * FROM metode_pembayaran WHERE status='Aktif' ORDER BY nama ASC");

// Siapkan array berdasarkan metode
$rawMetodeList = [];
while ($row = $metodeQuery->fetch_assoc()) {
    $rawMetodeList[$row['metode']][] = $row;
}

// Urutan kelompok yang diutamakan
$urutanMetode = ['QRIS', 'E-Wallet', 'Virtual Account', 'Convenience Store'];

// Susun $metodeList
$metodeList = [];
foreach ($urutanMetode as $urutan) {
    if (isset($rawMetodeList[$urutan])) {
        $metodeList[$urutan] = $rawMetodeList[$urutan];
    }
}
// Tambahkan metode lain yang belum ditambahkan
foreach ($rawMetodeList as $metode => $items) {
    if (!in_array($metode, $urutanMetode)) {
        $metodeList[$metode] = $items;
    }
}
?>
<div class="section-container py-4 px-3">
    <div class="row g-4 align-items-start">

        <!-- Kolom kiri: Gambar + Judul + Konten -->
        <div class="col-lg-6">
            <div class="bg-custom-card">
                <img src="<?php echo $web_url; ?>/assets/img/beli-apa-aja.png" alt="Gambar Layanan" class="img-fluid w-100 custom-rounded-top mb-3">
                <div id="konten-layanan" class="intruksi-order">
                    <h2 class="h5 mb-2" id="judul-layanan">Layanan: Aktivasi Perdana</h2>
                    <p>Berikut adalah langkah-langkah untuk melakukan pemesanan:</p>
                    <ol>
                        <li>Masukkan nomor tujuan</li>
                        <li>Pilih produk</li>
                        <li>Pilih metode pembayaran</li>
                        <li>Masukkan kontak/email aktif</li>
                        <li>Klik tombol pesan sekarang</li>
                    </ol>
                </div>
            </div>
        </div>

        <script>
            const metodePembayaran = <?= json_encode(array_map(function ($row) {
                return [
                    'kode' => strtolower($row['kode']),
                    'biaya' => (float)$row['biaya'],
                    'percent_biaya' => $row['percent_biaya'],
                    'tambahan_biaya' => (float)$row['tambahan_biaya'],
                    'tipe_tambahan' => $row['tipe_tambahan']
                ];
            }, is_array($metodeList) ? (
                    isset($metodeList[array_key_first($metodeList)]) && is_array($metodeList[array_key_first($metodeList)])
                        ? array_merge(...array_values($metodeList))
                        : $metodeList
                ) : [])) ?>;
        </script>


        <!-- Kolom kanan: Form -->
        <div class="col-lg-6">
            <div class="p-4 bg-white rounded shadow-sm">
                <div class="judul-pemesanan bg-custom-card">
                    <h5 class="mb-0">Buat Pemesanan</h5>
                </div>
                <form id="orderForm" method="POST">
                    <div class="mb-3 top-20">
                        <label for="noHp" class="form-label text-dark"><b>1.Nomor Tujuan:</b></label>
                        <input type="tel" class="form-control" name="noHp" id="noHp" placeholder="0812xxxxxxx" pattern="08[0-9]{8,11}" maxlength="13" required>
                        <small id="hpError" class="text-danger d-none">Nomor harus diawali 08 dan hanya angka.</small>
                    </div>

                    <!-- Produk -->
                    <div class="mb-3">
                        <label class="form-label"><b>2.Pilih Produk</b></label>
                        <span id="notsProduct"></span>
                        <span id="productNorsult"></span>
                        <div class="option-group" id="produkOptions">
                          <?php while ($stmt->fetch()): ?>
                                <?php
                                $diskon_persen = 0;
                                $tampilkan_diskon = false;
                            
                                // Validasi diskon
                                if ($tipe_diskon === 'On' && $harga_diskon > 0 && $harga_diskon < $harga_jual) {
                                    $selisih = $harga_jual - $harga_diskon;
                                    $persen = ($selisih / $harga_jual) * 100;
                                    $diskon_persen = ($persen < 1) ? 1 : round($persen);
                                    $tampilkan_diskon = true;
                                }
                            
                                $harga_final = $tampilkan_diskon ? $harga_diskon : $harga_jual;
                                ?>
                            
                                <div class="option-card position-relative" data-value="<?= htmlspecialchars($id) ?>">
                                    <?php if ($tampilkan_diskon): ?>
                                        <span class="badge bg-danger position-absolute end-0 top-0 m-1">-<?= ($diskon_persen == 0) ? 1 : $diskon_persen ?>%</span>
                                    <?php endif; ?>
                            
                                    <span class="checkmark">✔</span>
                                    <img src="<?= htmlspecialchars($logo ?: $web_url.'/assets/img/produk/new-product.png') ?>" alt="<?= htmlspecialchars($brand_db) ?>">
                            
                                    <div class="info">
                                        <div class="nama">
                                            <div class="kategori" hidden><?= htmlspecialchars($kategori_db) ?></div>
                                            <div class="produk"><?= ucwords(htmlspecialchars($nama_produk)) ?> | <?= htmlspecialchars($deskripsi) ?></div>
                                        </div>
                            
                                        <div class="harga mt-1" data-harga="<?= $harga_final ?>">
                                            <?php if ($tampilkan_diskon): ?>
                                                <span class="text-muted text-decoration-line-through small d-block">
                                                    Rp <?= number_format($harga_jual, 0, ',', '.') ?>
                                                </span>
                                                <span class="text-danger fw-bold">
                                                    Rp <?= number_format($harga_diskon, 0, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="fw-bold">Rp <?= number_format($harga_jual, 0, ',', '.') ?></span>
                                                <?php if ($status === 'gangguan'): ?>
                                                    <small class="text-danger"><?= ucfirst(htmlspecialchars($status)) ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <input type="hidden" name="produk" id="produkInput" required>
                    </div>


                    <?php
                    // Ambil semua metode pembayaran aktif
                    $metodeQuery = $conn->query("SELECT * FROM metode_pembayaran WHERE status='Aktif' ORDER BY nama ASC");

                    // Siapkan array kelompok
                    $rawMetodeList = [];
                    while ($row = $metodeQuery->fetch_assoc()) {
                        $rawMetodeList[$row['metode']][] = $row;
                    }

                    // Urutan metode yang diinginkan
                    $urutanMetode = ['QRIS', 'E-Wallet', 'Virtual Account', 'Convenience Store'];

                    // Susun berdasarkan urutan
                    $metodeList = [];
                    foreach ($urutanMetode as $urutan) {
                        if (isset($rawMetodeList[$urutan])) {
                            $metodeList[$urutan] = $rawMetodeList[$urutan];
                        }
                    }

                    // Jika ada metode lain yang belum ditampilkan, taruh di akhir
                    foreach ($rawMetodeList as $metode => $items) {
                        if (!in_array($metode, $urutanMetode)) {
                            $metodeList[$metode] = $items;
                        }
                    }
                    ?>

                    <div class="mb-3">
                        <label class="form-label"><b>3.Pilih Pembayaran:</b> </label>
                        <small id="selectedPaymentName" class="text-primary fw-semibold">Belum dipilih</small>
                        <div class="accordion" id="accordionPembayaran">
                            
                            <?php if (isset($_SESSION['user_id'])): 
                                $user_id = $_SESSION['user_id'];
                                $stmtSaldo = $conn->prepare("SELECT saldo FROM users WHERE id = ?");
                                $stmtSaldo->bind_param("i", $user_id);
                                $stmtSaldo->execute();
                                $stmtSaldo->bind_result($saldoUser);
                                $stmtSaldo->fetch();
                                $stmtSaldo->close();
                            ?>
                            <!-- Opsi Pembayaran via Saldo Akun -->
                            <div class="metode-saldo mb-3">
                                <div class="payment-option border rounded p-2 d-flex align-items-center gap-3" data-value="SALDO" style="cursor:pointer;">
                                    <span class="checkmark">✔</span>
                                    <!--<img src="/assets/img/saldo-icon.png" alt="Saldo" style="width: 32px; height: 32px;">-->
                                    <i class="fa-solid fa-wallet fa-2x text-primary"></i>
                                    <div>
                                        <div class="label text-muted">Saldo Akun Rp <?= number_format($saldoUser, 0, ',', '.') ?></div>
                                        <div class="desc" hidden style="display: none;">Saldo</div>
                                        <div class="harga-total text-success fw-bold small mt-1" id="hargaTotal_SALDO"></div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php
                            foreach ($metodeList as $group => $items):
                                $collapseId = strtolower(str_replace(' ', '', $group)) . "Collapse";
                                $headingId = strtolower(str_replace(' ', '', $group)) . "Heading";
                                 $isActive = ($group === 'QRIS'); 
                            ?>
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="<?= $headingId ?>">
                                    <button class="accordion-button <?= $isActive ? '' : 'collapsed' ?>" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>">
                                        <?= htmlspecialchars($group) ?>
                                    </button>
                                </h2>
                                <div id="<?= $collapseId ?>"
                                     class="accordion-collapse collapse <?= $isActive ? 'show' : '' ?>"
                                     data-bs-parent="#accordionPembayaran">
                                      <div class="accordion-body">
                                            <?php foreach ($items as $item): ?>
                                                <div class="payment-option" data-value="<?= htmlspecialchars($item['kode']) ?>">
                                                    <span class="checkmark">✔</span>
                                                    <img src="<?= htmlspecialchars($item['logo']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>">
                                                    <div class="label text-muted"><?= htmlspecialchars(trim(preg_replace('/Virtual[-\s]?Account/i', '', $item['nama']))) ?></div>
                                                    <div class="desc" hidden style="display: none;"><?= htmlspecialchars($group) ?></div>
                                                </div>
                                                <div class="harga-total text-success fw-bold small mt-1" id="hargaTotal_<?= htmlspecialchars($item['kode']) ?>"></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="pembayaran" id="pembayaranInput" required>
                    </div>



                    <div class="mb-3">
                        <label for="kontak" class="form-label"><b>4.WhatsApp/Handphone</b></label>
                        <input type="tel" class="form-control" id="kontak" name="kontak" placeholder="08xxxxx" pattern="08[0-9]{8,11}" maxlength="13" required>
                    </div>

                    <button type="submit" id="btnPesanSekarang" class="btn btn-custom-primary w-100">Pesan Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const operatorMap = {
        "0811": "telkomsel",
        "0812": "telkomsel",
        "0813": "telkomsel",
        "0821": "telkomsel",
        "0822": "telkomsel",
        "0823": "telkomsel",
        "0852": "telkomsel",
        "0853": "telkomsel",
        "0814": "indosat",
        "0815": "indosat",
        "0816": "indosat",
        "0855": "indosat",
        "0856": "indosat",
        "0857": "indosat",
        "0858": "indosat",
        "0817": "xl",
        "0818": "xl",
        "0819": "xl",
        "0859": "xl",
        "0877": "xl",
        "0878": "xl",
        "0831": "axis",
        "0832": "axis",
        "0833": "axis",
        "0838": "axis",
        "0895": "tri",
        "0896": "tri",
        "0897": "tri",
        "0898": "tri",
        "0899": "tri",
        "0881": "smartfren",
        "0882": "smartfren",
        "0883": "smartfren",
        "0884": "smartfren",
        "0885": "smartfren",
        "0886": "smartfren",
        "0887": "smartfren",
        "0888": "smartfren",
        "0889": "smartfren"
    };

    const byuPrefixes = ["085155", "085156", "085157", "085158", "085159", "085198"];
    let lastOperator = '';

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.getElementById("noHp").addEventListener("input", debounce(function() {
        const nomor = this.value.trim().replace(/\D/g, "");
        const nots = document.getElementById("notsProduct");
        const container = document.getElementById("produkOptions");
        const productNorsult = document.getElementById("productNorsult");

        if (nomor.length === 0) {
            lastOperator = '';
            container.innerHTML = "";
            nots.innerHTML = "";
            productNorsult.innerHTML = "<p class='text-muted text-center'>Masukkan nomor handphone terlebih dahulu.</p>";

            // ✅ Tambahan: reset input produk & pembayaran
            document.getElementById('produkInput').value = '';
            document.getElementById('pembayaranInput').value = '';
            document.getElementById('selectedPaymentName').textContent = 'Belum dipilih';
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.harga-total').forEach(el => el.remove());

            return;
        }

        if (nomor.length < 4 || nomor.length > 13) {
            lastOperator = '';
            container.innerHTML = "";
            productNorsult.innerHTML = "";
            nots.innerHTML = "<p class='text-danger text-center'>Pastikan nomor memiliki 10–13 digit.</p>";
            return;
        }

        const prefix6 = nomor.substring(0, 6);
        const prefix5 = nomor.substring(0, 5);
        const prefix4 = nomor.substring(0, 4);
        let operator = "";

        if (byuPrefixes.includes(prefix6)) {
            operator = "by.u";
        } else if (prefix5 === "0851") {
            operator = "telkomsel";
        } else {
            operator = operatorMap[prefix5] || operatorMap[prefix4] || "";
        }

        if (operator && operator !== lastOperator) {
            lastOperator = operator;
            nots.innerHTML = "";
            productNorsult.innerHTML = "";
            fetchProduk('aktivasi', operator);
        } else if (!operator) {
            lastOperator = '';
            container.innerHTML = "";
            productNorsult.innerHTML = "";
            nots.innerHTML = "<p class='text-danger text-center'>Produk tidak ditemukan atau nomor tidak valid.</p>";

            // ✅ Tambahan: reset input produk & pembayaran
            document.getElementById('produkInput').value = '';
            document.getElementById('pembayaranInput').value = '';
            document.getElementById('selectedPaymentName').textContent = 'Belum dipilih';
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.harga-total').forEach(el => el.remove());
        }
    }, 500));

    function fetchProduk(kategori, brand) {
    const nots = document.getElementById("notsProduct");
    const productNorsult = document.getElementById("productNorsult");
    const container = document.getElementById("produkOptions");

    fetch(`<?php echo $web_url; ?>/ajax/get_produk_by_operator.php?kategori=${kategori}&brand=${brand}`)
        .then(res => res.json())
        .then(produkList => {
            container.innerHTML = "";
            nots.innerHTML = "";
            productNorsult.innerHTML = "";

            // ✅ Tambahan perbaikan reset
            document.getElementById('produkInput').value = '';
            document.getElementById('pembayaranInput').value = '';
            document.getElementById('selectedPaymentName').textContent = 'Belum dipilih';
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.harga-total').forEach(el => el.remove());

            if (!produkList.length) {
                nots.innerHTML = "<p class='text-danger text-center'>Produk tidak ditemukan.</p>";
                return;
            }

            produkList.forEach(produk => {
                const diskon = produk.tipe_diskon === 'On' && produk.harga_diskon < produk.harga_jual;
                const hitungDiskon = diskon ? Math.round(((produk.harga_jual - produk.harga_diskon) / produk.harga_jual) * 100) : 0;
                const persenDiskon = (hitungDiskon === 0) ? 1 : hitungDiskon;
            
                // Tentukan harga final untuk dipakai di data-harga
                const hargaFinal = diskon ? produk.harga_diskon : produk.harga_jual;
            
                const produkHTML = `
                    <div class="option-card position-relative" data-value="${produk.id}">
                        ${diskon ? `<span class="badge bg-danger position-absolute end-0 top-0 m-1">-${persenDiskon}%</span>` : ''}
                        <span class="checkmark">✔</span>
                        <img src="${produk.logo || '<?php echo $web_url; ?>/assets/img/produk/new-product.png'}" alt="${produk.brand}">
                        <div class="info">
                            <div class="nama">
                                <div class="kategori" hidden>${produk.kategori}</div>
                                <div class="produk">${produk.nama_produk.charAt(0).toUpperCase() + produk.nama_produk.slice(1)} |  ${produk.deskripsi}</div>
                            </div>
                            <div class="harga mt-1" data-harga="${hargaFinal}">
                                ${diskon
                                    ? `<span class="text-muted text-decoration-line-through small d-block">Rp ${produk.harga_jual.toLocaleString('id-ID')}</span>
                                       <span class="text-danger fw-bold">Rp ${produk.harga_diskon.toLocaleString('id-ID')}</span>`
                                    : `<span class="fw-bold">Rp ${produk.harga_jual.toLocaleString('id-ID')}</span>`}
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', produkHTML);
            });


            bindProdukCardClick();
        })
        .catch(err => {
            container.innerHTML = "";
            nots.innerHTML = "<p class='text-danger text-center'>Gagal mengambil data produk.</p>";
            console.error("Gagal ambil produk:", err);
        });
}

    function bindProdukCardClick() {
        const produkCards = document.querySelectorAll('#produkOptions .option-card');
        const produkInput = document.getElementById('produkInput');

        produkCards.forEach(card => {
            card.addEventListener('click', () => {
                // Reset selected produk
                produkCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                produkInput.value = card.dataset.value;

                // ✅ Reset metode pembayaran
                document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
                document.getElementById('pembayaranInput').value = '';
                document.getElementById('selectedPaymentName').textContent = 'Belum dipilih';

                // ✅ Hapus harga total yang sebelumnya muncul
                document.querySelectorAll('.harga-total').forEach(el => el.remove());

                // Hitung ulang harga total untuk produk yang baru dipilih
                hitungHargaTotal();
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const produkContainerKosong = document.querySelectorAll('#produkOptions .option-card').length === 0;
        const noQueryBrand = !new URLSearchParams(window.location.search).get('brand');
        const productNorsult = document.getElementById("productNorsult");

        if (produkContainerKosong && noQueryBrand) {
            productNorsult.innerHTML = "<p class='text-muted text-center'>Masukkan nomor handphone terlebih dahulu.</p>";
        } else {
            // Pastikan produk tetap bisa diklik walau dari parameter brand langsung
            bindProdukCardClick();
        }
    });

    // Metode pembayaran
    document.querySelectorAll('.payment-option').forEach(card => {
        card.addEventListener('click', () => {
            const produkDipilih = document.getElementById('produkInput').value;

            if (!produkDipilih) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Silakan pilih produk terlebih dahulu sebelum memilih metode pembayaran.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            document.querySelectorAll('.payment-option').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('pembayaranInput').value = card.dataset.value;

            const labelText = card.querySelector('.label')?.textContent || 'Belum dipilih';
            document.getElementById('selectedPaymentName').textContent = labelText;
        });
    });

    // Validasi submit
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const produkInput = document.getElementById('produkInput');
        const pembayaranInput = document.getElementById('pembayaranInput');

        if (!produkInput.value || !pembayaranInput.value) {
            e.preventDefault();
            Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Pastikan sudah memilih produk dan metode pembayaran sebelum melanjutkan pemesanan.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#3085d6'
                });
                return;
        }
    });

    // Validasi nomor HP: hanya angka & wajib 08
    const noHpInput = document.getElementById('noHp');
    const hpError = document.getElementById('hpError');

    noHpInput.addEventListener('input', function() {
        // Hapus karakter non-angka
        this.value = this.value.replace(/\D/g, '');

        // Validasi: Harus diawali 08
        if (this.value.length > 0 && !this.value.startsWith('08')) {
            hpError.classList.remove('d-none');
        } else {
            hpError.classList.add('d-none');
        }
    });
</script>

<script>
    function hitungHargaTotal() {
    const produkId = document.getElementById('produkInput').value;
    const metodeCards = document.querySelectorAll('.payment-option');

    if (!produkId) return;

    // Hapus semua harga total lama
    document.querySelectorAll('.harga-total').forEach(el => el.remove());

    metodeCards.forEach(card => {
        const code = card.dataset.value;
        const descDiv = card.querySelector('.desc');

        if (!code || !descDiv) return;

        fetch(`<?= $web_url ?>/ajax/get_harga_total.php?produk_id=${produkId}&pembayaran=${code}`)
            .then(res => res.json())
            .then(data => {
                if (!data || typeof data.total !== 'number') return;

                const hargaEl = document.createElement('div');
                hargaEl.className = 'harga-total mt-1 text-muted small';
                hargaEl.id = 'hargaTotal_' + code; // Tambahkan ID unik berdasarkan kode metode
                hargaEl.innerHTML = `<b>Rp ${data.total.toLocaleString('id-ID')}</b>`;
                descDiv.insertAdjacentElement('afterend', hargaEl);
            })
            .catch(err => console.error(`Gagal ambil total untuk metode ${code}:`, err));
    });
}


    // Event: saat produk dipilih
    document.getElementById('produkInput').addEventListener('change', hitungHargaTotal);

    // Event: saat metode pembayaran dipilih
    document.getElementById('pembayaranInput').addEventListener('change', hitungHargaTotal);

    // Tambahan: saat metode pembayaran diklik langsung
    document.querySelectorAll('.payment-option').forEach(el => {
        el.addEventListener('click', () => setTimeout(hitungHargaTotal, 200));
    });
</script>

<script>
    document.getElementById("orderForm").addEventListener("submit", function(e) {
    const noHp = document.getElementById("noHp").value.trim();
    const produk = document.getElementById("produkInput").value.trim();
    const pembayaran = document.getElementById("pembayaranInput").value.trim();
    const kontak = document.getElementById("kontak").value.trim();

    if (!noHp || !produk || !pembayaran || !kontak) {
        Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Pastikan sudah memilih produk dan metode pembayaran sebelum melanjutkan pemesanan.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#3085d6'
                });
                e.preventDefault();
                return;
    }
    
    
});
</script>


<script>
document.getElementById('btnPesanSekarang').addEventListener('click', function(e) {
    e.preventDefault();

    const noHp = document.getElementById('noHp').value.trim();
    const produkId = document.getElementById('produkInput').value;
    const pembayaran = document.getElementById('pembayaranInput').value;
    const kontak = document.getElementById('kontak').value.trim();

    if (!noHp || !produkId || !pembayaran || !kontak) {
        Swal.fire({
            icon: 'warning',
            title: 'Formulir Belum Lengkap',
            text: 'Harap isi semua kolom sebelum melanjutkan.',
        });
        return;
    } 

    // Ambil elemen produk & harga produk
    const produkElemen = document.querySelector(`.option-card[data-value="${produkId}"] .produk`);
    const hargaElemen = document.querySelector(`.option-card[data-value="${produkId}"] .harga`);
    const metodeElemen = document.querySelector(`.payment-option[data-value="${pembayaran}"] .label`);
    const namaProduk = produkElemen?.innerText || 'Produk tidak ditemukan';
    const hargaProduk = hargaElemen?.getAttribute('data-harga') 
    ? 'Rp ' + parseInt(hargaElemen.getAttribute('data-harga')).toLocaleString('id-ID') 
    : '';
    const namaMetode = metodeElemen?.innerText || 'Metode tidak ditemukan';

    // Ambil harga total dari elemen dengan ID unik berdasarkan kode pembayaran
    let hargaTotal = 'Rp 0';
    const hargaTotalElem = document.getElementById('hargaTotal_' + pembayaran);
    if (hargaTotalElem) {
        const match = hargaTotalElem.innerText.match(/Rp\s[\d.,]+/);
        if (match) {
            hargaTotal = match[0];
        }
    }

    // Tampilkan konfirmasi
    Swal.fire({
        title: 'Konfirmasi Pesanan',
        html: `
            <div class="swall-order" style="text-align:left; display: grid; grid-template-columns: max-content 1fr; gap: 6px 10px; font-size: 14px;">
                <div><strong>Nomor HP</strong></div><div>: ${noHp}</div>
                <div><strong>Produk</strong></div><div>: ${namaProduk}</div>
                <div><strong>Harga</strong></div><div>: ${hargaProduk}</div>
                <div><strong>Pembayaran</strong></div><div>: ${namaMetode}</div>
                <div><strong>Total Bayar</strong></div><div>: ${hargaTotal}</div>
                <div><strong>Kontak</strong></div><div>: ${kontak}</div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('orderForm').submit();
        }
    });
});
</script>


<?php require '../lib/footer.php'; ?>