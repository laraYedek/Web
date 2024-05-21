<?php
session_start();

$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . pg_last_error());
}

$hataMesaji = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    $sql = "SELECT id, sifre FROM kullanicilar WHERE mail = $1";
    $result = pg_prepare($conn, "login_query", $sql);

    if ($result) {
        $result = pg_execute($conn, "login_query", array($email));
        if ($result && pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            if (password_verify($sifre, $row['sifre'])) {
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['user_id'] = $row['id'];
                header("Location: ../MainPage.php");
                exit();
            } else {
                $hataMesaji = "Hatalı Şifre!";
            }
        } else {
            $hataMesaji = "Hatalı Eposta!";
        }
    } else {
        $hataMesaji = "Sorgu hazırlanırken bir hata oluştu: " . pg_last_error($conn);
    }
}

pg_close($conn);
?>

<?php if ($hataMesaji != ''): ?>
    <script>alert('<?php echo $hataMesaji; ?>');</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - FitCheck</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/log.css">

</head>
<body>
    <div class="login-background" style="background-image: url('../assets/images/foodbowl.jpg'); background-size: cover; background-repeat: no-repeat;">
        <div class="container login-container my-5">
            <h2 class="mb-4">Giriş Yap</h2>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Eposta</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="sifre" required>
                    <button type="button" id="sifreGosterGizle" class="password-toggle-button"><img src="../assets/images/hiddenEye.jpg" alt="sifre"></button>
                        <script>
                            const sifreInput = document.getElementById('password');
                                const sifreGosterGizle = document.getElementById('sifreGosterGizle');

                                sifreGosterGizle.addEventListener('click', function() {
                                    if (sifreInput.type === 'password') {
                                        sifreInput.type = 'text';
                                    } else {
                                        sifreInput.type = 'password';
                                    }
                                });
                        </script>
                </div>
                
                <div class="mb-3">
                    <a href="forgetPassw.html" class="link-primary">Şifremi Unuttum</a>
                </div>
                <button type="submit" class="btn btn-primary">Giriş Yap</button>
                <a href="../MainPage.php" class="btn btn-outline-primary">Ana Sayfa</a>
                <!-- Yeni eklenen kısım -->
                <p class="mt-3 additional-info">Hala kendin için bir adım atmadın mı? <a href="register.html" class="link-primary">Bize katıl.</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
