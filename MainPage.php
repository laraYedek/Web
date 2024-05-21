<?php
session_start();
$isLoggedIn = isset($_SESSION['isLoggedIn']) ? $_SESSION['isLoggedIn'] : false;

// Kullanıcının formdaki seçimlerini oturumda saklama
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['sporGunSayisi'] = $_POST['sporGunSayisi'];
    $_SESSION['antrenmanSuresi'] = $_POST['antrenmanSuresi'];
    $_SESSION['antrenmanYogunlugu'] = $_POST['antrenmanYogunlugu'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCheck Ana Sayfa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>
    
    <div class="main-background" style="background-image: url('assets/images/sport.jpg'); background-size: cover; background-repeat: no-repeat;">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">FitCheck</a>
            <div class="navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account/logout.php">Çıkış Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="asistan.php">Asistanım</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">Profilim</a>
                        </li>
                        <li class="nav-item">
                                <a class="nav-link" href="forum/index.php">Forum</a>
                                </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account/login.html">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="account/register.html">Üye Ol</a>
                        </li>
                        <li class="nav-item">
                                <a class="nav-link" href="forum/index.php">Forum</a>
                                </li>
                    <?php endif; ?>
                    <!-- Bu link her zaman görünür -->
                    <li class="nav-item">
                        <a class="nav-link" href="dietRecipes.php">Diyet Tarifleri</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
      </body>
        <section class="hero-section">
               <div class="container">
                         <h1>FitCheck ile Sağlıklı Yaşam</h1>
                          <p>FitCheck ailesine katılarak serüveninizi başlatın.</p>
                    </div> </section>


                    <?php if ($isLoggedIn): ?>
<div class="container text-center my-5">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sporSeviyesiModal">Spor Seviyenizi Belirleyin</button>
</div>

<div class="modal fade" id="sporSeviyesiModal" tabindex="-1" aria-labelledby="sporSeviyesiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sporSeviyesiModalLabel">Spor Seviyenizi Belirleyin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="spor_seviyesi.php" method="post" id="sporSeviyesiForm">
                    <div class="mb-3">
                        <p>Haftada kaç gün spor yapıyorsunuz?</p>
                        <select name="sporGunSayisi" class="form-select" id="sporGunSayisi" required onchange="handleSporGunSayisiChange()">
                            <option value="">Seçiniz</option>
                            <option value="0" <?php echo isset($_SESSION['sporGunSayisi']) && $_SESSION['sporGunSayisi'] == "0" ? 'selected' : ''; ?>>Hiç yapmıyorum</option>
                            <option value="1-2" <?php echo isset($_SESSION['sporGunSayisi']) && $_SESSION['sporGunSayisi'] == "1-2" ? 'selected' : ''; ?>>1-2 Gün</option>
                            <option value="3-4" <?php echo isset($_SESSION['sporGunSayisi']) && $_SESSION['sporGunSayisi'] == "3-4" ? 'selected' : ''; ?>>3-4 Gün</option>
                            <option value="5+" <?php echo isset($_SESSION['sporGunSayisi']) && $_SESSION['sporGunSayisi'] == "5+" ? 'selected' : ''; ?>>5 Gün veya daha fazla</option>
                        </select>
                    </div>
                    <div id="digerSorular" style="display: none;">
                        <div class="mb-3">
                            <p>Antrenmanlarınız ortalama ne kadar sürüyor?</p>
                            <select name="antrenmanSuresi" class="form-select" required>
                                <option value="">Seçiniz</option>
                                <option value="0-30">30 dakikadan az</option>
                                <option value="30-60">30-60 dakika</option>
                                <option value="60+">60 dakikadan fazla</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <p>Antrenman yoğunluğunuz nasıl?</p>
                            <select name="antrenmanYogunlugu" class="form-select" required>
                                <option value="">Seçiniz</option>
                                <option value="dusuk">Düşük</option>
                                <option value="orta">Orta</option>
                                <option value="yuksek">Yüksek</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

                    
    <section class="app-description">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Uygulamamız Hakkında</h2>
                    <p>FitCheck ile fitness yolculuğunuzda profesyonel bir rehberiniz var. Uygulamamız, kişisel egzersiz seviyenize uygun spor paketlerini önererek, her hareketin doğru formda nasıl yapıldığını adım adım öğretir. Uygulama, sadece spor değil, sağlıklı yaşamın tüm yönleriyle ilgili kapsamlı bir rehberdir. Size özel hazırlanan egzersiz planları, form analizleri ve sağlıklı beslenme tavsiyeleri ile FitCheck, fitness hedeflerinize ulaşmanızda vazgeçilmez bir yardımcıdır.</p>
                    <h4>Peki nasıl?</h4>
                    <p>Avatarınızın yardımıyla, FitCheck'te sunulan egzersizlerin her birini doğru formda yapmanın inceliklerini keşfedin. Uygulamamız, gerçek zamanlı geri bildirimlerle hareketleri düzeltmenize ve antrenmanlarınızdan maksimum verim almanıza olanak tanır. FitCheck ile her hareket, fitness seviyenize uygun bir şekilde kişiselleştirilir ve fitness yolculuğunuz boyunca size rehberlik edilir.</p>
                    <h4>Fitcheck amacı nedir?</h4>
                    <p>FitCheck'in amacı, kullanıcıların spor salonunda ya da evde doğru egzersiz tekniklerini öğrenirken güvenli ve etkili bir şekilde antrenman yapmalarını sağlamaktır. Detaylı görsel ve yazılı talimatlar, sağlıklı yaşam bilgileri ve motivasyon ipuçları ile donatılmış olan FitCheck, sadece bir uygulamadan çok daha fazlasını sunar. Siz egzersiz yaparken FitCheck, her zaman yanınızdadır; sizi motive eder, ilerlemenizi takip eder ve sağlıklı yaşam alışkanlıkları kazanmanıza yardımcı olur. Web sitemiz ise, bu yolculukta ihtiyaç duyacağınız ek bilgileri sağlamak ve uygulamamızın sunduğu deneyimi tamamlamak için sizinledir.</p>
                </div>
                
                <div class="col-md-5">
                    <img src="assets/images/back.png" class="img-fluid max-width: 150%;" alt="Uygulama Ekran Görüntüsü">
                </div>
                
                    </div>
            </div>
            
    </section>

    <div class="container text-center my-5">
        <button class="btn btn-primary app-download-btn">Uygulamayı İndir</button>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p>&copy; 2023 FitCheck. Tüm hakları saklıdır.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function handleSporGunSayisiChange() {
        var sporGunSayisi = document.getElementById('sporGunSayisi').value;
        var digerSorularDiv = document.getElementById('digerSorular');
        var antrenmanSuresi = document.getElementsByName('antrenmanSuresi')[0];
        var antrenmanYogunlugu = document.getElementsByName('antrenmanYogunlugu')[0];
        
        if (sporGunSayisi === '0') {
            digerSorularDiv.style.display = 'none';
            antrenmanSuresi.required = false;
            antrenmanYogunlugu.required = false;
        } else {
            digerSorularDiv.style.display = 'block';
            antrenmanSuresi.required = true;
            antrenmanYogunlugu.required = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleSporGunSayisiChange(); // Sayfa yüklendiğinde spor gün sayısı seçeneğine göre diğer soruların görünürlüğünü ayarlar
    });
</script>
</body>
</html>
