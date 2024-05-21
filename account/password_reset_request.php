<?php
session_start();
require __DIR__ . '/../vendor/autoload.php'; // PHPMailer için autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
date_default_timezone_set('Europe/Istanbul');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = pg_escape_string($conn, $_POST['email']);
    
    // Kullanıcının e-postasını kontrol et
    $sql = "SELECT id FROM kullanicilar WHERE mail = $1";
    $result = pg_prepare($conn, "user_query", $sql);
    $result = pg_execute($conn, "user_query", array($email));
    
    if ($result && pg_num_rows($result) > 0) {
        $userRow = pg_fetch_assoc($result);
        $userId = $userRow['id']; // Kullanıcının ID'sini al

        // Güvenli bir token oluştur
        $token = bin2hex(random_bytes(50));
        
        // Token son kullanma tarihini ayarla
        $expiry_date = new DateTime('NOW');
        $expiry_date->add(new DateInterval('PT01H')); // 1 saat sonrası için ayarla
        $expiry_date = $expiry_date->format('Y-m-d H:i:s');
        
        // Token'ı veritabanına kaydet
        $insertTokenSQL = "INSERT INTO password_reset_tokens (token, user_id, expiry_date) VALUES ($1, $2, $3)";
        $insertTokenResult = pg_prepare($conn, "insert_token", $insertTokenSQL);
        $insertTokenResult = pg_execute($conn, "insert_token", array($token, $userId, $expiry_date));
        
        if (!$insertTokenResult) {
            // Token kaydedilemedi, bir hata mesajı göster
            echo "Bir hata oluştu ve şifre sıfırlama linki gönderilemedi.";
            exit;
        }

        // E-posta gönder
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
            $mail->Subject = 'Sifre Sifirlama Talebi';
            $resetLink = "http://localhost/FitCheck/account/reset_password.php?token=" . urlencode($token);
            $mail->Body = "Sifrenizi sifirlamak icin asagidaki linke tiklayin: <a href='{$resetLink}'>Sifre Sifirla</a>";

            $mail->send();
        } catch (Exception $e) {
            echo "E-posta gönderilemedi. Hata: " . $mail->ErrorInfo;
        }
    } else {
        echo "Bu e-posta adresi ile kayitli bir hesap bulunamadi.";
    }
}
$mesaj = "Şifre sıfırlama talimatları e-posta adresinize gönderildi. Bu sayfayı kapatabilirsiniz.";

pg_close($conn);
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fcebeb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .mesaj-kutusu {
            background-color: white;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .mesaj-kutusu h2 {
            margin: 0 0 10px 0;
        }
        .mesaj-kutusu p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="mesaj-kutusu">
    <h2>İşlem Başarılı!</h2>
    <p><?php echo $mesaj; ?></p>
</div>

</body>
</html>