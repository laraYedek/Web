<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hamburger Diyet Tarifleri</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/foodList.css">
   
</head>
<nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">FitCheck</a>
            <div class="navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Bu link her zaman görünür -->
                    <li class="nav-item">
                        <a class="nav-link" href="../MainPage.php">Ana Sayfa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script>
document.addEventListener('DOMContentLoaded', (event) => {
  const blurredItems = document.querySelectorAll('.blur');

  blurredItems.forEach(item => {
    item.addEventListener('click', () => {
      // Kullanıcıyı giriş veya kayıt sayfasına yönlendir
      window.location.href = 'login.html'; // Giriş sayfanızın URL'si
    });
  });
});
</script>
<?php
$host = "localhost";
$port = "5432";
$dbname = "FitCheck";
$user = "postgres";
$password = "123123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Veritabanı bağlantısını kontrol edin
if (!$conn) {
    die("Veritabanı bağlantısında hata: " . pg_last_error());
}
// SQL sorgusunu çalıştırın ve sonucu $result değişkenine atayın
$query = "SELECT yemek_adi, malzemeler, tarif_metni,kalori FROM tarifler WHERE tür = 'Hamburger'";
$result = pg_query($conn, $query);

// Sonucu kontrol edin ve işleyin
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        echo "<div class='tarif-kart'>";
        echo "<h2>Yemek Adı:</h2>";
        echo "<h3>" . $row["yemek_adi"] . "</h3>";
        echo "<h2>Kullanılan Malzemeler:</h2>";
        echo "<p>" . $row["malzemeler"] . "</p>";
        echo "<h2>Yemek Tarifi:</h2>";
        echo "<p>" . $row["tarif_metni"] . "</p>";
        echo "<h2> Toplam (Yaklaşık) Kalori: <h2>";
        echo "<h2>" .$row["kalori"]."<h2>";
        echo "</div>";
    }
} else {
    echo "Sorgu hatası: " . pg_last_error();
}

// PostgreSQL veritabanı bağlantısını kapatın
pg_close($conn);
?>
