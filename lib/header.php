<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="google-site-verification" content="cCWVZ4PTXOZNqa6hym5DfE63bfLVteP1--Xtq3AUEN0" />
  
  <title><?= htmlspecialchars($meta_title ?? 'Produk Digital') ?></title>
  
  <!-- SEO Meta -->
  <meta name="description" content="<?= htmlspecialchars($meta_desc ?? 'Temukan layanan digital terbaik seperti pulsa, paket data, token PLN, dan lainnya hanya di '.$meta_nama.'.') ?>">
  <meta name="keywords" content="<?= htmlspecialchars($meta_keyword ?? 'pulsa, paket data, token pln, '.$meta_nama.', pembayaran digital') ?>">
  <meta name="author" content="<?= htmlspecialchars($meta_nama) ?>">
  <meta name="robots" content="index, follow">
  <meta name="language" content="id">
  <!-- Android -->
  <!-- Warna utama untuk browser (Android & lainnya) -->
    <meta name="theme-color" content="#3d1424" />
    
    <!-- Android Chrome -->
    <meta name="mobile-web-app-capable" content="yes">
    
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($meta_nama ?? 'Sakurupiah') ?>">



  <!-- Open Graph / Facebook -->
  <meta property="og:title" content="<?= htmlspecialchars($meta_title ?? 'Produk Digital') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc ?? 'Belanja produk digital mudah dan cepat di '.$meta_nama.'') ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($result_logo_web['favicon'] ?? $web_url . '/assets/img/web/default-og-image.png') ?>">
  <meta property="og:site_name" content="<?= htmlspecialchars($meta_nama) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($meta_title ?? 'Produk Digital') ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc ?? 'Belanja produk digital mudah dan cepat di '.$meta_nama.'') ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($result_logo_web['favicon'] ?? $web_url . '/assets/img/web/logo-dompet-sakurupiah.png') ?>">

  <!-- Canonical -->
  <link rel="canonical" href="<?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="16x16" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="icon" type="image/png" sizes="48x48" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="icon" type="image/png" sizes="192x192" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="icon" type="image/png" sizes="512x512" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= $result_logo_web['favicon'] ?>">
  <link rel="shortcut icon" href="<?= $result_logo_web['favicon'] ?>">
  
  <link rel="manifest" href="<?= $web_url; ?>/manifest.json">

  <!-- CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-P8xQ0qz9Nq6A7...dstA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="<?= $web_url; ?>/assets/css/sakurupiah.css">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
   <script type="application/ld+json">
    <?= json_encode([
      "@context" => "https://schema.org",
      "@type" => "Organization",
      "name" => $meta_nama,
      "url" => $web_url,
      "logo" => $result_logo_web['favicon'],
      "sameAs" => array_filter([
        $meta_fb,
        $meta_ig,
        !empty($meta_wa) ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $meta_wa) : null
      ])
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>

</head>


<body>
    
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
      .then(reg => console.log("Service worker registered:", reg.scope))
      .catch(err => console.error("Service worker failed:", err));
  }
</script>
    <div class="wrapper">
    <!-- HEADER -->
    <header class="bg-custom shadow-sm position-fixed">
        <div class="header-container d-flex align-items-center justify-content-between">

            <!-- Logo -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?php echo getCurrentUrl();?>" class="text-decoration-none">
                    <img src="<?php echo $result_logo_web['header'];?>" alt="Logo" height="40" class="logo-header">
                </a>
            </div>

            <!-- Desktop Menu with Icons -->
            <nav class="d-none d-lg-flex align-items-center gap-4 desktop-nav">
                <a href="<?php echo $web_url; ?>" class="text-decoration-none text-white d-flex align-items-center gap-1">
                    <i class="bi bi-house"></i> Home
                </a>
                <a href="<?php echo $web_url; ?>/history" class="text-decoration-none text-white d-flex align-items-center gap-1">
                    <i class="bi bi-search"></i> Cari Pesanan
                </a>
                <a href="<?php echo $web_url; ?>/regional-ml" class="text-decoration-none text-white d-flex align-items-center gap-1">
                    <i class="bi bi-globe2"></i> Regional Ml
                </a>
                <div class="dropdown">
                    <a href="#" class="text-decoration-none text-white dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                        <i class="bi bi-calculator"></i> Kalkulator ML
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/kalkulator-winrate">
                               Win Rate
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/magic-whell">
                               Magic Wheel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/zodiac-ml">
                               Zodiac
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <a href="#" class="text-decoration-none text-white dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Lainnya
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/lainnya/syarat-ketentuan">
                                Syarat & Ketentuan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/lainnya/kebijakan-privasi">
                                Kebijakan Privasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo $web_url; ?>/lainnya/tentang-kami">
                                Tentang Kami
                            </a>
                        </li>
                    </ul>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                  <a href="<?php echo $web_url; ?>/dashboard" class="text-decoration-none text-white d-flex align-items-center gap-1">
                    <i class="bi bi-person-circle"></i> Akun Saya
                  </a>
                <?php endif; ?>

            
                <button class="btn btn-outline-light search-box-toggle" id="searchToggle">
                    <i class="bi bi-search"></i>
                </button>
                <?php if (isset($_SESSION['user_id'])): ?>
                  <a href="<?php echo $web_url; ?>/logout" class="btn btn-custom-primary d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </a>
                <?php else: ?>
                  <a href="<?php echo $web_url; ?>/login" class="btn btn-custom-primary d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                  </a>
                <?php endif; ?>

            </nav>

            <!-- Mobile: Burger Menu & Search -->
            <div class="d-lg-none d-flex align-items-center gap-2">
                <button class="btn btn-outline-light" data-bs-toggle="collapse" data-bs-target="#mobileSearch">
                    <i class="bi bi-search"></i>
                </button>
                <button class="btn btn-outline-light" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>

        <!-- Search Desktop (Centered Absolute) -->
        <div class="header-container d-none d-lg-block">
            <div id="searchDesktopBox">
                <input type="text" class="form-control" id="searchInputDesktop" placeholder="Cari layanan atau produk...">
            </div>
        </div>

        <!-- Search Mobile -->
        <div class="collapse" id="mobileSearch">
            <div class="search-input-mobile">
                <input type="text" class="form-control" id="searchInputMobile" placeholder="Cari layanan atau produk...">
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div class="collapse d-lg-none" id="mobileMenu">
            <div class="list-group list-group-flush px-3 pb-2">

                <!-- Menu utama -->
                <a href="<?php echo $web_url; ?>" class="list-group-item list-group-item-action start d-flex align-items-center gap-2">
                    <i class="bi bi-house"></i> Home
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $web_url; ?>/dashboard" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle"></i> Akun Saya
                </a>
                <?php endif; ?>
                
                <a href="<?php echo $web_url; ?>/history" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-search"></i> Cari Pesanan
                </a>
                
                
                <a href="<?php echo $web_url; ?>/regional-ml" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-globe2"></i> Regional ML
                </a>
                
                <div class="accordion menus border-0" id="accordionWinrate">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingLainnya">
                            <button class="accordion-button header collapsed d-flex align-items-center gap-2 bg-white px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWinrate" aria-expanded="false" aria-controls="collapseWinrate" style="font-size: 1rem;">
                                <i class="bi bi-calculator"></i> Kalkulator ML
                            </button>
                        </h2>
                        <div id="collapseWinrate" class="accordion-collapse collapse" aria-labelledby="headingLainnya" data-bs-parent="#accordionWinrate">
                            <div class="accordion-body px-0 pt-1 pb-2">
                                <a href="<?php echo $web_url; ?>/kalkulator-winrate" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                    Win Rate
                                </a>
                                <a href="<?php echo $web_url; ?>/magic-whell" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                    Magic Whell
                                </a>
                                <a href="<?php echo $web_url; ?>/zodiac-ml" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                    Zodiac
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu "Lainnya" pakai Accordion -->
                <div class="accordion border-0" id="accordionLainnya">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingLainnya">
                            <button class="accordion-button header collapsed d-flex align-items-center gap-2 bg-white px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLainnya" aria-expanded="false" aria-controls="collapseLainnya" style="font-size: 1rem;">
                                <i class="bi bi-three-dots"></i> Lainnya
                            </button>
                        </h2>
                        <div id="collapseLainnya" class="accordion-collapse collapse" aria-labelledby="headingLainnya" data-bs-parent="#accordionLainnya">
                            <div class="accordion-body px-0 pt-1 pb-2">
                                <a href="<?php echo $web_url; ?>/lainnya/syarat-ketentuan" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                    Syarat & Ketentuan
                                </a>
                                <a href="<?php echo $web_url; ?>/lainnya/kebijakan-privasi" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                   Kebijakan Privasi
                                </a>
                                <a href="<?php echo $web_url; ?>/lainnya/tentang-kami" class="list-group-item list-group-item-action d-flex align-items-center gap-2 border-0 ps-4">
                                   Tentang Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                  <!-- Tombol Logout -->
                  <a href="<?php echo $web_url; ?>/logout" class="btn btn-custom-primary mt-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                  </a>
                <?php else: ?>
                  <!-- Tombol Masuk -->
                  <a href="<?php echo $web_url; ?>/login" class="btn btn-custom-primary mt-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                  </a>
                <?php endif; ?>

            </div>
        </div>
    </header>


    <main class="main-content">

        <div id="searchResults"></div>
    