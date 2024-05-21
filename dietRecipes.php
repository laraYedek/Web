
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCheck Tarifler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/recipes.css">
    <script src="assets/js/main.js"></script>
    
    <?php
    session_start(); 
    $isLoggedIn = isset($_SESSION['isLoggedIn']) ? $_SESSION['isLoggedIn'] : false;
    ?>
    <style>
        .blur {
            filter: blur(8px);
            cursor: not-allowed;
        }
        .blur:hover {
            filter: blur(8px);
        }
    </style>
</head>
<body>
    <div class="recipes-background">
        <header>
            <header>
                <a href="MainPage.php" class="home-button"><i class="fa fa-home"></i> Ana Sayfa</a>
                <h1 class="baslik">FitCheck</h1>
                
            </header>
            
        </header>
        <main>
            <section class="recipe-section">
                <div id="tarifler">
                <div class="buton-container">
                    <!-- Tarif butonları -->
                    <a href="pages/HamburgerPage.php" class="buton"><img src="assets/images/hamburger.jpg" alt="Hamburger"><span class="buton-metin">Hamburger</span></a>
                    <a href="pages/AnaYemekPage.php" class="buton"><img src="assets/images/anaYemek.jpg" alt="Ana Yemek"><span class="buton-metin">Ana Yemek</span></a>
                    <a href="pages/PizzaPage.php" class="buton"><img src="assets/images/pizza.jpg" alt="Pizza"><span class="buton-metin">Pizza</span></a>
                    <a href="pages/SalataPage.php" class="buton"><img src="assets/images/salata.jpg" alt="Salata"><span class="buton-metin">Salata</span></a>
                    <a href="pages/MakarnaPage.php" class="buton"><img src="assets/images/makarna.jpg" alt="Makarna"><span class="buton-metin">Makarna</span></a> 
                </div>
                <div class="buton-container">
                    <a href="pages/DenizPage.php" class="buton"><img src="assets/images/denizUrunleri.jpg" alt="Balık"><span class="buton-metin">Deniz Ürünleri</span></a>
                    <a href="pages/KahvaltiPage.php" class="buton"><img src="assets/images/kahvaltı.jpg" alt="Kahvaltı"><span class="buton-metin">Kahvaltı</span></a>
                    <a href="pages/TavukPage.php" class="buton"><img src="assets/images/tavuk.jpg" alt="Tavuk"><span class="buton-metin">Tavuk</span></a>
                    <a href="pages/PideLahmacunPage.php" class="buton"><img src="assets/images/pide.jpg" alt="Pide"><span class="buton-metin">Pide & Lahmacun</span></a>
                    <a href="pages/TatlıPage.php" class="buton"><img src="assets/images/tatlı.jpg" alt="Tatlı"><span class="buton-metin">Tatlı</span></a>
                </div>
            </section>

            <div class="nutrition-disclaimer">
                  <p><h2>Lütfen unutmayın!</h2> Bu sitede sunulan tarif ve diyet önerileri genel bilgilendirme amaçlıdır ve kişisel sağlık durumunuz veya özel beslenme ihtiyaçlarınız göz önünde bulundurularak hazırlanmamıştır. Sağlık durumunuzla ilgili herhangi bir endişeniz varsa veya özelleştirilmiş bir diyet planı ihtiyacınız varsa, bir sağlık profesyoneli veya diyetisyenle görüşmelisiniz.</p>
            </div>

        </main>
        <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Kullanıcı giriş yapmamışsa, son 5 tarif butonunu bulanık yap
            if (!<?php echo json_encode($isLoggedIn); ?>) {
                const buttons = document.querySelectorAll('.buton-container:last-of-type .buton');
                buttons.forEach((button, index) => {
                    if (index >= 0) { 
                        button.classList.add('blur');
                        button.href = 'account/login.html'; // Giriş sayfanızın yolu
                        button.onclick = function() {
                            window.location.href = this.href; // Giriş sayfasına yönlendir
                            return false; // Normal link davranışını engelle
                        };
                    }
                });
            }
        });

    </script>
        <footer class="footer">
            <div class="container">
                <p>&copy; 2023 FitCheck. Tüm hakları saklıdır.</p>
            </div>
        </footer>
    </div>
</body>
</html>
