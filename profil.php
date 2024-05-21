<?php
require 'vendor/autoload.php';
session_start();

// Veritabanı bağlantı bilgileri
$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";


$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

$userId = $_SESSION['user_id'] ?? null;
if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

// Kullanıcı adı ve spor seviyesi bilgilerini al
$query = pg_query($conn, "SELECT kullanici_adi, sport_level,mail,boy,kilo,cinsiyet,yas,dogum_tarihi,istenen_kilo FROM kullanicilar WHERE id = '{$userId}'");

if ($row = pg_fetch_assoc($query)) {
    $username = $row['kullanici_adi'];
    $sportLevel = $row['sport_level'];
    $mail=$row['mail'];
    $boy=$row['boy'];
    $kilo=$row['kilo'];
    $cinsiyet=$row['cinsiyet'];
    $yas=$row['yas'];
    $dogum_tarihi=$row['dogum_tarihi'];
    $istenen_kilo=$row['istenen_kilo'];
} else {
    $username = 'Bilinmiyor';
    $sportLevel = 'Belirtilmemiş';
}

$calculatedAge = calculateAge($dogum_tarihi);

if ($calculatedAge != $yas) {
    $updateQuery = "UPDATE kullanicilar SET yas = '{$calculatedAge}' WHERE id = '{$userId}'";
    pg_query($conn, $updateQuery);
}
 
function calculateAge($dogum_tarihi) {
    $birthDate = new DateTime($dogum_tarihi);
    $today = new DateTime('now');
    $age = $birthDate->diff($today)->y;
    return $age;
}
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Profili | FitCheck</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/profil.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
</head>
<body>
    
<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="MainPage.php">FitCheck</a>
 <div class="auth-links">
            <a href="account/logout.php">Çıkış Yap</a>
            <a href="dietRecipes.php" class="sign-up">Diyet Tarifleri</a>
            <a class="nav-link" href="asistan.php">Asistanım</a>
        </div>    
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <!-- Profil Bilgileri -->
        <div class="welcome-message">
             <h5>Hoş Geldin!</h5>
        </div>
        <div class="card user-info-card">
             <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($username); ?></h5>
                <p class="card-text">Fitness Seviyesi: <?php echo htmlspecialchars($sportLevel); ?></p>
        </div>
</div>
     
<div class="row">
        <!-- Hesap Bilgileri -->
        <div class="col-md-6">
            <div class="card mb-3">
                 <div class="card-header">
                      Hesap Bilgileri
                  </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">E-posta:
                <input type="text" id="mail" class="form-control" value="<?php echo htmlspecialchars($mail); ?>" readonly>
              <!--    <button onclick="editField('mail')" class="btn-edit">Düzenle</button>
              <button onclick="saveField('mail')" class="btn-save" id="mail-save" style="display:none;">Kaydet</button>-->
            </li>
            <li class="list-group-item">Boy:
                <input type="text" id="boy" class="form-control" value="<?php echo htmlspecialchars($boy); ?>" readonly>
                <button onclick="editField('boy')" class="btn-edit">Düzenle</button>
                <button onclick="saveField('boy')" class="btn-save" id="boy-save" style="display:none;">Kaydet</button>
            </li>
            <li class="list-group-item">Kilo:
                <input type="text" id="kilo" class="form-control" value="<?php echo htmlspecialchars($kilo); ?>" readonly>
                <button onclick="editField('kilo')" class="btn-edit">Düzenle</button>
                <button onclick="saveField('kilo')" class="btn-save" id="kilo-save" style="display:none;">Kaydet</button>
            </li>
            <li class="list-group-item">Yaş:
                <input type="text" id="yas" class="form-control" value="<?php echo htmlspecialchars($yas); ?>" readonly>
                <button onclick="editField('yas')" class="btn-edit">Düzenle</button>
                <button onclick="saveField('yas')" class="btn-save" id="yas-save" style="display:none;">Kaydet</button>
            </li>
            <li class="list-group-item">Cinsiyet:
            <select id="cinsiyet" class="form-control" onchange="enableSaveButton('cinsiyet')">
                <option value="Kadın" <?php echo ($cinsiyet == 'Kadın') ? 'selected' : ''; ?>>Kadın</option>
                <option value="Erkek" <?php echo ($cinsiyet == 'Erkek') ? 'selected' : ''; ?>>Erkek</option>
            </select>
            <button onclick="saveField('cinsiyet')" class="btn-save" id="cinsiyet-save" style="display:none;">Kaydet</button>
        </li>

            <li class="list-group-item">Doğum Tarihi:
            <input type="date" id="dogum_tarihi" class="form-control" value="<?php echo htmlspecialchars($dogum_tarihi); ?>" onchange="enableSaveButton('dogum_tarihi')">
                <button onclick="editField('dogum_tarihi')" class="btn-edit">Düzenle</button>
                <button onclick="saveField('dogum_tarihi')" class="btn-save" id="dogum_tarihi-save" style="display:none;">Kaydet</button>
            </li>      
        </ul>               
    </div>
</div>
 <!-- İlerleme ve Hedefler -->
<div class="col-md-6"> 
    <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">İlerleme ve Hedefler</h5>
                                                        <!-- Mevcut Kilo -->
                            <li class="list-group-item">Mevcut Kilo:
                                <input type="number" id="kilo" class="form-control" value="<?php echo htmlspecialchars($kilo); ?>" readonly>
                            </li>

                            <!-- Hedef Kilo -->
                            <li class="list-group-item">Hedef Kilo:
                            <input type="number" id="kilo" class="form-control" value="<?php echo htmlspecialchars($istenen_kilo); ?>" readonly>
                            </li>
                            <canvas id="weightProgressChart"></canvas>
    <script>
    // Chart.js ile basit bir çubuk grafik
    var ctx = document.getElementById('weightProgressChart').getContext('2d');
    var weightProgressChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mevcut Kilo', 'Hedef Kilo'],
            datasets: [{
                label: 'Kilo (kg)',
                data: [<?php echo $kilo; ?>, <?php echo $istenen_kilo ?? '0'; ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

   
    </script>
    <div class="container">
    <div class="row">
        <div class="col-md-6">
            <div id='progressCalendar'></div>
        </div>
    </div>
</div>

                    </div>
                 </div>   
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script> 
function editField(fieldId) {
    var field = document.getElementById(fieldId);
    var saveButton = document.getElementById(fieldId + '-save');
    field.readOnly = false;
    field.focus();
    saveButton.style.display = 'inline'; // Kaydet butonunu göster
}

function saveField(fieldId) {
    var field = document.getElementById(fieldId);
    var value = field.value; // Kullanıcının girdiği değeri al
    field.readOnly = true; // Alanı tekrar salt okunur yap

    var data = {
        'userId': '<?php echo $userId; ?>'
    };
    data[fieldId] = value; // Dinamik olarak veri nesnesine alan bilgisini ekle

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_profile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if(response.success) {
                console.log('Güncelleme başarılı!');
                // Güncelleme başarılıysa kullanıcıya bildirim yapabilirsiniz
            } else {
                console.error('Güncelleme başarısız: ' + response.error);
                // Hata mesajını kullanıcıya göster
            }
        }
    };
    xhr.send(JSON.stringify(data)); // Veriyi JSON olarak gönder

    var saveButton = document.getElementById(fieldId + '-save');
    saveButton.style.display = 'none'; // Kaydet butonunu gizle
}

function enableSaveButton(fieldId) {
    var saveButton = document.getElementById(fieldId + '-save');
    saveButton.style.display = 'inline'; // Kaydet butonunu göster
}


</script>
</body>
</html>