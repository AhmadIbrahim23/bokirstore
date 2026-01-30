
<?php if (isset($_SESSION['popup_error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops!',
    text: '<?= addslashes($_SESSION["popup_error"]) ?>',
    confirmButtonText: 'Tutup',
    confirmButtonColor: '#d33'
});
</script>
<?php unset($_SESSION['popup_error']); endif; ?>

<?php if (isset($_SESSION['popup_success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= addslashes($_SESSION["popup_success"]) ?>',
    timer: 3000,
    showConfirmButton: false
});
</script>
<?php unset($_SESSION['popup_success']); endif; ?>

</main>
<footer class="footer-section mt-5 pt-4 pb-4 text-white">
    <div class="section-container">
        <div class="row gy-4">
            <!-- Logo & Deskripsi -->
            <div class="col-12 col-md-4">
                <h5 class="footer-logo">
                  <img src="<?php echo $result_logo_web['footer']; ?>" alt="Logo" class="footer-img">
                </h5>
                <p class="footer-desc">
                    <?= htmlspecialchars($meta_desc ?? '') ?>
                </p>
            </div>

            <!-- Navigasi -->
            <div class="col-6 col-md-2">
                <h6 class="footer-title">Layanan</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo $web_url; ?>">Home</a></li>
                    <li><a href="<?php echo $web_url; ?>/regional-ml">Cek Regional ML</a></li>
                    <li><a href="<?php echo $web_url; ?>/history">Cari Pesanan</a></li>
                </ul>
            </div>

            <!-- Info -->
            <div class="col-6 col-md-3">
                <h6 class="footer-title">Informasi</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo $web_url; ?>/lainnya/tentang-kami">Tentang Kami</a></li>
                    <li><a href="<?php echo $web_url; ?>/lainnya/syarat-ketentuan">Syarat & Ketentuan</a></li>
                    <li><a href="<?php echo $web_url; ?>/lainnya/kebijakan-privasi">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <!-- Sosial Media -->
            <div class="col-12 col-md-3">
                <h6 class="footer-title">Ikuti Kami</h6>
                <div class="footer-social d-flex gap-3">
                    <a href="<?= htmlspecialchars($meta_fb) ?>" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="<?= htmlspecialchars($meta_ig) ?>" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="<?= htmlspecialchars($meta_tw) ?>" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="<?= htmlspecialchars($meta_yt) ?>" target="_blank"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
        </div>
       
        <!-- Metode Pembayaran -->
        <div class="col-12">
            <h6 class="footer-title mt-4">Metode Pembayaran Lengkap</h6>
            <marquee behavior="scroll" direction="left" scrollamount="5" class="payment-marquee mt-2">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/BCA.png" alt="BCA" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/BNI.png" alt="BNI" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/MANDIRI.png" alt="Mandiri" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/BRI.png" alt="BRI" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/OVO.png" alt="OVO" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/DANA.png" alt="DANA" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/SHOPPEPAY.png" alt="ShopeePay" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/GOPAY.png" alt="GoPay" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/QRIS.png" alt="QRIS" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/INDOMARET.png" alt="INDOMARET" height="30" class="mx-3">
                <img src="<?php echo $web_url; ?>/assets/img/pembayaran/ALFAMART.png" alt="ALFAMART" height="30" class="mx-3">
            </marquee>
        </div>

        <hr class="border-secondary mt-4">

        <!-- Copyright -->
        <div class="text-center small text-white">
            &copy; <?php echo date('Y'); ?> <?= htmlspecialchars($meta_nama ?? 'Produk Digital') ?>. All rights reserved.
        </div>
    </div>
</footer>

<style>
  .install-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #3d1424;
    color: white;
    padding: 15px 20px;
    border-radius: 12px;
    z-index: 9999;
    max-width: 300px;
    animation: fadeInUp 0.3s ease-out;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  #installBox button {
    font-size: 0.975rem;
  }
</style>


<!-- Floating Install Notification -->
<div id="installBox" style="display:none;" class="install-box shadow">
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <strong>Install App</strong><br>
      <small>Akses cepat langsung dari layar utama</small>
    </div>
    <div class="d-flex align-items-center">
      <button id="installBtn" class="btn btn-sm btn-custom-primary ms-3">Install</button>
      <button class="btn btn-sm btn-link text-light ms-2" id="closeInstallBox">&times;</button>
    </div>
  </div>
</div>


</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let deferredPrompt;
  const installBox = document.getElementById('installBox');
  const installBtn = document.getElementById('installBtn');
  const closeBtn = document.getElementById('closeInstallBox');

  // Deteksi apakah sedang dibuka dalam mode PWA
  const isInStandaloneMode = () =>
    window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

  const hasDismissed = sessionStorage.getItem('install_dismissed');

  if (!isInStandaloneMode()) {
    // Jika bukan mode app (masih di browser)
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;

      // Hanya tampil kalau belum ditutup oleh user di sesi ini
      if (!hasDismissed) {
        setTimeout(() => {
          installBox.style.display = 'block';
        }, 3000); // Delay 3 detik biar tidak ganggu langsung
      }
    });

    // Tombol Install diklik
    installBtn.addEventListener('click', () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(choice => {
          if (choice.outcome === 'accepted') {
            console.log('✅ User accepted A2HS');
          } else {
            console.log('❌ User dismissed A2HS');
          }
          deferredPrompt = null;
          installBox.style.display = 'none';
        });
      }
    });

    // Tombol tutup diklik
    closeBtn.addEventListener('click', () => {
      installBox.style.display = 'none';
      sessionStorage.setItem('install_dismissed', 'true');
    });

    // Jika user benar-benar menginstal
    window.addEventListener('appinstalled', () => {
      console.log('📦 Aplikasi berhasil diinstal');
      installBox.style.display = 'none';
    });
  } else {
    // Kalau sudah dibuka sebagai app, jangan tampilkan box install
    installBox.style.display = 'none';
  }
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.flash-sale-scroll');
    if (!container) return;

    const cardWidth = container.querySelector('.flash-card')?.offsetWidth || 160; // default fallback
    const gap = parseInt(getComputedStyle(container).gap) || 16; // ambil jarak antar card
    const scrollAmount = cardWidth + gap;

    let currentScroll = 0;

    setInterval(() => {
        // Jika sudah sampai ujung kanan, reset scroll ke awal
        if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 5) {
            container.scrollTo({ left: 0, behavior: 'smooth' });
            currentScroll = 0;
        } else {
            currentScroll += scrollAmount;
            container.scrollTo({ left: currentScroll, behavior: 'smooth' });
        }
    }, 2000); // scroll setiap 2 detik
});
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.clickable-card');
        let redirectTimeout = null; // <- simpan timeout ID global

        cards.forEach(card => {
            card.addEventListener('click', function() {
                const url = card.getAttribute('data-href');
                if (!url) return;

                // Batalkan timeout redirect sebelumnya jika ada
                if (redirectTimeout) {
                    clearTimeout(redirectTimeout);
                    redirectTimeout = null;
                }

                // Reset semua kartu
                cards.forEach(c => {
                    c.classList.remove('cling');
                    const otherText = c.querySelector('.card-text');
                    if (otherText) {
                        otherText.classList.remove('spinner');
                        otherText.textContent = otherText.getAttribute('data-original') || otherText.textContent;
                    }
                });

                // Aktifkan efek cling & spinner untuk kartu yang baru diklik
                const text = card.querySelector('.card-text');
                card.classList.add('cling');
                if (text) {
                    if (!text.getAttribute('data-original')) {
                        text.setAttribute('data-original', text.textContent);
                    }
                    text.classList.add('spinner');
                    text.textContent = '';
                }

                // Redirect setelah 600ms
                redirectTimeout = setTimeout(() => {
                    window.location.href = url;
                }, 600);
            });
        });
    });
</script>



<script>
    function adjustMainPadding() {
        const header = document.querySelector('header');
        const main = document.querySelector('main.main-content');
        const isMobile = window.innerWidth < 992;

        if (!header || !main) return;

        let paddingTop;

        if (isMobile) {
            // Gunakan offsetTop + offsetHeight agar tetap stabil meskipun user scroll
            paddingTop = header.offsetTop + header.offsetHeight;
        } else {
            // Desktop: gunakan tinggi tetap tergantung class search-active
            const searchActive = header.classList.contains('search-active');
            paddingTop = searchActive ? 140 : 70;
        }

        main.style.paddingTop = paddingTop + 'px';
    }

    // Jalankan saat halaman dimuat dan saat ukuran layar berubah
    window.addEventListener('load', adjustMainPadding);
    window.addEventListener('resize', adjustMainPadding);

    // Pantau perubahan pada header
    const headerEl = document.querySelector('header');
    if (headerEl) {
        const observer = new MutationObserver(adjustMainPadding);
        observer.observe(headerEl, {
            attributes: true,
            childList: true,
            subtree: true
        });
    }

    // Toggle pencarian
    document.getElementById('searchToggle')?.addEventListener('click', () => {
        setTimeout(adjustMainPadding, 350);
    });

    // Toggle dropdown menu
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
        el.addEventListener('click', () => {
            setTimeout(adjustMainPadding, 350);
        });
    });

    // Juga tangani scroll (untuk kasus mobile kadang layout berubah setelah scroll cepat)
    window.addEventListener('scroll', () => {
        if (window.innerWidth < 992) {
            requestAnimationFrame(adjustMainPadding);
        }
    });
</script>


<script>
    // Debounce helper
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.addEventListener("DOMContentLoaded", function() {
        const inputs = [
            document.getElementById('searchInputDesktop'),
            document.getElementById('searchInputMobile'),
            document.getElementById('searchInput'),
        ].filter(Boolean);

        const searchToggle = document.getElementById('searchToggle');
        const searchBox = document.getElementById('searchDesktopBox');
        const header = document.querySelector('header');
        const mobileSearch = document.getElementById('mobileSearch');
        const searchResults = document.getElementById('searchResults');

        const isProdukPage = document.body.dataset.page === "produk";

        function filterProduk(keyword) {
            const items = document.querySelectorAll("");
            const noResults = document.getElementById("noResultsMessage");
            const keywordLower = keyword.trim().toLowerCase();
            let matchCount = 0;

            if (!items.length) return;

            items.forEach((item) => {
                const nama = item.dataset.nama?.toLowerCase() || "";
                const keywords = item.dataset.keywords?.toLowerCase() || "";
                const cocok = nama.includes(keywordLower) || keywords.includes(keywordLower);
                item.style.display = cocok ? "" : "none";
                if (cocok) matchCount++;
            });

            if (noResults) {
                noResults.style.display = matchCount === 0 ? "block" : "none";
            }
        }

        function loadSearchResults(keyword) {
            const q = keyword.trim().toLowerCase();
            if (!searchResults) return;

            // Batasi panjang keyword demi keamanan dan performa
            if (q.length > 50) return;

            if (q === "") {
                searchResults.innerHTML = "";
                searchResults.style.display = "none";
                return;
            }

            fetch("<?php echo $web_url; ?>/ajax/live.php?search=" + encodeURIComponent(q) + "&ajax=1")
                .then(res => res.text())
                .then(html => {
                    searchResults.innerHTML = html;
                    searchResults.style.display = "block";
                })
                .catch(err => {
                    console.error("Gagal ambil data pencarian:", err);
                    searchResults.innerHTML = "<p class='text-danger px-3 py-2'>Terjadi kesalahan saat mengambil data.</p>";
                    searchResults.style.display = "block";
                });
        }



        const debouncedSearch = debounce((keyword) => {
            const trimmed = keyword.trim();
            const path = window.location.pathname;

            if (trimmed === "") {
                sessionStorage.removeItem("keepSearchOpen");
                sessionStorage.removeItem("searchManuallyClosed");
                sessionStorage.removeItem("originPage");
                if (searchResults) searchResults.style.display = "none";
                return;
            }

            if (!sessionStorage.getItem("originPage")) {
                sessionStorage.setItem("originPage", path);
            }

            sessionStorage.setItem("keepSearchOpen", "true");
            sessionStorage.setItem("searchManuallyClosed", "false");

            if (isProdukPage) {
                filterProduk(trimmed);
            } else {
                loadSearchResults(trimmed);
            }
        }, 300);

        inputs.forEach(input => {
            input.addEventListener("input", () => {
                debouncedSearch(input.value);
            });
        });

        if (searchToggle && searchBox && header && inputs[0]) {
            searchToggle.addEventListener("click", function() {
                const isOpen = searchBox.classList.toggle("show");
                header.classList.toggle("search-active");
                sessionStorage.setItem("keepSearchOpen", isOpen ? "true" : "false");
                sessionStorage.setItem("searchManuallyClosed", isOpen ? "false" : "true");

                if (isOpen) {
                    setTimeout(() => inputs[0].focus(), 150);
                } else {
                    inputs[0].value = '';
                    if (searchResults) searchResults.style.display = 'none';
                    sessionStorage.removeItem('originPage');
                }
            });
        }

        if (mobileSearch && inputs[1]) {
            mobileSearch.addEventListener("hidden.bs.collapse", function() {
                inputs[1].value = "";
                if (searchResults) searchResults.style.display = "none";
                sessionStorage.setItem("searchManuallyClosed", "true");
                sessionStorage.setItem("keepSearchOpen", "false");
                sessionStorage.removeItem("originPage");
            });
        }

        // Auto-load from URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialSearch = urlParams.get("search");
        if (initialSearch) {
            inputs.forEach(input => input.value = initialSearch);
            if (isProdukPage) {
                filterProduk(initialSearch);
            } else {
                loadSearchResults(initialSearch);
            }
        }
    });
</script>


</body>

</html>