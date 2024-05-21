<?php
session_start();

// İçerik türünü JSON olarak belirleyin
header('Content-Type: application/json');

// Veritabanı bağlantı bilgileri
$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

// Veritabanına bağlan
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    // Veritabanı bağlantı hatası durumunda JSON hatası gönder
    echo json_encode(['success' => false, 'error' => 'Veritabanı bağlantısı başarısız.']);
    exit();
}

// Kullanıcı ID'sini kontrol et
$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    // Kullanıcı oturum açmamışsa JSON hatası gönder
    echo json_encode(['success' => false, 'error' => 'Kullanıcı oturumu bulunamadı.']);
    exit();
}

// Mevcut kullanıcı verilerini al
$currentDataQuery = pg_query($conn, "SELECT mail, boy, kilo, cinsiyet, yas, dogum_tarihi FROM kullanicilar WHERE id = '{$userId}'");
$currentData = pg_fetch_assoc($currentDataQuery);

// POST isteğini JSON olarak al
$input = json_decode(file_get_contents('php://input'), true);

// Güncellenecek veriler, eğer set edilmemişse mevcut veriyi kullan
$mail = $input['mail'] ?? $currentData['mail'];
$boy = $input['boy'] ?? $currentData['boy'];
$kilo = $input['kilo'] ?? $currentData['kilo'];
$cinsiyet = $input['cinsiyet'] ?? $currentData['cinsiyet'];
$yas = $input['yas'] ?? $currentData['yas'];
$dogum_tarihi = $input['dogum_tarihi'] ?? $currentData['dogum_tarihi'];
$dogum_tarihi = date('Y-m-d', strtotime($dogum_tarihi));

// Güvenli bir şekilde veritabanında güncelleme yap
$query = pg_prepare($conn, "update_profile", "UPDATE kullanicilar SET mail = $1, boy = $2, kilo = $3, cinsiyet = $4, yas = $5, dogum_tarihi = $6 WHERE id = $7");
$result = pg_execute($conn, "update_profile", array($mail, $boy, $kilo, $cinsiyet, $yas, $dogum_tarihi, $userId));

// İşlem başarılıysa JSON yanıtı gönder
if ($result) {
    echo json_encode(['success' => true]);
} else {
    // Güncelleme işlemi başarısızsa JSON hatası gönder
    echo json_encode(['success' => false, 'error' => pg_last_error($conn)]);
}

// Veritabanı bağlantısını kapat
pg_close($conn);
?>
