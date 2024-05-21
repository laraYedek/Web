<?php session_start(); 
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

function sendActivationEmail($recipientEmail, $activationLink) {
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
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Hesap Aktivasyonu';
        $mail->Body = "Merhaba, hesabinizi aktiflestirmek icin asagidaki baglantiya tiklayin: <a href='{$activationLink}'>Aktivasyon Linki</a>";

        $mail->send();
        
        return true;
    } catch (Exception $e) {
        return "E-posta gönderilemedi. PHPMailer Hata: " . $mail->ErrorInfo;
    }
}

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $kullaniciAdi = pg_escape_string($conn, $_POST["username"]);
    $eposta = pg_escape_string($conn, $_POST["email"]);
    $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT);
    $kilo = pg_escape_string($conn, $_POST["kilo"]);
    $boy = pg_escape_string($conn, $_POST["boy"]);
    $yas = pg_escape_string($conn, $_POST["yas"]);
    $cinsiyet = pg_escape_string($conn, $_POST["cinsiyet"]);

 
    $queryCheck = "SELECT * FROM kullanicilar WHERE kullanici_adi = $1 OR mail = $2";
    $stmtCheck = pg_prepare($conn, "check_user", $queryCheck);
    $resultCheck = pg_execute($conn, "check_user", array($kullaniciAdi, $eposta));

    //echo $eposta; 
    if (pg_num_rows($resultCheck) > 0) {
        echo "<script>
                alert('Kullanıcı adı veya e-posta adresi zaten kullanımda. Lütfen farklı bir kullanıcı adı veya e-posta adresi deneyin.');
                window.location.href='register.html';
              </script>";
        exit;
    }
    date_default_timezone_set('Europe/Istanbul');

  // Güvenli bir token oluşturma
    $token = hash('sha256', $eposta . 'gizli_anahtar');

    // Aktivasyon linkini oluşturma
    $activationLink = "http://localhost/FitCheck/account/activate.php?token=" . urlencode($token) . "&email=" . urlencode($eposta);
   
    $expires = date('Y-m-d H:i:s', strtotime('+5 minute'));

    // Token  süresini veritabanına kaydetme
    $query = "INSERT INTO kullanicilar (kullanici_adi, mail, sifre, kilo, boy, cinsiyet, yas, token_expires) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    $stmt = pg_prepare($conn, "register_user", $query);
    $result = pg_execute($conn, "register_user", array($kullaniciAdi, $eposta, $sifre, $kilo, $boy, $cinsiyet, $yas, $expires));

    if ($result) {
        $emailSent = sendActivationEmail($eposta, $activationLink);
        if ($emailSent === true) {
            // E-posta başarıyla gönderildikten sonra kullanıcının e-postasını session'a kaydet
            $_SESSION['user_email'] = $eposta;
            
            echo "<script>alert('Aktivasyon linki e-posta adresinize gönderildi. Lütfen e-postanızı kontrol edin.'); window.location.href='activate.php';</script>";
        } else {
            echo "<script>alert('E-posta gönderilemedi: $emailSent');</script>";
        }
    } else {
        echo "<script>alert('Kayıt sırasında bir hata oluştu.');</script>";
    }
    
}

pg_close($conn);
?>
