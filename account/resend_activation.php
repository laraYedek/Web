<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Veritabanı bağlantı bilgileri
$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

// Veritabanına bağlan
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}


function sendActivationEmail($email, $activationLink) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sitefitcheck@outlook.com';
        $mail->Password = 'fitcheck2024';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('sitefitcheck@outlook.com', 'FitCheck');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Hesap Aktivasyonu';
        $mail->Body = "Merhaba, hesabınızı aktifleştirmek için aşağıdaki bağlantıya tıklayın: <a href='{$activationLink}'>Aktivasyon Linki</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "E-posta gönderilemedi. PHPMailer Hata: " . $e->getMessage();
    }
}


if (!empty($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];

    // Güvenli bir token oluşturun ve süresini belirleyin
    $newToken = hash('sha256', $email . 'gizli_anahtar');
    $activationLink = "http://localhost/FitCheck/account/activate.php?token=" . urlencode($newToken) . "&email=" . urlencode($email);
    
  // E-postayı yeniden gönderin
  if (sendActivationEmail($email, $activationLink)) {
    echo "<div class='activation-message1'>Aktivasyon linkinizin süresi doldu.</div>";
    echo "<div class='activation-message'>Yeni aktivasyon linki e-posta adresinize gönderildi. Lütfen e-postanızı kontrol edin.</div>";
} else {
    echo "<div class='error-message'>Aktivasyon linki gönderilirken bir hata oluştu.</div>";
}
} else {
echo "<div class='error-message'>Oturum bilgileri bulunamadı. Lütfen giriş yapın.</div>";
}

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasyon Sayfası</title>
    <style>
         body {
        font-family: 'Open Sans', sans-serif;
        background-image: url('../assets/images/duck.jpeg'); /* url() fonksiyonunu kullanın */
        background-size: cover; /* Resmin tam sayfa kaplamasını sağlar */
        background-position: center; /* Resmi sayfanın ortasına alır */
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        text-align: center;
    }
    .message-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 90%;
        margin: 20px;
        color: #333;
    }
    .activation-message {
        color: #4CAF50; /* yeşil */
        background: #DDFFDD;
        padding: 10px;
        height: 20px;
        margin: 10px 0;
        border: 1px solid #C2FCC2;
        border-radius: 5px;
        text-align: center;
    }
    .activation-message1 {
        color:#333;
        background: #ff7e67;
        padding: 10px;
        position: absolute;
        top: 350px;
        margin: 10px 0;
        border-radius: 5px;
        text-align: center;
    }
    .error-message {
        color: #F44336; /* kırmızı */
        background: #FFDDDD;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #FFC2C2;
        border-radius: 5px;
        display: block;
        text-align: center;
    }
  
    
    </style>
</head>
