<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $sql = "SELECT user_id FROM password_reset_tokens WHERE token = $1 AND expiry_date > NOW()";
        $prep = pg_prepare($conn, "token_query", $sql);
        $result = pg_execute($conn, "token_query", array($token));

        if ($result && pg_num_rows($result) > 0) {
            $userId = pg_fetch_result($result, 0, 'user_id');
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE kullanicilar SET sifre = $1 WHERE id = $2";
            $prep = pg_prepare($conn, "update_password", $sql);
            $result = pg_execute($conn, "update_password", array($hashedPassword, $userId));

            if ($result) {
                // Başarıyla güncellendi, giriş sayfasına yönlendir
                header('Location: login.php?reset=success');
                exit;
            } else {
                $message = 'Sifre guncellenirken bir hata oluştu.';
            }
        } else {
            $message = 'Sifre sifirlama linki gecersiz veya suresi dolmus.';
        }
    } else {
        $message = 'Girilen sifreler eslesmiyor.';
    }
}

pg_close($conn);
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama - FitCheck</title>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
            background-image: url('../assets/images/glass_effect.jpg'); 
            background-size: cover; 
            background-repeat: no-repeat;
        }
        .reset-password-form {
            font-family: 'Quicksand', sans-serif;
            background-color: #69b5e091;
            padding: 70px;
            border-radius: 50px;
            box-shadow: -20px 18px 12px rgba(0, 0, 0, 0.1);
            max-width: 300px; /* Ensure form doesn't stretch beyond this max-width */
        }
        .title {
            font-size: 29px;
            font-weight: 300;
            color: #63366e;
            text-shadow: -20px -5px 3px rgba(0, 0, 0, 0.3);
            padding-bottom: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center form elements vertically */
        }
        .form-group {
            margin-bottom: 15px;
            width: 100%; /* Ensure full width within form */
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="password"] {
            font-family: 'Quicksand', sans-serif;
            width: 100%; /* Adjusted for padding */
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #ccc; /* Added border styling */
        }
        button[type="submit"] {
            font-family: 'Quicksand', sans-serif;
            padding: 10px 20px;
            background-color: #5cb546;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="reset-password-form">
    <div class="title">Şifre Sıfırlama Formu</div>
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="reset_password.php?token=<?php echo urlencode($token); ?>" method="post">
            <div class="form-group">
                <label for="new_password">Yeni Şifrenizi Girin:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Yeni Şifrenizi Tekrar Girin:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit">Şifre Sıfırla</button>
        </form>
    </div>
</body>
</html>
