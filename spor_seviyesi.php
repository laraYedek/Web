<?php
require 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
    header('Location: login.php');
    exit();
}

// Veritabanı bağlantı bilgileri
$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

$userId = $_SESSION['user_id'];
$sporGunSayisi = $_POST['sporGunSayisi'];
$antrenmanSuresi = $_POST['antrenmanSuresi'];
$antrenmanYogunlugu = $_POST['antrenmanYogunlugu'];

// Spor seviyesini belirleme
$sporSeviyesi = '';

if ($sporGunSayisi === '0' || $antrenmanSuresi === '0-30' || $antrenmanYogunlugu === 'dusuk') {
    $sporSeviyesi = 'Başlangıç';
} elseif ($sporGunSayisi === '1-2') {
    $sporSeviyesi = 'Orta';
} elseif ($sporGunSayisi === '3-4') {
    if (($antrenmanSuresi === '60+' && $antrenmanYogunlugu === 'orta') || ($antrenmanSuresi === '60+' && $antrenmanYogunlugu === 'yuksek')) {
        $sporSeviyesi = 'Uzman';
    } elseif ($antrenmanSuresi === '30-60' || $antrenmanYogunlugu === 'orta') {
        $sporSeviyesi = 'Orta';
    }
} elseif ($sporGunSayisi === '5+') {
    if ($antrenmanYogunlugu === 'yuksek') {
        $sporSeviyesi = 'Uzman';
    } else {
        $sporSeviyesi = 'Orta'; // 5 gün ve üzeri ama yoğunluk orta veya düşükse
    }
}

// Veritabanında kullanıcının spor seviyesini güncelle
$query = pg_prepare($conn, "update_sport_level", "UPDATE kullanicilar SET sport_level = $1 WHERE id = $2");
$result = pg_execute($conn, "update_sport_level", array($sporSeviyesi, $userId));

if ($result) {
    echo "<script>alert('Spor seviyeniz başarıyla $sporSeviyesi olarak güncellendi.'); window.location.href = 'MainPage.php';</script>";
} else {
    echo "<script>alert('Spor seviyenizi güncellerken bir hata oluştu.'); window.location.href = 'MainPage.php';</script>";
}

pg_close($conn);

?>
