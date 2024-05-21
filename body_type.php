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

date_default_timezone_set('Europe/Istanbul');

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = pg_prepare($conn, "query_user_gender", "SELECT cinsiyet FROM kullanicilar WHERE id = $1");
    $result = pg_execute($conn, "query_user_gender", array($userId));

    if ($row = pg_fetch_assoc($result)) {
        $gender = $row['cinsiyet'];
        echo "<script>var userGender = '{$gender}';</script>";
    }
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
    <link rel="stylesheet" href="assets/css/body_type.css"> 
</head>
<body>
    
<div class="main-background" style="background-image: url('assets/images/arkap.jpg'); background-size: cover; background-repeat: no-repeat;" >
    <nav class="navbar navbar-expand-lg">
        <div class="container1">
            <div class="navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="account/logout.php">Çıkış Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="asistan.php">Asistanım</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="forum/index.php">Forum</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dietRecipes.php">Diyet Tarifleri</a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="Mainpage.php">Ana Sayfa</a>
                        </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="cinsiyet">
           <!-- <button id="femaleBtn">Kadınlara Göre Vücut Tipleri</button>
            <button id="maleBtn">Erkeklere Göre Vücut Tipleri</button>  -->
    </div>
    <div class="mainInfo">
        

            <div class="bubble">
              <h2 >Vücut Tipleri ve Özellikleri Nelerdir?</h2> <p>Ektomorf, Endomorf ve Mezomorf olarak bilinen üç vücut tipi antrenman programı ya da diyet listesi çıkarırken de değerlendirilir. Çünkü tiplerin her biri kendine has özelliklere sahiptir. Yani hızlı kilo alıyor ama vermekte zorlanıyorsanız ya da beraber spora başladığınız arkadaşınızdan farklı gelişiyorsanız sebebi vücut tipinizin ait olduğu kategori olabilir.</p>
            </div>
        <div class="button-container">
        <div class="expandable-section">
            <button class="toggle-button">Ektomorf Vücut Tipi ve Özellikleri</button>
            <div class="expandable-content" style="display: none;">
                <p>Bu vücut tipinin en önemli özelliklerinden biri hızlı bir metabolizmaya sahip olmasıdır. Yediklerini hızla erittiği için kilo almakta zorlanabilir. Yiyorum ama kilo alamıyorum diyorsanız bu sizin vücut tipiniz olabilir. İnce ve uzun kas yapısına sahip olan bu vücut tipinin kas inşa etmesi oldukça zordur. Spordan istediğiniz verimi alamıyor ve beslenmenin doğru olduğunu düşünüyorsanız sebebi bu olabilir.Dar omuz, dar kalça, uzun bacaklar, uzun kollar, ince kemikler ve eklemler ektomorf vücut tipinin belirgin özelliklerindendir. Çoğunlukla uzun boylu ve ince siluete sahip insanlar bu kategoriye girer. Yağsız bir vücut olduğu için avantajlı görünse de kas yapması zor olduğundan dezavantajı da vardır</p>
                  <li>Omuzları incedir ve genişliği azdır.</li>
                  <li>Küçük eklemler ve kemik yapılarına sahiptirler.</li>
                  <li>Kasları yağsızdır ve kaz kazanımları oldukça zordur.</li>
                  <li>Göğüs bölgeleri düzdür.</li>
                  <li>Genellikle zayıftırlar ve omuzları küçük kısadır</li>
                  <li>Metabolizmaları hızlıdır.</li>    
            </div>
       </div>
       <div class="expandable-section" >
            <button class="toggle-button">Endomorf Vücut Tipi ve Özellikleri</button>
            <div class="expandable-content" style="display: none;">
                  <p>Ektomorf vücut tipinin aksine metabolizmaları oldukça yavaştır. Bu nedenle hızlı kilo alıp oldukça yavaş bir şekilde verebilirler. Metabolizma hızı nedeniyle kasların gelişmesi de oldukça zordur. Endomorf vücut tipine sahip insanları tanımanın en kolay yolu geniş bel ve kalçadır. Eklemler, kemikler geniş ve yuvarlak hatlıdır.Bu vücut tipine sahip insanlar kilo problemi nedeniyle diyet yaparken zorlanabilir. Kas kütlesi kazanmak ve sıkı egzersiz uygulamak zor olabilir. Çabuk yağlanan vücut yapısı nedeniyle ortaya çıkan kilo sorunu hareketi engellediği için çevik sayılmazlar. Buna rağmen sahip oldukları kaslar güçlü ve dolgundur. Yeni kaslar inşa etmek ve geliştirmek için antrenman ve beslenme konusunda uzman yardımına ihtiyaç duyabilirler</p>
                    <li>Kilo alımları çok kolaydır ve kilo vermekte zorluk yaşarlar.</li>
                    <li>Metabolizmaları yavaştır.</li>
                    <li>Kolay kas kütlesi edinebilirler.</li>
                    <li>Orta veya iri kemik yapısına sahiptirler.</li>
                    <li>Omuzları dardır.</li>
                    <li>Vücut yağı vücudun alt bölgelerine, özellikle alt karın, kalça ve uyluklara yerleşme eğilimi gösterdiğinden genellikle vücut tipi armut şeklindedir.</li>
                    <li>Dayanıklılıkları fazladır ve güç, vücut ağırlığı gerektiren sporlarda başarılıdırlar.</li>
                    <li>Geniş bir bel, geniş kemiklere ve yuvarlak bir gövdeye sahiptirler.</li>         
            </div>
       </div>

       <div class="expandable-section">
            <button class="toggle-button">Mezomorf Vücut Tipi ve Özellikleri</button>
               <div class="expandable-content" style="display: none;">
                  <p>Mezomorf vücut tipi ilk iki tipin tam arasında yer alır. Zayıf ve ince Ektomorf ile kilolu ve geniş Endomorf vücut tipinin ortasında kalan insanlar bu gruba girer. Düşük yağ oranını destekleyen hızlı metabolizmaları sayesinde şanslı insanlardır. Güçlü kaslara sahip olmalarının yanı sıra vücut gelişimi de oldukça belirgin hızda olur. Kilo almak ve vermek kolaydır</p>
                     <li>Atletik ve kas gelişimine yatkındırlar.</li>
                    <li>Ne fazla kiloludurlar ne de fazla zayıftırlar.</li>
                    <li>Geniş omuzlara, dar bir bele, orta kemik yapısına ve düşük vücut yağ yüzdesine sahiptirler.</li>
                    <li>Kilo almaya eğilimlidirler fakat kolay kilo verirler.</li>
                    <li>Kas kütleleri fazla olduğu için diğer vücut tiplerine oranla daha fazla enerji ihtiyaçları vardır.</li>   
              </div>
       </div>
     
        </div>
    <div class="container" id="container">
  <div class="caption" id="slider-caption">
    <div class="caption-heading">
      <h1>Vücut Tipleri</h1>
    </div>
    <div class="caption-subhead"><span></span></div>
    <a class="btn" href="#"></a>
  </div>
  <div class="left-col" id="left-col">
    <div id="left-slider">
      <ul class="nav">
        <li class="slide-up"><a href="#">^</a></li>
        <li class="slide-down"><a href="#" id="down_button">v</a></li>
      </ul>
    </div>
  </div>
  <div class="right-col">
    <img src="assets/images/bodyType_main.webp" alt="Açıklama">
  </div>
  
</div>
<div class="container mt-5">
    <div class="row">
            <h2 class="text-center mb-4">Vücut Şeklinizi Hesaplayın</h2>
            <div>
              <p class="vucutBilgi"> Vücut ölçümü, kişisel sağlık ve fitness hedeflerinize ulaşmanızda önemli bir yol gösterici olabilir. Yapılan ölçümler, vücut yapınızı anlamanızı ve uygun sağlık ile fitness programlarına karar vermenizi sağlar. </p>
            </div>
            <div class="form-section">
            <form action="body_measurements.php" method="post">
            <label for="gogusCevresi" class="form-label">Göğüs Çevresi</label>
            <div class="input-group">
                <input type="text" class="form-control" id="gogusCevresi" name="gogusCevresi" required>
                <span class="input-group-text"><i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Doğru ölçümü almak için ölçüm bandını göğsünüzün en dolu kısmından sararak ölçmeniz gerekmektedir.Ölçüm bandını sıkmamaya özen gösteriniz"></i></span>
            </div>
       
        <div class="mb-3">
            <label for="belCevresi" class="form-label">Bel Çevresi</label>
            <div class="input-group">
                <input type="text" class="form-control" id="belCevresi" name="belCevresi" required>
                <span class="input-group-text"><i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Bel Çevresi vücutta daha yüksek miktarda vücut yağı içerebilen bir alandır. Bu sebeple ölçüm sırasında mezurayı belinize sıkmadan ölçüm alınması gerekmektedir."></i></span>
            </div>
        </div>
        <div class="mb-3">
            <label for="kalcaCevresi" class="form-label">Kalça Çevresi</label>
            <div class="input-group">
                <input type="text" class="form-control" id="kalcaCevresi" name="kalcaCevresi" required>
                <span class="input-group-text"><i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="İnce giysileriniz ile ölçüm yapabilirsiniz.Yandan bakıldığında, kalçaların en geniş kısmına sarılacak şekilde ölçüm bandını kalçanın etrafına sarın. Bandı yere paralel tutarak ölçüm sonucunu kaydedin."></i></span>
            </div>
        </div>
        <div class="mb-3">
            <label for="ustKalcaCevresi" class="form-label">Üst Kalça Çevresi</label>
            <div class="input-group">
                <input type="text" class="form-control" id="ustKalcaCevresi" name="ustKalcaCevresi" required>
                <span class="input-group-text"><i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Bu yer, genellikle göbek deliğinin biraz yukarısında bulunmaktadır. Kaburgaların bitimi ile kalça kemiğinin en üst kısmı arasındaki orta noktadan ölçüm alınabilir"></i></span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Gönder</button>
        </div>
      
        <div class="image-section">
            <img src="assets/images/vucutSekli.png" alt="Vücut Şekilleri" class="img-fluid">
        </div>
    </form>
<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function () {
  $('[data-bs-toggle="tooltip"]').tooltip() // Bootstrap tooltip aktifleştirme
})
</script>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleButtons = document.querySelectorAll('.toggle-button');
    toggleButtons.forEach(function(toggleButton) {
        toggleButton.addEventListener('click', function() {
            var expandableContent = this.nextElementSibling;
            var isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
            toggleButton.setAttribute('aria-expanded', !isExpanded);
            expandableContent.style.display = isExpanded ? 'none' : 'block';
        });
    });
});
</script>


<script>
        document.addEventListener('DOMContentLoaded', function() {
            var femaleBtn = document.getElementById('femaleBtn');
            var maleBtn = document.getElementById('maleBtn');

            // Cinsiyete göre içerik yükleme fonksiyonu
            var loadContentForGender = function(gender) {
                console.log(gender + ' içeriği yükleniyor...');
                // AJAX isteği ile içerik yükleyebilirsiniz.
                // Örnek: loadContent('/path_to_content', {gender: gender});
            };

            // Kullanıcının cinsiyetine göre varsayılan içeriği yükle.
            loadContentForGender(userGender);

            // Buton etkinliklerini tanımla.
            femaleBtn.addEventListener('click', function() {
                loadContentForGender('female');
            });

            maleBtn.addEventListener('click', function() {
                loadContentForGender('male');
            });
        });
    </script>


    <script>
        let slide_data = [
  {
    'src':'assets/images/ekkto.webp',
    'title':'FıtCheck',
  },
  {
    'src':'assets/images/woman_type.webp', 
    'title':'FıtCheck',
  },
  {
    'src':'assets/images/slide4.webp', 
    'title':'FıtCheck',
  },
  {
    'src':'assets/images/slide5.jpg', 
    'title':'FıtCheck',
  },
  
];
let slides = [],
  captions = [];

let autoplay = setInterval(function(){
  nextSlide();
},5000);
let container = document.getElementById('container');
let leftSlider = document.getElementById('left-col');
// console.log(leftSlider);
let down_button = document.getElementById('down_button');
// let caption = document.getElementById('slider-caption');
// let caption_heading = caption.querySelector('caption-heading');

down_button.addEventListener('click',function(e){
  e.preventDefault();
  clearInterval(autoplay);
  nextSlide();
  autoplay;
});

for (let i = 0; i< slide_data.length; i++){
  let slide = document.createElement('div'),
      caption = document.createElement('div'),
      slide_title = document.createElement('div');
    
  slide.classList.add('slide');
  slide.setAttribute('style','background:url('+slide_data[i].src+')');
  caption.classList.add('caption');
  slide_title.classList.add('caption-heading');
  slide_title.innerHTML = '<h1>'+slide_data[i].title+'</h1>';
  
  switch(i){
    case 0:
        slide.classList.add('current');
        caption.classList.add('current-caption');
        break;
    case 1:
        slide.classList.add('next');
        caption.classList.add('next-caption');
        break;
    case slide_data.length -1:
      slide.classList.add('previous');
      caption.classList.add('previous-caption');
      break;
    default:
       break;       
  }
  caption.appendChild(slide_title);
  caption.insertAdjacentHTML('beforeend','<div class="caption-subhead"><span> </span></div>');
  slides.push(slide);
  captions.push(caption);
  leftSlider.appendChild(slide);
  container.appendChild(caption);
}
// console.log(slides);

function nextSlide(){
  // caption.classList.add('offscreen');
  
  slides[0].classList.remove('current');
  slides[0].classList.add('previous','change');
  slides[1].classList.remove('next');
  slides[1].classList.add('current');
  slides[2].classList.add('next');
  let last = slides.length -1;
  slides[last].classList.remove('previous');
  
  captions[0].classList.remove('current-caption');
  captions[0].classList.add('previous-caption','change');
  captions[1].classList.remove('next-caption');
  captions[1].classList.add('current-caption');
  captions[2].classList.add('next-caption');
  let last_caption = captions.length -1;
  
  // console.log(last);
  captions[last].classList.remove('previous-caption');
  
  let placeholder = slides.shift();
  let captions_placeholder = captions.shift();
  slides.push(placeholder); 
  captions.push(captions_placeholder);
}

let heading = document.querySelector('.caption-heading');


// https://jonsuh.com/blog/detect-the-end-of-css-animations-and-transitions-with-javascript/
function whichTransitionEvent(){
  var t,
      el = document.createElement("fakeelement");

  var transitions = {
    "transition"      : "transitionend",
    "OTransition"     : "oTransitionEnd",
    "MozTransition"   : "transitionend",
    "WebkitTransition": "webkitTransitionEnd"
  }

  for (t in transitions){
    if (el.style[t] !== undefined){
      return transitions[t];
    }
  }
}

var transitionEvent = whichTransitionEvent()
caption.addEventListener(transitionEvent, customFunction);

function customFunction(event) {
  caption.removeEventListener(transitionEvent, customFunction);
  console.log('animation ended');
}
    </script>
</body>

