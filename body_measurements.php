<?php
session_start();

$host = "localhost";
$user = "postgres";
$password = "123123";
$dbname = "FitCheck";
$port = "5432";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

$gender = null;
$vucutTipi = '';
$bilgi = '';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = pg_prepare($conn, "query_user_gender", "SELECT cinsiyet FROM kullanicilar WHERE id = $1");
    $result = pg_execute($conn, "query_user_gender", array($userId));

    if ($row = pg_fetch_assoc($result)) {
        $gender = $row['cinsiyet'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gogusCevresi = $_POST['gogusCevresi'];
    $belCevresi = $_POST['belCevresi'];
    $kalcaCevresi = $_POST['kalcaCevresi'];
    $ustKalcaCevresi = $_POST['ustKalcaCevresi'];

    $belKalcaOrani = $belCevresi / $kalcaCevresi;
    $gogusBelOrani = $gogusCevresi / $belCevresi;
    $gogusKalcaOrani = $gogusCevresi / $kalcaCevresi;

    if ($gender === 'erkek') {
        if ($gogusKalcaOrani > 1.05 && $gogusBelOrani > 1.05) {
            $vucutTipi = "Ters Üçgen";
            $bilgi = "Göğüs çevresi hem bel hem de kalça çevresinden geniş, üst vücut daha dominant.";
        } elseif ($belKalcaOrani >= 0.9 && $belKalcaOrani <= 1.1 && $gogusBelOrani >= 0.9 && $gogusBelOrani <= 1.1) {
            $vucutTipi = "Dikdörtgen";
            $bilgi = "Göğüs, bel ve kalça ölçüleri birbirine yakın, vücut hatları daha düz.";
        } elseif ($belKalcaOrani < 0.75 && $gogusKalcaOrani >= 1) {
            $vucutTipi = "Kum Saati";
            $bilgi = "Bel çevresi göreceli olarak daha ince ve omuzlar ile kalçalar dengeli.";
        } elseif ($belKalcaOrani > 0.95) {
            $vucutTipi = "Elma Tipi";
            $bilgi = "Bel çevresi kalça çevresine göre daha geniş.";
        } else {
            $vucutTipi = "Armut Tipi";
            $bilgi = "Kalça çevresi bel çevresinden daha geniş.";
        }
    } elseif ($gender === 'Kadın') {
        // Kadın vücut tipi hesaplamaları
        if ($gogusKalcaOrani >= 1 && $belKalcaOrani >= 0.75 && $gogusBelOrani >= 1) {
            $vucutTipi = "Ters Üçgen";
            $bilgi = "Omuzlar kalça çevresinden daha geniş, bel çevresi nispeten daha ince.";
        } elseif ($belKalcaOrani > 0.75 && $belKalcaOrani < 0.85 && ($gogusKalcaOrani < 0.85 || $gogusKalcaOrani > 1.15)) {
            $vucutTipi = "Dikdörtgen";
            $bilgi = "Göğüs, bel ve kalça ölçüleri birbirine benzer, belirgin bel kıvrımı az.";
        } elseif ($belKalcaOrani < 0.75 && $gogusKalcaOrani >= 0.8 && $gogusKalcaOrani < 1.1) {
            $vucutTipi = "Kum Saati";
            $bilgi = "Bel çevresi belirgin şekilde daha ince ve göğüs ile kalça çevresi dengeli.";
        } elseif ($belKalcaOrani > 0.85) {
            $vucutTipi = "Elma Tipi";
            $bilgi = "Bel çevresi kalça çevresine göre daha geniş.";
        } else {
            $vucutTipi = "Armut Tipi";
            $bilgi = "Kalça çevresi bel çevresinden daha geniş.";
        }
    } else {
        $vucutTipi = "Bilinmiyor";
        $bilgi = "Lütfen cinsiyetinizi kontrol ediniz.";
    }
   
} else {
    // Eğer POST ile veri gelmemişse kullanıcıyı bilgilendir
    echo "<div class='vucut-tipi-container'><p>Lütfen bilgilerinizi giriniz.</p></div>";

}


if ($vucutTipi && $bilgi) {
    // ... PHP kodlarınız
    // Cinsiyete göre prefix ataması
    $cinsiyetPrefix = ($gender === 'erkek') ? 'e' : 'k';
    
    // "Tipi" kelimesini kaldırarak ve boşlukları silerek vücut tipini slug haline getir
    $vucutTipiSlug = strtolower(str_replace(array(' ', 'Tipi'), '', $vucutTipi));
    
    // Türkçe karakterleri URL kodlaması ile değiştirme
    $resimAdi = rawurlencode($cinsiyetPrefix . "-" . $vucutTipiSlug) . ".png";
    $resimYolu = "assets/images/bodyTypes/" . $resimAdi;

    // Vücut tipi ve bilgiyi göstermek için HTML çıktısı
    echo "<div class='vucut-tipi-container'>";
    echo "<div class='vucut-tipi-gorsel'>";
    echo "<img src='$resimYolu' alt='$vucutTipi' class='vucut-tipi-resim'>";
    echo "<div class='bilgilendirme-notu'>Bu görsel yalnızca bilgilendirme amaçlıdır.</div>";
    echo "</div>";
    echo "<p>Vücut Tipiniz: <strong>$vucutTipi</strong></p>";
    echo "<p>$bilgi</p>";
    echo "</div>";
} else {
    // Eğer vücut tipi ve bilgi boşsa veya değerleri yoksa hata mesajı
    echo "<div class='vucut-tipi-container'><p>Bilgiler eksik, lütfen tüm alanları doldurunuz.</p></div>";
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vücut Tipi Hesaplama</title>
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background-color: #c5bfbf;
    margin: 0;
    padding: 20px;
    color: #333;
}
.navbar {
    display: flex;
    justify-content: flex-end; /* Navbar içeriğini sağa hizala */
    align-items: center;
    padding: 1rem;
    top: 0;
    width: 100%;
    background-color: #333;
  
  }
  
  h2 {
    font-weight: 500;
    color: #444;
    text-align: center;
    margin-bottom: 1rem;
}

  .navbar-brand {
    margin-right: auto; /* Markayı sola hizala, diğer öğeleri sağa hizala */
  }
  
  
  .navbar-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .nav-item {
    color: #d6f9dd;
    text-decoration: none; 
    font-size: 1rem; 
  }
  
  .nav-link {
    color: #d6f9dd;
    text-decoration: none;
    padding: 0 1rem; /* Öğeler arasında eşit boşluk */
  }
  
  p {
    text-align: start;
    max-width: 600px;
    margin:0.5cm;
}

.bilgilendirme-notu {
    position: absolute;
    bottom: 5px; /* Alt kenardan konumlandırma */
    right: 5px; /* Sağ kenardan konumlandırma */
    background-color: rgba(0, 0, 0, 0.7); /* Yarı saydam siyah arka plan */
    color: #fff; /* Beyaz yazı rengi */
    padding: 5px 10px; /* İç boşluk */
    border-radius: 5px; /* Kenar yuvarlaklığı */
    font-size: 14px; /* Yazı boyutu */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Gölge efekti */
    z-index: 10; /* Diğer öğelerin üzerinde görünsün */
}
.navbar {
    position: fixed; /* Navbar'ı sabitler */
    top: 0; /* Sayfanın en üstüne yerleştirir */
    left: 0; /* Sol kenara yerleştirir */
    z-index: 1000; /* Diğer içeriklerin üzerinde olmasını sağlar */
    width: 100%; /* Genişliği tam ekran yapar */
    background-color: #333; /* Navbar arka plan rengi */
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 1rem;
}
  
  h2 {
    font-weight: 500;
    color: #444;
    text-align: center;
    margin-bottom: 1rem;
}

  .navbar-brand {
    margin-right: auto; /* Markayı sola hizala, diğer öğeleri sağa hizala */
  }
  
  
  .navbar-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .nav-item {
    color: #d6f9dd;
    text-decoration: none; 
    font-size: 1rem; 
  }
  
  .nav-link {
    color: #d6f9dd;
    text-decoration: none;
    padding: 0 1rem; /* Öğeler arasında eşit boşluk */
  }
  
  p {
    text-align: start;
    max-width: 600px;
    margin:0.5cm;
}

.vucut-tipi-container {
    max-width: 800px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    border-radius: 15px;
    text-align: center;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    border: 1px solid #ddd;
    position: relative; /* Pozisyon belirleme not için */
    padding: 30px;
}
.vucut-tipi-gorsel {
    position: relative;
    display: inline-block; /* Görseli satır içi blok olarak ayarla */
    margin-bottom: 20px; /* Görsel ve metin arasında boşluk */
}

.vucut-tipi-container p {
    color: #333;
    font-size: 24px; /* Metin boyutunu büyüttük */
    line-height: 1.6;
    font-weight: 600; /* Metni kalınlaştırdık */
}

.vucut-tipi-resim {
    width: 400px; /* Görselin genişliğini artırın */
    height: auto; /* Yüksekliği otomatik ayarlayın */
}
.vucut-tipi-container strong {
    color: #0056b3;
    font-size: 26px; /* Vurgulu metin için font boyutunu büyüttük */
    display: block; /* Blok seviyesinde görüntülemek için */
    margin-bottom: 10px; /* Vurgulu metin ve açıklama arasında boşluk ekledik */
}

    </style>
</head>
<body>
<div class="main-background" style="background-image: url('assets/images/arkap.jpg'); background-size: cover; background-repeat: no-repeat;">
    <nav class="navbar navbar-expand-lg">
        <div class="container1">
            <div class="navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="account/logout.php">Çıkış Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="asistan.php">Asistanım</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum/index.php">Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dietRecipes.php">Diyet Tarifleri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Mainpage.php">Ana Sayfa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
