<?php
session_start();
// Veritabanı bağlantı bilgileriniz
$host = "localhost";
$user = "postgres";
$password = "123123";
$dbname = "FitCheck";
$port = "5432";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = urldecode($_GET['email']);

    // Burada güvenliği artırmak için daha fazla kontrol yapabilirsiniz, örneğin tokenin süresini kontrol edebilirsiniz
    $verifyToken = hash('sha256', $email . 'gizli_anahtar'); // token oluştururken kullanılan zaman damgasını çıkardım çünkü doğrulama sırasında aynı zaman damgasına erişimimiz olmayacak

    // Token doğrulaması ve e-posta adresinin kontrolü
    $query = "SELECT * FROM kullanicilar WHERE mail = $1";
    $result = pg_query_params($conn, $query, array($email));

    if (pg_num_rows($result) > 0 && $token === $verifyToken) {
        // Kullanıcıyı aktif hale getirme sorgusu
        $updateQuery = "UPDATE kullanicilar SET isactivated = TRUE WHERE mail = $1";
        $updateResult = pg_query_params($conn, $updateQuery, array($email));

        if ($updateResult) {
            echo "<script>alert('Hesabınız başarıyla aktifleştirildi.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Hesap aktifleştirme işlemi sırasında bir hata oluştu.');</script>";
        }
    } else {
        echo "<script>alert('Geçersiz aktivasyon linki veya süresi dolmuş.');</script>";
    }
}

pg_close($conn);
?>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasyon Sayfası</title>
    <style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: linear-gradient(to right, #6dd5ed, #2193b0);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .activation-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 100%;
        max-width: 400px;
    }

    h1 {
     margin-top: 30px;
    color: #333;
    margin-bottom: 44px;
    font-weight: 600;

    }

    input[type="text"] {
        padding: 15px;
        margin-bottom: 20px;
        font-size:20px;
        border: none;
        border-radius: 50px;
        width: calc(100% - 30px);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    input[type="email"] {
        padding: 15px;
        margin-bottom: 20px;
        font-size:20px;
        border: none;
        border-radius: 50px;
        width: calc(100% - 30px);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    input[type="submit"] {
        padding: 15px 25px;
        border: none;
        border-radius: 50px;
        background-color: #ff416c;
        background-image: linear-gradient(to right, #ff4b2b, #ff416c);
        color: white;
        font-size:20px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease-out;
        width: 100%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    input[type="submit"]:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }

    ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #b1b1b1;
        opacity: 1; /* Firefox */
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #b1b1b1;
    }

    ::-ms-input-placeholder { /* Microsoft Edge */
        color: #b1b1b1;
    }
</style>

</head>
<body>
    <div class="activation-container">
        <h1>Hesap Aktivasyonu</h1>
            <form action="activate.php" method="post">
                <input type="email" name="email" placeholder="E-posta Adresiniz" required><br>
                <input type="text" name="activation_code" placeholder="Aktivasyon Kodunuz" required><br>
                <input type="submit" value="Aktifleştir">
            </form>
    </div>

</body>
</html>