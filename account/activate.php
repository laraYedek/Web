<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$host = "localhost";
$user = "postgres";
$password = "123123";
$dbname = "FitCheck";
$port = "5432";

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

$email = ''; // Bu değişken, aşağıdaki JavaScript fonksiyonu için kullanılacak
$message = ''; // Kullanıcıya gösterilecek mesaj
date_default_timezone_set('Europe/Istanbul');

// Aktivasyon linki kontrolü
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = urldecode($_GET['email']);
    $secretKey = "gizli_anahtar";
    $verifyToken = hash('sha256', $email . $secretKey);

    $query = "SELECT * FROM kullanicilar WHERE mail = $1 AND token_expires > NOW()";
    $result = pg_query_params($conn, $query, array($email));

    if (pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        if ($token === $verifyToken) {
            if ($user['token_expires'] > date('Y-m-d H:i:s')) {
                $updateQuery = "UPDATE kullanicilar SET isactivated = TRUE, token_expires = NULL WHERE mail = $1";
                $updateResult = pg_query_params($conn, $updateQuery, array($email));

                if ($updateResult) {
                    $message = "Hesabınız başarıyla aktifleştirildi. Giriş yapabilirsiniz.";
                    $_SESSION['user_email'] = $email;
                    header('Location: login.php');
                    exit();

                } else {
                    $message = "Hesap aktifleştirme işlemi sırasında bir hata oluştu.";
                }
            } else {
                $message = "Aktivasyon linkinizin süresi dolmuş. Yeni bir link için lütfen yeniden deneyin.";
            }
        } else {
            $message = "Geçersiz aktivasyon linki. Lütfen linkinizi kontrol edin.";
        }
    } else {
        $message = "Bu e-posta adresiyle ilişkili aktif bir aktivasyon işlemi bulunamadı veya aktivasyon süreniz dolmuş.";
    }
}

pg_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasyon Sayfası</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/activate.css">
</head>
<body data-email="<?php echo htmlspecialchars($email); ?>"  style="background-image: url('../assets/images/glass_effect.jpg'); background-size: cover; background-repeat: no-repeat;">
    
    <div class="act_back" >
    <div id="activation-container">
       
        <div id="activation-info">Aktivasyon linkinizin süresi 5 dakika içerisinde dolacaktır.</div>
        <div id=timer></div> 
        <button id="resendButton" style="display:none;" onclick="window.location.href='resend_activation.php'">Aktivasyon Linkini Yeniden Gönder</button>
    </div>
    </div>
    <script>
    var countDownDate = new Date().getTime() + 5 * 60 * 1000; // 5 dakika sonra

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("timer").innerHTML = minutes + " dakika " + seconds + " saniye ";

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "Aktivasyon süreniz dolmuştur.";
            // Butonu göstermek yerine doğrudan yeniden aktivasyon sayfasına yönlendir
            window.location.href = 'resend_activation.php';
        }
    }, 1000);
    </script>
</body>
</html>
