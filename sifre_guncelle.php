
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

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eposta = pg_escape_string($conn, $_POST["email"]);
    $activation_code = pg_escape_string($conn, $_POST["activation_code"]);
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Yeni şifrelerin eşleşip eşleşmediğini kontrol et
    if ($new_password != $confirm_password) {
        echo "<script>alert('Yeni şifreler eşleşmiyor.');</script>";
        exit;
    }

    // Kullanıcının e-posta adresi ve aktivasyon kodu doğruysa şifreyi güncelle
    $query = "SELECT * FROM kullanicilar WHERE mail = $1 AND activation_code = $2";
    $result = pg_query_params($conn, $query, array($eposta, $activation_code));

    if (pg_num_rows($result) > 0) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE kullanicilar SET sifre = $1 WHERE mail = $2";
        $updateResult = pg_query_params($conn, $updateQuery, array($hashed_password, $eposta));

        if ($updateResult) {
            echo "Şifreniz başarıyla güncellendi.";
        } else {
            echo "Şifre güncelleme işlemi başarısız oldu.";
        }
    } else {
        echo "Geçersiz istek.";
    }
}
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Şifre Belirleme</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #60a3bc; /* Spor temalı bir arka plan rengi */
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #0a3d62; /* Koyu mavi bir arka plan rengi */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
        }

        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #0a3d62;
        }

        button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #78e08f; /* Canlı yeşil bir buton rengi */
            color: #0a3d62;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #38ada9;
        }

        input::placeholder {
            color: #b8c6db;
        }
    </style>
</head>
<body>
    <form action="sifre_guncelle.php" method="POST">
        <input type="hidden" name="email" value="<?php echo $eposta; ?>">
        <input type="hidden" name="activation_code" value="<?php echo $activation_code; ?>">
        <input type="password" name="new_password" placeholder="Yeni Şifreniz" required>
        <input type="password" name="confirm_password" placeholder="Yeni Şifrenizi Tekrar Girin" required>
        <button type="submit">Şifreyi Güncelle</button>
    </form>
</body>
</html>