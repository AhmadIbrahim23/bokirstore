<?php
session_start(); 
require 'config.php';
require 'lib/header.php';

// Ambil layanan flash sale yang belum expired
$flashQuery = $conn->query("SELECT * FROM layanan 
    WHERE tipe_diskon = 'On' 
    AND status = 'normal'
    AND expired_diskon IS NOT NULL 
    AND expired_diskon != '0000-00-00 00:00:00' 
    AND expired_diskon > NOW()
");

$produkFlash = [];
$expiredWaktu = null;

if ($flashQuery && $flashQuery->num_rows > 0) {
    while ($flash = $flashQuery->fetch_assoc()) {
        $produkFlash[] = $flash;
    }

    // Ambil expired_diskon paling lama
    $expiredWaktu = max(array_column($produkFlash, 'expired_diskon'));
} else {
    $expiredWaktu = date('Y-m-d H:i:s', time() - 3600); // fallback expired
}

$expiredISO = date('c', strtotime($expiredWaktu));
?>

        
<!-- SLIDER -->
<div class="section-container mt-3">
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner rounded-3 shadow-sm">
            <?php
            // Ambil semua banner untuk slider, urutkan berdasarkan id DESC
            $query = "SELECT * FROM banner ORDER BY id DESC";
            $result = $conn->query($query);
            
            // Penanda hanya item pertama yang aktif
            $isFirst = true;
            
            while ($row = $result->fetch_assoc()):
            ?>
                <div class="carousel-item <?= $isFirst ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($row['slider']) ?>" class="d-block w-100" alt="banner">
                </div>
            <?php
            $isFirst = false; // setelah item pertama, set false agar selanjutnya tidak aktif
            endwhile;
            ?>
        </div>
    </div>
</div>
<?php if (count($produkFlash) > 0): ?>
<!-- FLASH SALE -->
<div class="section-container" id="flashSaleSection">
    <div class="flash-sale-header">
        <h2 class="section-title flash-sale-title">
            <img src="https://img.icons8.com/fluency/24/flash-on.png" alt="⚡" style="margin-right: 6px; vertical-align: middle;">
            <span class="text-flase-sale">Flash Sale</span>
        </h2>
        <div class="countdown-box" id="flashCountdown">
            <div class="time-segment" id="jam">00<span class="label">jam</span></div>
            <div class="time-segment" id="menit">00<span class="label">menit</span></div>
            <div class="time-segment" id="detik">00<span class="label">detik</span></div>
        </div>
    </div>

    <div class="flash-sale-scroll">
        <?php foreach ($produkFlash as $produk): 
            $harga_asli = (int)$produk['harga_jual'];
            $harga_diskon = (int)$produk['harga_diskon'];
    
            // Jangan tampilkan jika harga diskon >= harga asli
            if ($harga_diskon >= $harga_asli) {
                continue; // skip produk ini
            }
    
            $produk_id = $produk['id'];
            $expired_produk = date('c', strtotime($produk['expired_diskon']));
            $brand = htmlspecialchars($produk['brand']);
            
            $kategori = htmlspecialchars($produk['kategori']);
            $slug = strtolower(preg_replace('/[^a-z0-9]/', '', $kategori));
            
            $targetUrl = "".$web_url."/order/{$slug}.php?kategori=" . urlencode($kategori) . "&brand=" . urlencode($brand);
            
            // Hitung selisih dan diskon persen
            $selisih = $harga_asli - $harga_diskon;
            $diskon_persen = 0;
    
            if ($selisih >= 1) {
                $persen = ($selisih / $harga_asli) * 100;
                $diskon_persen = ($persen < 1) ? 1 : round($persen);
            } else {
                continue; // skip jika selisih kurang dari 1
            }
        ?>
        <a href="<?= $targetUrl; ?>" class="flash-card text-decoration-none text-dark"  data-expired="<?= $expired_produk ?>">
            <div class="discount-ribbon"><?= $diskon_persen ?>%</div>
    
            <div class="card-content">
                <img src="<?= htmlspecialchars($produk['logo'] ?: 'https://img.icons8.com/fluency/48/flash-on.png') ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" />
                <div class="box-flase">
                    <div class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></div>
                <p class="old-price">Rp <?= number_format($harga_asli, 0, ',', '.') ?></p>
                <p class="new-price">Rp <?= number_format($harga_diskon, 0, ',', '.') ?></p>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>


</div>

<script>
const flashSaleEnd = new Date("<?= $expiredISO ?>");

const countdownInterval = setInterval(() => {
    const now = new Date();
    const diff = flashSaleEnd - now;

    if (diff <= 0) {
        clearInterval(countdownInterval);
        document.getElementById("flashSaleSection").style.display = "none";
        return;
    }

    const jam = Math.floor(diff / (1000 * 60 * 60));
    const menit = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const detik = Math.floor((diff % (1000 * 60)) / 1000);

    document.getElementById("jam").innerHTML = jam.toString().padStart(2, '0') + "<span class='label text-white'>jam</span>";
    document.getElementById("menit").innerHTML = menit.toString().padStart(2, '0') + "<span class='label text-white'>menit</span>";
    document.getElementById("detik").innerHTML = detik.toString().padStart(2, '0') + "<span class='label text-white'>detik</span>";
}, 1000);

// Auto hide produk yang expired sendiri-sendiri
setInterval(() => {
    const now = new Date();
    const flashCards = document.querySelectorAll('.flash-card');
    flashCards.forEach(card => {
        const expiredAt = new Date(card.dataset.expired);
        if (now > expiredAt) {
            card.remove();
        }
    });

    // Cek kalau semua produk sudah hilang, maka sembunyikan section
    if (document.querySelectorAll('.flash-card').length === 0) {
        const section = document.getElementById("flashSaleSection");
        if (section) section.style.display = "none";
    }
}, 30000); // cek setiap 30 detik
</script>
<?php endif; ?>



<!-- PRODUK -->
<div class="section-container">
    <div class="section-title-wrapper d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Layanan Populer</h2>
        <!--<a href="#" class="selengkapnya-link">Selengkapnya
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 1 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
            </svg>
        </a>-->
    </div>
    <div class="row g-3 product-row">

        <!--START PRODUCK-->
        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/pulsa" class="text-decoration-none text-dark">
                <div class="product-card h-100">
                    <img src="https://img.icons8.com/fluency/48/phone.png" alt="Pulsa">
                    <div>Pulsa</div>
                </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/paketdata" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="https://img.icons8.com/fluency/48/wifi.png" alt="Data">
                <div>Paket Data</div>
            </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/pln" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="https://img.icons8.com/fluency/48/light-on.png" alt="Token PLN">
                <div>Token PLN</div>
            </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/emoney" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="https://img.icons8.com/fluency/48/wallet.png" alt="E-monney">
                <div>E-monney</div>
            </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/voucher" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="https://img.icons8.com/fluency/48/gift-card.png" alt="Voucher">
                <div>Voucher</div>
            </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/teleponsms" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="<?php echo $web_url; ?>/assets/img/layanan/tlp-sms.png" alt="Tlp&Sms">
                <div>Tlp&Sms</div>
            </div>
            </a>
        </div>
        
        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/masaaktif" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="<?php echo $web_url; ?>/assets/img/layanan/sim-card.png" alt="Masa Aktif">
                <div>Masa Aktif</div>
            </div>
            </a>
        </div>
        
        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/aktivasi" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="<?php echo $web_url; ?>/assets/img/layanan/aktivasi-perdana1.png" alt="Aktivasi Perdana">
                <div>Aktivasi Perdana</div>
            </div>
            </a>
        </div>

        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/tv" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="<?php echo $web_url; ?>/assets/img/layanan/tv-logo-free-vector.jpg" alt="TV">
                <div>TV</div>
            </div>
            </a>
        </div>
        
        <div class="col-3 col-lg-2">
            <a href="<?php echo $web_url; ?>/order/lainnya" class="text-decoration-none text-dark">
            <div class="product-card h-100">
                <img src="<?php echo $web_url; ?>/assets/img/layanan/streaming.jpg" alt="Streaming">
                <div>Streaming</div>
            </div>
            </a>
        </div>
        <!-- END PRODUK -->

    </div>
</div>


<!-- GAMES -->
<div class="section-container">
    <div class="section-title-wrapper d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Topup Games</h2>
        <a href="<?php echo $web_url; ?>/order/game" class="selengkapnya-link">Selengkapnya
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 1 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
            </svg>
        </a>
    </div>

    <div class="row g-3 product-row">
        
        
        <?php
        $brandList = [];
        $games = $conn->prepare("SELECT nama_produk, kategori, brand, logo FROM layanan WHERE kategori = 'game' AND status = 'normal'");
        $games->execute();
        $games->store_result();
        $games->bind_result($nama_produk, $kategori, $brand, $logo);
        
        $brandSeen = [];
        $counter = 0;
        $maxLimit = 36;
        
        while ($games->fetch()) {
            if ($counter >= $maxLimit) break; // ✅ Batasi sampai 24 brand
        
            $brandTrimmed = trim($brand);
            $brandLower = strtolower($brandTrimmed);
        
            // Hindari duplikat
            if (in_array($brandLower, $brandSeen)) continue;
        
            // Simpan brand
            $brandList[] = [
                'nama_produk' => $nama_produk,
                'kategori' => $kategori,
                'brand' => $brandTrimmed,
                'logo' => $logo
            ];
        
            $brandSeen[] = $brandLower;
            $counter++; // ✅ Tambah hitungan saat berhasil dimasukkan
        }
        
        $games->close();
        ?>

        
        <?php foreach ($brandList as $game): ?>
        <?php
        $brand = htmlspecialchars($game['brand']);
            
        $kategori = htmlspecialchars($game['kategori']);
        $slug = strtolower(preg_replace('/[^a-z0-9]/', '', $kategori));
            
        $targetUrl = "".$web_url."/order/{$slug}.php?kategori=" . urlencode($kategori) . "&brand=" . urlencode($brand);
        ?>
        <div class="col-3 col-lg-2">
            
            <div class="game-card h-100 clickable-card" data-href="<?= $targetUrl; ?>">
                <div class="shine"></div>
                <img src="<?= htmlspecialchars($game['logo'] ?: 'https://via.placeholder.com/150') ?>" alt="<?= htmlspecialchars($game['nama_produk']) ?>">
                <div class="card-text"><?= htmlspecialchars(ucwords(strtolower($game['brand']))) ?></div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>



<!-- SOCIAL MEDIA -->
<div class="section-container">
    <div class="section-title-wrapper d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Social Media</h2>
        <!--<a href="#" class="selengkapnya-link">Selengkapnya
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 1 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
            </svg>
        </a>-->
    </div>

    <div class="row g-3">
        <div class="col-3 col-lg-2">
            <div class="social-card h-100">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
            </div>
        </div>
        <div class="col-3 col-lg-2">
            <div class="social-card h-100">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Instagram">
            </div>
        </div>
        <div class="col-3 col-lg-2">
            <div class="social-card h-100">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733635.png" alt="Twitter">
            </div>
        </div>
        <div class="col-3 col-lg-2">
            <div class="social-card h-100">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733646.png" alt="YouTube">
            </div>
        </div>
        <div class="col-3 col-lg-2">
            <div class="social-card h-100">
                <img src="https://cdn-icons-png.flaticon.com/512/3670/3670125.png" alt="TikTok">
            </div>
        </div>
    </div>
</div>


<!-- BLOG SECTION -->
<div class="section-container">
    <h2 class="section-title text-center mb-3"><b>Informasi Terbaru</b></h2>

    <div class="blog-scroll-wrapper">
        
        
        <?php
        // Ambil data blog
        $query = $conn->query("SELECT id, judul, konten, logo FROM blog ORDER BY waktu_dibuat DESC");
        while ($row = $query->fetch_assoc()):
            $id     = (int) $row['id'];
            $judul  = htmlspecialchars($row['judul']);
            $konten = htmlspecialchars($row['konten']);
            $logo   = htmlspecialchars($row['logo']);
        
            // Potong judul jika lebih dari 50 karakter
            if (mb_strlen($judul) > 50) {
                $judul = mb_substr($judul, 0, 50) . '...';
            }
        
            // Potong konten jika lebih dari 100 karakter
            if (mb_strlen($konten) > 100) {
                $konten = mb_substr($konten, 0, 100) . '...';
            }
        
            // Enkripsi ID untuk URL
            $id_encrypted = encrypt($id);
        ?>
        
        <div class="blog-card">
            <a href="<?= $web_url; ?>/blog/index.php?id=<?= $id_encrypted ?>" class="text-decoration-none">
                <img src="<?= $web_url; ?>/blog/img/<?= $logo ?>" alt="<?= $judul ?>">
                <div class="p-2">
                    <div class="blog-title text-white"><?= $judul ?></div>
                    <div class="blog-content text-white"><?= $konten ?></div>
                </div>
            </a>
        </div>
        
        <?php endwhile; ?>



    </div>
</div>


<!-- Popup News HTML -->
<div id="popupBerita" class="news-popup" style="display: none;">
  <div class="news-popup-content">
    <button class="news-popup-close" id="closePopup">&times;</button>
    <img src="<?php echo $web_url; ?>/assets/img/bannner-berita1.png" alt="Berita Terbaru">
    <label class="news-popup-checkbox">
      <input type="checkbox" id="dontShowAgain">
      Jangan tampilkan lagi
    </label>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const popup = document.getElementById('popupBerita');
  const closeBtn = document.getElementById('closePopup');
  const dontShowCheckbox = document.getElementById('dontShowAgain');

  // Tampilkan hanya jika belum diceklis dan disimpan di sessionStorage
  if (!sessionStorage.getItem('popup_dismissed')) {
    popup.style.display = 'flex';
  }

  // Tutup popup
  closeBtn.addEventListener('click', () => {
    if (dontShowCheckbox.checked) {
      sessionStorage.setItem('popup_dismissed', 'true');
    }
    popup.style.display = 'none';
  });

  // Klik di luar konten = close
  popup.addEventListener('click', (e) => {
    if (e.target === popup) {
      if (dontShowCheckbox.checked) {
        sessionStorage.setItem('popup_dismissed', 'true');
      }
      popup.style.display = 'none';
    }
  });
});
</script>

<?php
require 'lib/footer.php';
?>