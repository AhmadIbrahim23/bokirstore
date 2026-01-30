<?php
session_start();
require "../config.php";

if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    exit("Akses ditolak.");
}

// Ambil akun DigiFlazz
$q = $conn->query("SELECT * FROM provider WHERE nama='digiflazz' AND status='active' LIMIT 1");
$row = $q->fetch_assoc();
$username = $row['username'];
$api_key  = $row['api_key'];

// Ambil data setting profit
$setting = $conn->query("SELECT * FROM setting_profit LIMIT 1")->fetch_assoc();
$profit_jual     = (float)$setting['profit_jual'];
$tipe_jual       = $setting['tipe_jual'];
$profit_reseller = (float)$setting['profit_reseller'];
$tipe_reseller   = $setting['tipe_reseller'];
$profit_diskon   = (float)$setting['profit_diskon'];
$tipe_diskon     = $setting['tipe_diskon'];

// Panggil API DigiFlazz
$sign = md5($username . $api_key . "pricelist");
$data = ["cmd" => "prepaid", "username" => $username, "sign" => $sign];
$url = "https://api.digiflazz.com/v1/price-list";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
$result = json_decode($res, true);

// Mapping kategori
$tipeMap = [
    'aktivasi perdana' => 'aktivasi',
    'data' => 'paket_data',
    'e-money' => 'emoney',
    'esim' => 'esim',
    'games' => 'game',
    'gas' => 'gas',
    'masa aktif' => 'masa_aktif',
    'paket sms & telpon' => 'telepon_sms',
    'pln' => 'pln',
    'pulsa' => 'pulsa',
    'tv' => 'tv',
    'voucher' => 'voucher'
];
$negara_ln = ['malaysia', 'china', 'vietnam', 'thailand', 'singapore', 'philippines'];

foreach ($result['data'] as $item) {
    $kode_produk = $item['buyer_sku_code'];
    $cat = strtolower(trim($item['category']));
    $tipe_kategori = 'lainnya';
    
    $tipe_produk = ($item['type'] === 'postpaid') ? 'pascabayar' : 'prabayar';

    foreach ($negara_ln as $n) {
        if (stripos($cat, $n) !== false && stripos($cat, 'topup') !== false) {
            $tipe_kategori = 'luar_negeri';
            break;
        }
    }
    if (isset($tipeMap[$cat])) {
        $tipe_kategori = $tipeMap[$cat];
    }

    $product_name = $item['product_name'];
    $category = $tipe_kategori;
    $brand = strtolower($item['brand']);
    $type = strtolower($item['type']);
    $desc = $item['desc'];
    $harga_asli = (float)$item['price'];

    $status = ($item['buyer_product_status'] === true and $item['seller_product_status'] === true) ? "normal" : "gangguan";

    $harga_jual = ($tipe_jual === 'percent') ? $harga_asli + ($harga_asli * $profit_jual / 100) : $harga_asli + $profit_jual;
    $harga_reseller = ($tipe_reseller === 'percent') ? $harga_asli + ($harga_asli * $profit_reseller / 100) : $harga_asli + $profit_reseller;
    $harga_diskon = ($tipe_diskon === 'percent') ? $harga_jual - ($harga_jual * $profit_diskon / 100) : $harga_jual - $profit_diskon;
    if ($harga_diskon < $harga_asli || $harga_diskon < 0 || $harga_diskon > $harga_jual) $harga_diskon = $harga_jual;

    // Penentuan logo otomatis
    $logo = $web_url . '/assets/img/produk/new-product.png';
    $brand_key = strtolower($brand);
    if ($brand_key === 'tri') $logo = "$web_url/assets/img/produk/3-perdana.png";
    elseif ($brand_key === 'axis') $logo = "$web_url/assets/img/produk/axis-perdana.png";
    elseif ($brand_key === 'xl') $logo = "$web_url/assets/img/produk/xl-perdana.png";
    elseif ($brand_key === 'telkomsel') $logo = "$web_url/assets/img/produk/telkomsel-perdana.png";
    elseif ($brand_key === 'indosat') $logo = "$web_url/assets/img/produk/indosat-perdana.png";
    elseif ($brand_key === 'by.u') $logo = "$web_url/assets/img/produk/byu-perdana.png";
    elseif ($brand_key === 'smartfren') $logo = "$web_url/assets/img/produk/smartfren-perdana.png";
    elseif ($brand_key === 'pln' || $category === 'pln') $logo = "$web_url/assets/img/produk/pln.png";
    elseif ($brand_key === 'pertamina gas' || $category === 'gas') $logo = "$web_url/assets/img/produk/pertamina.png";
    elseif (in_array($brand_key, ['link aja','linkaja'])) $logo = "$web_url/assets/img/produk/LINKAJA.png";
    elseif ($brand_key === 'ovo') $logo = "$web_url/assets/img/produk/OVO.png";
    elseif (in_array($brand_key, ['gopay', 'go pay'])) $logo = "$web_url/assets/img/produk/GOPAY.png";
    elseif (in_array($brand_key, ['shopeepay', 'shopee pay'])) $logo = "$web_url/assets/img/produk/SHOPPEPAY.png";
    elseif ($brand_key === 'dana') $logo = "$web_url/assets/img/produk/DANA.png";
    elseif ($brand_key === 'grab') $logo = "$web_url/assets/img/produk/grab.png";
    elseif ($brand_key === 'mandiri e-toll') $logo = "$web_url/assets/img/produk/etool.jpg";
    elseif ($brand_key === 'bri brizzi') $logo = "$web_url/assets/img/produk/brizii.png";
    elseif ($brand_key === 'bukalapak') $logo = "$web_url/assets/img/produk/bukalapak.png";
    elseif ($brand_key === 'free fire') $logo = "$web_url/assets/img/produk/free-fire.jpg";
    elseif ($brand_key === 'tower of fantasy') $logo = "$web_url/assets/img/produk/toweroffantasy.png";
    elseif (stripos($brand_key, 'football master') !== false) $logo = "$web_url/assets/img/produk/football-master.png";
    elseif ($brand_key === 'garena') $logo = "$web_url/assets/img/produk/garena.png";
    elseif ($brand_key === 'hago') $logo = "$web_url/assets/img/produk/hago.png";
    elseif (stripos($brand_key, 'mobile legend') !== false) $logo = "$web_url/assets/img/produk/mobile-legends.png";
    elseif ($brand_key === 'be the king') $logo = "$web_url/assets/img/produk/be-the-king.jpg";
    elseif ($brand_key === 'call of duty mobile') $logo = "$web_url/assets/img/produk/callofdutymobile.jpg";
    elseif ($brand_key === 'arena of valor') $logo = "$web_url/assets/img/produk/arenaOfValor.jpg";
    elseif ($brand_key === 'au2 mobile') $logo = "$web_url/assets/img/produk/au2Dance.jpg";
    elseif ($brand_key === 'honor of kings') $logo = "$web_url/assets/img/produk/honor-of-kings.jpg";
    elseif ($brand_key === 'astral guardians') $logo = "$web_url/assets/img/produk/astral-guardians.jpg";
    elseif ($brand_key === 'laplace m') $logo = "$web_url/assets/img/produk/laplaceMm.png";
    elseif ($brand_key === 'lords mobile') $logo = "$web_url/assets/img/produk/lords-mobile.jpg";
    elseif ($brand_key === 'mangatoon') $logo = "$web_url/assets/img/produk/mangatoon.jpg";
    elseif ($brand_key === 'point blank') $logo = "$web_url/assets/img/produk/point-blank.jpg";
    elseif ($brand_key === 'tower of fantasyk') $logo = "$web_url/assets/img/produk/toweroffantasy.jpg";
    elseif ($brand_key === 'pubg mobile') $logo = "$web_url/assets/img/produk/pubG.png";
    
    
    elseif ($category === 'tv') $logo = "$web_url/assets/img/produk/tv.png";

    // Cek kode produk
    $cek = $conn->prepare("SELECT tipe_diskon, harga_asli, harga_diskon, expired_diskon  FROM layanan WHERE kode_produk = ? LIMIT 1");
    $cek->bind_param("s", $kode_produk);
    $cek->execute();
    $cek->store_result();
    $cek->bind_result($tipe_diskon_db, $harga_asli_saatini, $harga_diskon_saatini, $expired_diskon_db);
    
    if ($cek->num_rows > 0) {
            $cek->fetch();
            
            // Cek apakah expired
            $expired = false;
            if ($tipe_diskon_db === 'On' && $expired_diskon_db !== '0000-00-00 00:00:00') {
                $expired = strtotime($expired_diskon_db) < time();
            }
        
            if ($tipe_diskon_db === 'On' && $harga_asli_saatini < $harga_diskon_saatini && !$expired && $status == "normal") {
                // Jangan ubah tipe_diskon dan expired_diskon
                $query = "UPDATE layanan SET 
                    nama_produk = ?, kategori = ?, brand = ?, tipe = ?, deskripsi = ?, logo = ?, 
                    harga_asli = ?, harga_jual = ?, harga_reseller = ?, harga_diskon = ?,
                    status = ?, tipe_produk = ?
                    WHERE kode_produk = ?";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param(
                    "ssssssddddsss",
                    $product_name, $category, $brand, $type, $desc, $logo,
                    $harga_asli, $harga_jual, $harga_reseller, $harga_diskon,
                    $status, $tipe_produk, $kode_produk
                );

            } elseif ($tipe_diskon_db === 'On' && $expired || $status == "gangguan"){
                // Jangan ubah tipe_diskon dan expired_diskon
                $stmt = $conn->prepare("UPDATE layanan SET 
                    nama_produk = ?, kategori = ?, brand = ?, tipe = ?, deskripsi = ?, logo = ?, 
                    harga_asli = ?, harga_jual = ?, harga_reseller = ?, harga_diskon = ?, 
                    tipe_diskon = 'Off', expired_diskon = '0000-00-00 00:00:00', status = ?, tipe_produk = ?
                    WHERE kode_produk = ?");
                $stmt->bind_param("ssssssddddsss", // total 13: 6 s + 4 d + 3 s
                $product_name,   // s
                $category,       // s
                $brand,          // s
                $type,           // s
                $desc,           // s
                $logo,           // s
                $harga_asli,     // d
                $harga_jual,     // d
                $harga_reseller, // d
                $harga_diskon,   // d
                $status,         // s
                $tipe_produk,    // s
                $kode_produk     // s
            );
            } else {
                // Reset tipe_diskon dan expired_diskon
                $stmt = $conn->prepare("UPDATE layanan SET 
                  nama_produk = ?, kategori = ?, brand = ?, tipe = ?, deskripsi = ?, logo = ?, 
                  harga_asli = ?, harga_jual = ?, harga_reseller = ?, harga_diskon = ?, 
                  tipe_diskon = 'Off', expired_diskon = '0000-00-00 00:00:00', status = ?, tipe_produk = ?
                  WHERE kode_produk = ?");
                
                $stmt->bind_param("ssssssddddsss",  // 6 string, 4 double, 3 string = 13
                  $product_name,   // s
                  $category,       // s
                  $brand,          // s
                  $type,           // s
                  $desc,           // s
                  $logo,           // s
                  $harga_asli,     // d
                  $harga_jual,     // d
                  $harga_reseller, // d
                  $harga_diskon,   // d
                  $status,         // s
                  $tipe_produk,    // s
                  $kode_produk     // s
                );
            }
        
        } else {
            $stmt = $conn->prepare("INSERT INTO layanan 
                  (kode_produk, nama_produk, kategori, brand, tipe, deskripsi, logo, 
                   harga_asli, harga_jual, harga_reseller, harga_diskon, 
                   tipe_diskon, status, expired_diskon, tipe_produk) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Off', ?, '0000-00-00 00:00:00', ?)");
                
                $stmt->bind_param("ssssssddddsss",
                  $kode_produk,     // s
                  $product_name,    // s
                  $category,        // s
                  $brand,           // s
                  $type,            // s
                  $desc,            // s
                  $logo,            // s
                  $harga_asli,      // d
                  $harga_jual,      // d
                  $harga_reseller,  // d
                  $harga_diskon,    // d
                  $status,          // s
                  $tipe_produk      // s
                );
        }


    $stmt->execute();
    $stmt->close();
    $cek->close();
}
