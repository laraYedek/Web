<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';


// SMTP ile e-posta gönderme işlemi
$mail = new PHPMailer(true);
try {
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->isSMTP();
    
    $mail->Host = 'smtp-mail.outlook.com'; // SMTP sunucu adresi
    $mail->SMTPAuth = true;
    $mail->Username = 'sitefitcheck@outlook.com'; // SMTP kullanıcı adı (Outlook.com e-posta adresi)
    $mail->Password = 'fitcheck2024'; // SMTP şifresi
    $mail->Port = 587; // SMTP portu (genellikle 587 veya 465 olarak kullanılır)
    $mail->SMTPSecure = 'tls'; // Güvenlik protokolü (TLS)

    $mail->setFrom('sitefitcheck@outlook.com', 'FitCheck'); // Gönderici adı ve e-posta adresi
    $mail->addAddress('laranazbaki@gmail.com'); // Alıcı e-posta adresi
    $mail->isHTML(true);
    $mail->Subject = 'Test Mail'; // E-posta konusu
    $mail->Body = 'Selam, bu bir denemedir.'; // E-posta içeriği

    $mail->send();
    echo 'E-posta başarıyla gönderildi.';
} catch (Exception $e) {
    echo "E-posta gönderilemedi. PHPMailer Hata: {$mail->ErrorInfo}";
}
?>
