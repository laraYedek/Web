<?php
session_start();

$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

$userId = $_SESSION['user_id'] ?? null;
$caloricDeficitMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['desired_weight'])) {
        $desiredWeight = $_POST['desired_weight'];
        $updateQuery = "UPDATE kullanicilar SET istenen_kilo = $1 WHERE id = $2";
        $updateResult = pg_query_params($conn, $updateQuery, array($desiredWeight, $userId));
        if ($updateResult) {
            echo "<script>alert('İstenen kilo başarıyla güncellendi.');</script>";
        } else {
            echo "<script>alert('İstenen kilo güncellenirken bir hata oluştu.');</script>";
        }
    }
    if (isset($_POST['weight'])) {
        $newWeight = $_POST['weight'];
        $updateQuery = "UPDATE kullanicilar SET kilo = $1 WHERE id = $2";
        $updateResult = pg_query_params($conn, $updateQuery, array($newWeight, $userId));
        if ($updateResult) {
            echo "<script>alert('Kilo başarıyla güncellendi.');</script>";
        } else {
            echo "<script>alert('Kilo güncellenirken bir hata oluştu.');</script>";
        }
    }
}

$userInfo = ['kilo' => '', 'cinsiyet' => '', 'boy' => '', 'kullanici_adi' => '', 'istenen_kilo' => '', 'bmi' => '', 'durum' => '', 'yas' => ''];

if ($userId) {
    $query = "SELECT kilo, cinsiyet, boy, kullanici_adi, istenen_kilo, yas FROM kullanicilar WHERE id = $1";
    $result = pg_query_params($conn, $query, array($userId));
    if ($result && pg_num_rows($result) > 0) {
        $userInfo = pg_fetch_assoc($result);
        $userInfo['bmi'] = calculateBMI($userInfo['kilo'], $userInfo['boy']);
        $userInfo['durum'] = healthStatus($userInfo['bmi']);
        // Hedef kilo belirlenmişse kalori açığını hesapla
        if (!empty($userInfo['istenen_kilo'])) {
            $caloricDeficitMessage = calculateCaloricDeficit($userInfo['kilo'], $userInfo['istenen_kilo']);
        }
    } else {
        echo "<script>alert('Kullanıcı bilgisi bulunamadı.');</script>";
    }
}

pg_close($conn);

function calculateBMI($weight, $height) {
    $heightInMeters = $height / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);
    return round($bmi, 1);
}

function healthStatus($bmi) {
    if ($bmi < 16) {
        return "Çok ciddi derecede zayıf";
    } elseif ($bmi >= 16 && $bmi <= 16.99) {
        return "Ciddi derecede zayıf";
    } elseif ($bmi >= 17 && $bmi <= 18.49) {
        return "Zayıf";
    } elseif ($bmi >= 18.5 && $bmi <= 24.99) {
        return "Normal kilolu";
    } elseif ($bmi >= 25 && $bmi <= 29.99) {
        return "Fazla kilolu";
    } elseif ($bmi >= 30 && $bmi <= 34.99) {
        return "Kilolu (Obez) - Sınıf I";
    } elseif ($bmi >= 35 && $bmi <= 39.99) {
        return "Kilolu (Obez) - Sınıf II";
    } else {
        return "Çok Kilolu (Aşırı Obez) - Sınıf III";
    }
}

// Kalori açığı hesapla
function calculateCaloricDeficit($currentWeight, $desiredWeight) {
    $weightDifference = $currentWeight - $desiredWeight;
    $caloriesPerKg = 7700; // 1 kg yağ yakmak için yaklaşık 7700 kalori gereklidir.
    $caloricDeficit = $weightDifference * $caloriesPerKg;

    if ($weightDifference > 0) {
        // Pozitif kalori açığı için motive edici mesaj
        return "💫 Hedefinize doğru bir adım daha! Hedef kilonuza ulaşabilmek için toplamda " . number_format(abs($caloricDeficit), 0, '.', ',') . " kalori açığı yaratmanız gerekiyor. Bu yolculukta her adımınız değerli ve biz de bu serüvende yanınızdayız! 🚀";
    } elseif ($weightDifference < 0) {
        // Negatif kalori açığı (kilo alma hedefi) için farklı bir mesaj
        return "🌟 Hedef kilonuza ulaşmak için " . number_format(abs($caloricDeficit), 0, '.', ',') . " kalori fazlası almanız gerekli. Her bir kalori, bu yolculukta size destek olacak! 🌈";
    } else {
        // Kilo koruma hedefi için nötr bir mesaj
        return "Mükemmel denge! 🌟 Mevcut kilonuzu korumak için günlük kalori alımınızı dengede tutuyorsunuz. Bu, sağlıklı bir yaşam tarzının önemli bir parçasıdır. 👏";
    }
}

$userDetailsJson = json_encode([
    'kilo' => $userInfo['kilo'],
    'boy' => $userInfo['boy'],
    'yas' => $userInfo['yas'],
    'cinsiyet' => $userInfo['cinsiyet'],
    'bmi' => $userInfo['bmi'],
    'durum' => $userInfo['durum'],
    'caloricDeficitMessage' => $caloricDeficitMessage
]);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/assist.css">
    <title>FitCheck</title>
    
</head>
<body>
    <header>
       <a href="MainPage.php"> <div class="logo">FitCheck  </div> </a>
       <section class="hero">
            <h1>FitCheck Asistanına Hoş geldiniz!</h1>
        </section>
        <div class="auth-links">
            <a href="account/logout.php">Çıkış Yap</a>
            <a href="dietRecipes.php" class="sign-up">Diyet Tarifleri</a>
            <a class="nav-link" href="profil.php">Profilim</a>
        
        </div>
    </header>
    <main>
    <section class="assist_info ">
    <div class="firstSec mb-5">
            <!--<p class="text-justify">FitCheck olarak amacımız, sağlık ve fitness hedeflerinize ulaşmanız için size destek olmaktır. Bu sayfada, mevcut durumunuzu değerlendirmenize ve hedeflerinize ulaşmak için gereken adımları planlamanıza yardımcı olacak bir dizi araç sunuyoruz.</p>
--></div>
        <div class="container">

<!-- BMR Kartı -->
<div class="info-card mb-5">
    <div class="card-inner">
        <div class="card-front">
            <h2>BMR (Temel Metabolizma Hızı) ve Günlük Kalori İhtiyacı Hesaplama</h2>
            <p><strong>BMR Nedir?</strong> BMR, vücudunuzun temel işlevlerini yerine getirirken günlük olarak harcadığı minimum kalori miktarıdır. Bu değer, yaşınız, cinsiyetiniz, ağırlığınız ve boyunuz gibi faktörlere göre değişir.</p>
        </div>
        <div class="card-back">
            <p><strong>Günlük Kalori İhtiyacı:</strong> Günlük kalori ihtiyacınız, BMR'nize ek olarak, günlük aktivitelerinizin ve egzersizlerinizin enerji gereksinimlerini de içerir. Bu sayfada, aktivite seviyenize göre ayarlanmış BMR kullanarak günlük kalori ihtiyacınızı hesaplayabilirsiniz.</p>
            <p><strong>Nasıl Hesaplıyoruz?</strong> BMR, vücudunuzun temel işlevlerini yerine getirirken günlük olarak yaktığı kalori miktarını ifade eder. Hesaplamada kullanılan formüller, genel kabul görmüş Harris-Benedict denklemlerine dayanmaktadır. Bu formüller, cinsiyet, yaş, ağırlık ve boy gibi faktörlere göre kişinin temel enerji ihtiyacını tahmin eder.</p>
        </div>
    </div>
</div>

<!-- Sağlık Durumu ve BMI Kartı -->
<div class="info-card mb-5">
    <div class="card-inner">
        <div class="card-front">
            <h2>Sağlık Durumu ve BMI</h2>
            <p><strong>BMI (Vücut Kitle İndeksi):</strong> BMI, vücut ağırlığınızın, boy uzunluğunuzun karesine bölünmesiyle hesaplanır ve genel bir sağlık durumu göstergesi olarak kullanılır. BMI değerinizi ve bu değere göre genel sağlık durumunuzu bu sayfada bulabilirsiniz.</p>
        </div>
        <div class="card-back">
        <p><strong>Not:</strong> Bu araçlar ve bilgiler, sağlıklı bir yaşam tarzına giden yolda size rehberlik etmek için tasarlanmıştır. Ancak, bireysel sağlık durumunuz ve hedefleriniz için en doğru bilgiyi almak amacıyla bir sağlık profesyoneli ile görüşmenizi öneririz.</p>
        </div>
    </div>
</div>

<!-- Nasıl Çalışır Kartı -->
<div class="info-card">
    <div class="card-inner">
        <div class="card-front">
            <h2>Nasıl Çalışır?</h2>
            <li>Kilonuzu ve İstenen Kilonuzu Güncelleyin.</li>
            <li>Günlük Kalori İhtiyacını Hesaplayın.</li>
            <li>BMI ve Sağlık Durumunuzu Öğrenin.</li>
        </div>
        <div class="card-back">
        <p><strong>Kilonuzu ve İstenen Kilonuzu Güncelleyin:</strong> Mevcut ve hedef kilonuzu güncellemek için düzenleme butonlarını kullanın. Bu, size özel tavsiyelerin daha doğru olmasını sağlar.</p>
            <p><strong>Günlük Kalori İhtiyacını Hesaplayın:</strong> Aktivite seviyenizi seçerek günlük olarak almanız gereken kalori miktarını hesaplayabilirsiniz. Bu, kilo verme veya kilo alma hedeflerinize ulaşmanız için bir rehberdir.</p>
            <p><strong>BMI ve Sağlık Durumunuzu Öğrenin:</strong> Boyunuz ve ağırlığınız ile BMI'nizi ve genel sağlık durumunuzu görüntüleyin. Bu bilgiler, sağlık hedeflerinize ulaşmak için nereden başlamanız gerektiği konusunda size fikir verebilir.</p>
        </div>
    </div>
</div>

<!--Vücut Tipi Kartı -->
<div class="info-card">
    <div class="card-inner">
        <div class="card-front">
            <h2>Vücut Tipleri Nedir?</h2>
            <p>Vücut tipleri, fiziksel olarak insan vücudunun kas, yağ, kemik ölçüsü ve şekline göre ortaya çıkan bedensel farklar ile sınıflandırılmasını ifade eder. Omurga ve bacak boyu uzunluğu, kalça kemiği genişliği, kemiklerin kıvrımı, deri kalınlığı, kilo, boy gibi birçok faktöre göre belirlenir. Kullanılan şemaya göre değerlendirilen unsurlar farklılaşabilir. Genetik olarak aileden gelen özellikler,
                 hormonlar, erişkinliğe kadar yapılan spor ve benzeri aktivitelerin yanı sıra beslenme de vücut tipi şekillenmesinde etkili olabilir</li>
        </div>
        <div class="card-back">
        <p>Vücut tipi doğru beslenme ve antrenman ile istenilen yönde şekillenebilir olsa da temelde aynı kalır. Bu nedenle kemik yapısını saran yağ ve kas kütlesini ancak sizin anatominize uygun antrenman ile istediğiniz yönde şekillendirebilirsiniz. Fakat bunu yaparken kemik ölçüsünün çoğunlukla aynı kalacağını bilmelisiniz.</p>
            <a class="bottom-button btn btn-primary" href="body_type.php"> Vücut Tipinizi Öğrenin!</a>
        </div>
    </div>
</div>

</div>
</section>

      </section>
                <section class="info-cards">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="info-card2">
                                    <h2>Mevcut Kilonuz</h2>
                                    <p><?php echo htmlspecialchars($userInfo['kilo']); ?> kg</p>
                                    <form method="post" style="display:none;" id="WeightForm">
                                        <input type="number" id="weight" name="weight" step="0.1" required>
                                        <button type="submit" class="btn-update">Güncelle</button>
                                        <a href="javascript:void(0)" onclick="cancelEditWeight()" class="btn-cancel">İptal</a>
                                    </form>
                             <a href="javascript:void(0)" class="editingen" onclick="editWeight()" id="editWeightButton">Düzenle</a>
                        </div>
                   </div>
              <div class="col-md-4">
         <div class="info-card2">
                                    <h2>İstenen Kilo</h2>
                                    <p><?php echo htmlspecialchars($userInfo['istenen_kilo']); ?> kg</p>
                                    <form method="post" style="display:none;" id="desiredWeightForm">
                                        <input type="number" id="desired_weight" name="desired_weight" step="0.1" required>
                                        <button type="submit" class="btn-update">Güncelle</button>
                                        <a href="javascript:void(0)" onclick="cancelEditDesiredWeight()" class="btn-cancel">İptal</a>
                                    </form>
                          <a href="javascript:void(0)" class="editingen" onclick="editDesiredWeight()" id="editDesiredWeightButton">Düzenle</a>
      </div>
 </div>

 <script>
    
            function editDesiredWeight() {
                var form = document.getElementById('desiredWeightForm');
                form.style.display = 'block';
                // Düzenle butonunu gizle
                var editButton = document.getElementById('editDesiredWeightButton');
                editButton.style.display = 'none';
            }
       
            function editWeight() {
                var form = document.getElementById('WeightForm');
                form.style.display = 'block';
                // Düzenle butonunu gizle
                var editButton = document.getElementById('editWeightButton');
                editButton.style.display = 'none';
            }

            function cancelEditWeight() {
                // Formu gizle
                var form = document.getElementById('WeightForm');
                form.style.display = 'none';
                // Düzenle butonunu göster
                var editButton = document.getElementById('editWeightButton');
                editButton.style.display = 'inline';
            }

            function cancelEditDesiredWeight() {
                // Formu gizle
                var form = document.getElementById('desiredWeightForm');
                form.style.display = 'none';
                // Düzenle butonunu göster
                var editButton = document.getElementById('editDesiredWeightButton');
                editButton.style.display = 'inline';
            }
            
</script>
<div class="col-md-4">
    <div class="info-card2">
         <h2>BMI Değeriniz</h2>
         <p><?php echo htmlspecialchars($userInfo['bmi']); ?></p>
         <h2>Sağlık Durumunuz</h2>
         <p><?php echo htmlspecialchars($userInfo['durum']); ?></p>
    </div>
</div>
 <div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h3 class="gunlukKalori">Günlük Almanız Gereken Kalori Miktarı</h3>
             <form id="calorieCalculator">
                <div class="mb-3">
                  <label for="activityLevel" class="form-label">Aktivite seviyenizi belirleyin.</label>
                    <select class="form-select" id="activityLevel" name="activityLevel" required>
                         <option value="">Lütfen aktivite seviyenizi seçiniz</option>
                                <option value="1.2">Sedanter (az veya hiç egzersiz)</option>
                                <option value="1.375">Hafif Aktif (haftada 1-3 gün egzersiz)</option>
                                <option value="1.55">Orta Aktif (haftada 3-5 gün egzersiz)</option>
                                <option value="1.725">Çok Aktif (haftada 6-7 gün egzersiz)</option>
                                <option value="1.9">Ekstra Aktif (çok ağır egzersiz/ fiziksel iş)</option>
                    </select>
                 </div>
                            <button type="button" id="calculate" class="btn btn-primary">Hesapla</button>
            </form>
              <p id="dailyCalorieIntake"></p>
                 </div>
            </div>
         </div>
     </div>
</div> 
<div id="caloricDeficitInfo"></div>
</section>
</main>

    <footer>
        <div class="container">
          <p>&copy; 2023 FitCheck. Tüm hakları saklıdır.</p>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
        document.addEventListener('DOMContentLoaded', function() {
            var userDetails = <?php echo json_encode($userInfo); ?>;
            var calculateButton = document.getElementById('calculate');

            if (calculateButton) {
                calculateButton.addEventListener('click', function() {
                    var activityLevel = document.getElementById('activityLevel').value;
                    if (userDetails && userDetails.kilo && userDetails.boy && userDetails.yas && activityLevel) {
                        var calorieIntake = calculateCalories(
                            parseInt(userDetails.yas), 
                            parseFloat(userDetails.kilo), 
                            parseFloat(userDetails.boy), 
                            userDetails.cinsiyet, 
                            parseFloat(activityLevel)
                        );
                        document.getElementById('dailyCalorieIntake').innerText = `Günlük almanız gereken kalori miktarı: ${calorieIntake.toFixed(2)} kalori`;
                    } else {
                        document.getElementById('dailyCalorieIntake').innerText = 'Lütfen tüm alanları doldurun ve aktivite seviyenizi seçin.';
                    }
                });
            }

            function calculateCalories(age, weight, height, gender, activityLevel) {
    var bmr;
    if (gender === 'Erkek') {
        bmr = (10 * weight) + (6.25 * height) - (5 * age) + 5;
    } else if (gender === 'Kadın') {
        bmr = (10 * weight) + (6.25 * height) - (5 * age) - 161;
    }
    return bmr * activityLevel;
}

        });
        var userDetails = <?php echo $userDetailsJson; ?>;
        var calculateButton = document.getElementById('calculate');
        
        if (calculateButton) {
            calculateButton.addEventListener('click', function() {
                var activityLevel = document.getElementById('activityLevel').value;
                if (userDetails.kilo && userDetails.boy && userDetails.yas && activityLevel) {
                    var calorieIntake = calculateCalories(
                        parseInt(userDetails.yas), 
                        parseFloat(userDetails.kilo), 
                        parseFloat(userDetails.boy), 
                        userDetails.cinsiyet, 
                        parseFloat(activityLevel)
                    );
                    document.getElementById('dailyCalorieIntake').innerText = `Günlük almanız gereken kalori miktarı: ${calorieIntake.toFixed(2)} kalori`;
                } else {
                    document.getElementById('dailyCalorieIntake').innerText = 'Lütfen tüm alanları doldurun ve aktivite seviyenizi seçin.';
                }
            });
        }

        if (userDetails.caloricDeficitMessage) {
            document.getElementById('caloricDeficitInfo').innerText = userDetails.caloricDeficitMessage;
        }

        function calculateCalories(age, weight, height, gender, activityLevel) {
            height = height / 100; // cm'den metre'ye çevir
            var bmr = gender === 'Erkek'
                ? 88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age)
                : 447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age);
            return bmr * activityLevel;
        }
        
    </script>
</body>
</html>

