<?php
session_start();
require 'config.php';

// Ambil dan dekripsi order_id terenkripsi dari URL
if (!isset($_GET['order_id']) || !($order_id = decrypt($_GET['order_id']))) {
    $_SESSION['popup_error'] = "ID pesanan tidak valid atau tidak ditemukan.";
    header("Location: " . $web_url . ""); // atau halaman lain yang cocok
    exit;
}

// Ambil data pesanan
$stmt = $conn->prepare("SELECT 
    kode_transaksi, no_hp, produk_id, metode_pembayaran, harga_produk, biaya_admin, harga_total,
    kontak, tipe_produk, status_pembayaran, status_pengiriman, created_at, payment_gateway,
    kode_pembayaran, ref_id, expired_at, json_invoice, digiflazz_response, status_digiflazz
    FROM pemesanan_ppob WHERE id = ? LIMIT 1");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['popup_error'] = "Pesanan tidak ditemukan.";
    header("Location: " . $web_url . ""); // atau halaman lain yang cocok
    exit;
}

$stmt->bind_result(
    $kode_transaksi, $no_hp, $produk_id, $metode_pembayaran, $harga_produk, $biaya_admin, $harga_total,
    $kontak, $tipe_produk, $status_pembayaran, $status_pengiriman, $created_at, $payment_gateway,
    $kode_pembayaran, $ref_id, $expired_at, $json_invoice, $digiflazz_response, $status_digiflazz
);
$stmt->fetch();
$stmt->close();


// Ambil nama produk
$produk_stmt = $conn->prepare("SELECT nama_produk, kategori, kode_produk FROM layanan WHERE id = ? LIMIT 1");
$produk_stmt->bind_param("i", $produk_id);
$produk_stmt->execute();
$produk_stmt->store_result();
$produk_stmt->bind_result($nama_produk, $kategori, $kode_produk);
$produk_stmt->fetch();
$produk_stmt->close();



// Order langsung ke DigiFlazz jika pakai saldo dan belum dikirim
    if ($metode_pembayaran === "SALDO" && $status_pengiriman === 'pending' && $status_pembayaran === 'paid' && $status_digiflazz !== 'sukses') {
        $q = $conn->query("SELECT username, api_key FROM provider WHERE nama = 'digiflazz' AND status = 'active' LIMIT 1");
        if ($q && $q->num_rows > 0) {
            [$api_id, $api_key] = $q->fetch_row();
        }

        $signature = md5($api_id . $api_key . $kode_transaksi);

        $payload = [
            "cmd" => "prepaid",
            "username" => $api_id,
            "buyer_sku_code" => $kode_produk,
            "customer_no" => $no_hp,
            "ref_id" => $kode_transaksi,
            "sign" => $signature
        ];

        $ch = curl_init("https://api.digiflazz.com/v1/transaction");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $digiflazz_response = $response;
        $digiflazz_json = json_decode($response, true);
        $status_kirim = 'proses';

        if (json_last_error() === JSON_ERROR_NONE && isset($digiflazz_json['data']['status'])) {
            if (strtolower($digiflazz_json['data']['status']) === 'sukses') {
                $status_kirim = 'berhasil';
            } elseif (strtolower($digiflazz_json['data']['status']) === 'gagal') {
                $status_kirim = 'gagal';
            }
        }

        // Simpan update ke database
        $stmt = $conn->prepare("UPDATE pemesanan_ppob SET digiflazz_response = ?, status_pengiriman = ?, status_digiflazz = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssi", $digiflazz_response, $status_kirim, $status_kirim, $order_id);
        $stmt->execute();
        $stmt->close();

        $status_pengiriman = $status_kirim;
        $status_digiflazz = $status_kirim;
    }


// Ambil metode pembayaran berdasarkan kode (yang cocok dengan pemesanan_ppob.metode_pembayaran)
$metode_stmt = $conn->prepare("SELECT metode, guide FROM metode_pembayaran WHERE kode = ? LIMIT 1");
$metode_stmt->bind_param("s", $metode_pembayaran);
$metode_stmt->execute();
$metode_stmt->store_result();

$jenis_metode = 'Unknown'; // default
if ($metode_stmt->num_rows > 0) {
    $metode_stmt->bind_result($jenis_metode, $guide);
    $metode_stmt->fetch();
}
$metode_stmt->close();


// Jika belum punya ref_id (belum request invoice ke Sakurupiah)
if ($metode_pembayaran !== "SALDO" && empty($ref_id)) {
    $q = $conn->query("SELECT api_id, api_key FROM payment_gateway WHERE kode = 'sakurupiah' LIMIT 1");

    if ($q && $q->num_rows > 0) {
        [$api_id, $api_key] = $q->fetch_row();
    } else {
        $_SESSION['popup_error'] = "Kesalahan konfigurasi system.";
    }

    $amount = (int) $harga_total;
    $signature = hash_hmac('sha256', $api_id . $metode_pembayaran . $kode_transaksi . $amount, $api_key);

    $data = [
        'api_id'       => $api_id,
        'method'       => $metode_pembayaran,
        'phone'        => $kontak,
        'amount'       => $amount,
        'merchant_fee' => 1,
        'merchant_ref' => $kode_transaksi,
        'expired'      => 1,
        'produk' => array(ucfirst($kategori) . ' - ' . $nama_produk),
        'qty' => array('1'),
        'harga' => array($harga_produk), // gunakan harga diskon jika ada
        'size' => array('-'),
        'note' => array('Pembelian '.$kategori.''),
        'callback_url' => $web_url . '/callback.php?token='.$secret.'',
        'return_url'   => $web_url . '/invoice.php?order_id=' . urlencode($_GET['order_id']),
        'signature'    => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://sakurupiah.id/api/create.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $api_key"]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);

    if ($result && $result['status'] == '200') {
        $dataTrx = $result['data'][0];
        $ref_id           = $dataTrx['trx_id'] ?? null;
        $merchant_ref     = $dataTrx['merchant_ref'] ?? null;
        $qr               = $dataTrx['qr'] ?? null;
        $kode_pembayaran  = $dataTrx['payment_no'] ?? null;
        $expired_at       = $dataTrx['expired'] ?? null;
        $via              = $dataTrx['via'] ?? null;
        
        $payment_no       = $kode_pembayaran;
        
        $update = $conn->prepare("UPDATE pemesanan_ppob SET ref_id = ?, json_invoice = ?, kode_pembayaran = ?, expired_at = ?, updated_at = NOW() WHERE id = ?");
        $update->bind_param("ssssi", $ref_id, $response, $kode_pembayaran, $expired_at, $order_id);
        $update->execute();
        $update->close();
    } else {
        $_SESSION['popup_error'] = "Konfigurasi sistem pembayaran belum siap ."; 
    }

} else {
    // Sudah ada invoice, decode json_invoice untuk ditampilkan
    $kelola_json = json_decode($json_invoice, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $ref_id        = $kelola_json['data'][0]['trx_id'] ?? 'unknown';
        $merchant_ref  = $kelola_json['data'][0]['merchant_ref'] ?? 'unknown';
        $qr            = $kelola_json['data'][0]['qr'] ?? 'unknown';
        $payment_no    = $kelola_json['data'][0]['payment_no'] ?? 'unknown';
        $via           = $kelola_json['data'][0]['via'] ?? 'unknown';
    }
    
    $digiflazz_json = json_decode($digiflazz_response, true);

    $sn_token = 'unknown';
    
    if (json_last_error() === JSON_ERROR_NONE) {
        // Cek apakah ada field `sn`
        if (isset($digiflazz_json['data']['sn']) && !empty($digiflazz_json['data']['sn'])) {
            $sn_token = trim($digiflazz_json['data']['sn']);
        }
        // Jika `sn` tidak ada, coba ambil dari `message`
        elseif (isset($digiflazz_json['data']['message'])) {
            if (preg_match('/SN[\s:]*([0-9\s]+)/i', $digiflazz_json['data']['message'], $match)) {
                $sn_token = trim($match[1]);
            }
        }
    }

}

require 'lib/header.php';
?>

<style>
    .label {
  max-width: 120px; /* atau sesuai layout kamu */
  overflow-wrap: break-word;
  white-space: normal;
}
</style>
<div class="section-container invoice-container top-30">
  <div class="card card-invoices">
    <div class="invoice-header">
      <h2>INVOICE</h2>
      <div class="alert alert-warning text-center text-dark fw-semibold" role="alert">
          Catatan penting: Setiap transaksi Anda, mohon catat <strong>ID Transaksi</strong> untuk pengecekan jangka panjang apabila transaksi Anda mengalami kendala.
        </div>

      <p><small class="text-white text-center">Jika transaksi gagal silahkan hub admin atau cek detail transaksi di menu cek transaksi <a href="<?php echo $web_url; ?>/history" class="badge bg-warning text-dark fw-bold text-decoration-none" target="_blank">DISINI</a> untuk melihat detail informasi transaksi anda. segera hub tim dukungan kami jika mengalami kendala transaksi!</small></p>
    </div>

    <div class="card-body p-4">
      <div class="row mb-3">
        <div class="invoice-columns">
          <div class="invoice-column">
            <div class="invoice-detail">
              <div class="invoice-row">
              <div class="label">ID Transaksi</div>
              <div class="colon">:</div>
              <div class="value d-flex align-items-center">
                <span id="idTransaksi"><?= htmlspecialchars($kode_transaksi) ?></span>
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard()" title="Salin ID">
                  <i class="fas fa-copy text-info"></i>
                </button>
              </div>
            </div>
              <div class="invoice-row">
                <div class="label">Waktu</div>
                <div class="colon">:</div>
                <div class="value"><?= htmlspecialchars($created_at) ?> WIB</div>
              </div>
              <div class="invoice-row">
                <div class="label">Tujuan</div>
                <div class="colon">:</div>
                <div class="value"><?= ucwords(htmlspecialchars($no_hp)) ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Produk</div>
                <div class="colon">:</div>
                <div class="value"><?= ucwords(htmlspecialchars($kategori)) ?> - <?= htmlspecialchars($nama_produk) ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Metode</div>
                <div class="colon">:</div>
                <div class="value"><?= htmlspecialchars($via ?? 'Saldo Akun') ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Note/SN</div>
                <div class="colon">:</div>
                <div class="value"><?= htmlspecialchars($sn_token ?? 'unknown') ?></div>
              </div>
            </div>
          </div>

          <div class="invoice-column">
            <div class="invoice-detail">
              <div class="invoice-row">
                <div class="label">Harga Produk</div>
                <div class="colon">:</div>
                <div class="value">Rp <?= number_format(round($harga_produk), 0, ',', '.') ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Fee Admin</div>
                <div class="colon">:</div>
                <div class="value">Rp <?= number_format(round($biaya_admin), 0, ',', '.') ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Total Bayar</div>
                <div class="colon">:</div>
                <div class="value text-warning">Rp <?= number_format(round($harga_total), 0, ',', '.') ?></div>
              </div>
              <div class="invoice-row">
                <div class="label">Status Pembayaran</div>
                <div class="colon">:</div>
                <div class="value">
                  <?php
                  $statusColor = [
                      'pending' => 'warning',
                      'paid' => 'success',
                      'expired' => 'danger',
                      'failed' => 'secondary'
                  ];
                  $color = $statusColor[$status_pembayaran] ?? 'dark';
                  ?>
                  <span class="text-uppercase badge bg-<?= $color ?>">
                    <?= ucfirst($status_pembayaran) ?>
                  </span>
                </div>
              </div>
              <div class="invoice-row">
                <div class="label">Status Pemesanan</div>
                <div class="colon">:</div>
                <div class="value">
                  <?php
                  $statusPS = [
                      'pending' => 'warning',
                      'berhasil' => 'success',
                      'proses' => 'info',
                      'gagal' => 'danger'
                  ];
                  $color = $statusPS[$status_pengiriman] ?? 'dark';
                  ?>
                  <span class="text-uppercase badge bg-<?= $color ?>">
                    <?= ucfirst($status_pengiriman) ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

          <div class="qr-box">
              <?php if ($jenis_metode === 'QRIS' or $jenis_metode === 'QRIS2'): ?>
                <p><strong>Scan QR untuk Pembayaran:</strong></p>
                <img src="<?= htmlspecialchars($qr ?? 'Unknown') ?>" alt="QR Code"><br>
                
                
                <!--ACORDION-->
                <div class="accordion mt-3" id="accordionPanduanQRIS">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPanduanQR">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePanduanQR" aria-expanded="false" aria-controls="collapsePanduanQR">
                        Panduan Pembayaran
                      </button>
                    </h2>
                    <div id="collapsePanduanQR" class="accordion-collapse collapse" aria-labelledby="headingPanduanQR" data-bs-parent="#accordionPanduanQRIS">
                      <div class="accordion-body text-start">
                         <?= nl2br(htmlspecialchars($guide ?? 'Tidak Di Menemukan Cara Pembayaran')) ?>
                      </div>
                    </div>
                  </div>
                </div>


              <?php elseif ($jenis_metode === 'E-Wallet'): ?>
                    <a href="<?= htmlspecialchars($payment_no ?? '/') ?>" class="btn btn-custom-primary mt-4" target="_blank">Bayar Sekarang</a>

                                <!--ACORDION-->
                <div class="accordion mt-3" id="accordionPanduanQRIS">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPanduanQR">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePanduanQR" aria-expanded="false" aria-controls="collapsePanduanQR">
                        Panduan Pembayaran
                      </button>
                    </h2>
                    <div id="collapsePanduanQR" class="accordion-collapse collapse" aria-labelledby="headingPanduanQR" data-bs-parent="#accordionPanduanQRIS">
                      <div class="accordion-body text-start">
                         <?= nl2br(htmlspecialchars($guide ?? 'Tidak Di Menemukan Cara Pembayaran')) ?>
                      </div>
                    </div>
                  </div>
                </div>
                
                <?php elseif ($jenis_metode === 'Virtual-Account' || $jenis_metode === 'Convenience Store'): ?>
                <p><strong>Kode Pembayaran Anda:</strong></p>
                <div class="payment-code-box bg-light p-3 rounded text-center">
                  <strong><?= htmlspecialchars($payment_no ?? '-') ?></strong>
                </div>
                 
                                 <!--ACORDION-->
                <div class="accordion mt-3" id="accordionPanduanQRIS">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPanduanQR">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePanduanQR" aria-expanded="false" aria-controls="collapsePanduanQR">
                        Panduan Pembayaran
                      </button>
                    </h2>
                    <div id="collapsePanduanQR" class="accordion-collapse collapse" aria-labelledby="headingPanduanQR" data-bs-parent="#accordionPanduanQRIS">
                      <div class="accordion-body text-start">
                         <?= nl2br(htmlspecialchars($guide ?? 'Tidak Di Menemukan Cara Pembayaran')) ?>
                      </div>
                    </div>
                  </div>
                </div>
            
              <?php else: ?>
                <!--<p class="text-danger">Jenis metode pembayaran tidak dikenali.</p>-->
              <?php endif; ?>
            </div>

        
      </div>
    </div>
  </div>
</div>

<script>
  function copyToClipboard() {
    const text = document.getElementById('idTransaksi').innerText;
    navigator.clipboard.writeText(text).then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'ID Transaksi berhasil disalin ke clipboard!',
        timer: 2000,
        showConfirmButton: false
      });
    }).catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Gagal menyalin ID Transaksi.',
      });
    });
  }
</script>

<?php 
require 'lib/footer.php';
?>
