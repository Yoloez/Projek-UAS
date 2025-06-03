<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Orbyt - Landing page</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Leaflet CSS -->
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
     <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Heebo:wght@100..900&family=Petrona:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" href="../assets/img/kopi1.jpg" type="image/x-icon" />  
  </head>
  <body>
<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Orbyt</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <div class="nav-center mx-auto" id="contentarea">
        <a class="nav-link" href="#hero">Home</a>
        <a class="nav-link" href="#best">Product</a>
        <a class="nav-link" href="#about-us">About</a>
      </div>

      <div class="ms-lg-auto mt-3 mt-lg-0" id="contentarea">
        <a class="nav-link" href="../authentication/sign-up/index.php" id="nav-sign-up">Sign-up</a>
        <a class="nav-link" href="#footer">Contact us</a>
      </div>
    </div>
  </div>
</nav>

    <!-- <nav>
      <div class="nav-container">
        <h1>Orbyt</h1>
        <ul class="nav-list">
          <li><a href="#hero">Home</a></li>
          <li><a href="#">Shop</a></li>
          <li><a href="#about-us">About</a></li>
        </ul>
        <a href="#footer" style="font-family: petrona; font-size: 1.6rem; text-wrap: nowrap; text-decoration: none; color: black">Contact Us</a>
      </div>
    </nav> -->

    <div class="hero" id="hero">
      <!-- <img src="../assets/img/landing-ufo.jpg" alt="gambar-ufo" /> -->
      <div class="hero-desc">
        <h1 style="color: #fff; text-align: center; font-family: Cormorant; font-size: 128px; font-style: normal; font-weight: 400; line-height: normal;  height: 335px; flex-shrink: 0">ORBYT</h1>
        <h2 style="color: #fff; text-align: center; font-family: Cormorant; font-size: 96px; font-style: normal; font-weight: 400; line-height: normal; margin-top: -2rem">A CAFETARIA FROM ANOTHER GALAXY</h2>
      </div>
    </div>

    <section id="about-us">
      <div class="about-us">
        <img src="../assets/img/mockup-graphics-AIkFaeX9ILc-unsplash 1.png" alt="kopi-about-us" style="margin-top: 224px; "/>
        <div class="about-us-desc">
          <h2 style="color: #000; font-family: Cormorant; font-size: 32px; font-style: normal; font-weight: 400; line-height: normal">About Us</h2>
          <h1 style="color: #000; font-family: Cormorant; font-size: 64px; font-style: normal; font-weight: 700; line-height: normal">we believe that coffee is more than just fuel — it's a journey.</h1>
          <p style="color: #989898; font-family: Heebo; font-size: 24px; font-style: normal; font-weight: 500; line-height: normal; align-self: stretch">
            Inspired by imagination and crafted with passion, Orbyt was born as a space where flavors, stories, and design collide. From the first sip of coffee to the last bite of dessert, we invite you to escape the ordinary and enter a
            world where every detail is designed to surprise and delight.
          </p>
        </div>
      </div>
    </section>

    <section class="taste">
      <h3 style="color: #C4A163;
text-align: center;
font-family: Heebo;
font-size: 24px;
font-style: normal;
font-weight: 400;
line-height: normal;">LOVE TASTE</h3>
      <h1 style="color: #000;
      width: 896px;
text-align: center;
font-family: Cormorant;
font-size: 80px;
font-style: normal;
font-weight: 400;
line-height: normal;">FOR THE LOVE OF COFFEE AND BREAD</h1>
      <div class="taste-container">
        <div class="taste-item">
          <img src="../assets/svg/cafe 1.svg" alt="kopi" />
          <h2>Cafetaria</h2>
          <p>A warm little corner serving fresh brews and baked goods simple pleasures, done right.</p>
        </div>
        <div class="taste-item">
          <img src="../assets/svg/cheers 1.svg" alt="roti" />
          <h2>Moments</h2>
          <p>For slow mornings or lively chats — let coffee and bread accompany your favorite moments</p>
        </div>
        <div class="taste-item">
          <img src="../assets/svg/gift-box 1.svg" alt="kopi dan roti" />
          <h2>Handcrafted Flavor</h2>
          <p>Every cup and loaf is made with care — a gift of comfort, one sip and bite at a time</p>
      </div>
    </section>

    <section class="premium" id="premium">
      <div
        class="premium-desc"
        align="center"
        style="color: #fff; text-align: center; font-family: Cormorant; font-size: 96px; font-style: normal; font-weight: 400; line-height: normal; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%)"
      >
        PREMIUM QUALITY
      </div>
      <!-- <img src="../assets/img/green-abstract-3d-3840x2160-21975.jpg" alt="bg-detail" style="background-position: center; background-size: cover; background-repeat: no-repeat; width: 100%" /> -->
    </section>
<section class="best" id="best">

  <div class="container py-5" id="products">
    <h6 style="text-align: center;">OUR BEST</h6>
    <h1>OUR FRESH PRODUCTS</h1>
    <p class="text-muted mb-4" align="center" style="color: #8C8C8F;
text-align: center;
font-family: Heebo;
font-size: 24px;
font-style: normal;
font-weight: 400;
line-height: normal; margin: 4rem 0rem !important;">
Indulge in a curated selection of artisanal products, crafted to elevate your cafe experience with every sip and bite.
</p>

<div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="row">
        <div class="col-md-4">
          <div class="card product-card mb-3">
            <img src="../assets/img/kopi1.jpg" class="card-img-top" alt="Cucumber">
            <div class="card-body">
              <h5 class="card-title">Coffee Latte</h5>
              <p class="card-text">A smooth and creamy blend of rich espresso and steamed milk, topped with a light layer of foam.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
            <div class="card product-card mb-3">
              <img src="../assets/img/mango.jpg" class="card-img-top" alt="Tomato">
              <div class="card-body">
                <h5 class="card-title">Mango Mist</h5>
                <p class="card-text">Ripe mango blended with ice, milk, and a touch of mint syrup—naturally sweet and creamy.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card product-card mb-3" id="cappucino">
              <img src="../assets/img/cappucino.jpg" class="card-img-top" alt="Lettuce">
              <div class="card-body">
                <h5 class="card-title">Cappucino</h5>
                <p class="card-text">Espresso with steamed milk and velvety foam
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="carousel-item">
        <div class="row">
          <div class="col-md-4">
            <div class="card product-card mb-3">
              <img src="../assets/img/croissant.jpg" class="card-img-top" alt="Carrot">
              <div class="card-body">
                <h5 class="card-title">Croissant</h5>
                <p class="card-text">A flaky, golden pastry with delicate layers of buttery goodness, baked to perfection for an authentic French bakery experience.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card product-card mb-3">
              <img src="../assets/img/matcha.jpg" class="card-img-top" alt="Pepper">
              <div class="card-body">
                <h5 class="card-title">Matcha Cloud</h5>
                <p class="card-text">Japanese matcha shaken with cold milk and topped with a fluffy milk foam.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card product-card mb-3">
              <img src="../assets/img/blueberry.jpg" class="card-img-top" alt="Pepper">
              <div class="card-body">
                <h5 class="card-title">Blueberry Muffin</h5>
                <p class="card-text">A moist, fluffy muffin bursting with fresh blueberries and topped with a golden crumb.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>
</section>


<div class="btn" style="position: relative; width: 100%;">
  <button onclick="window.location.href='../authentication/sign-up/index.php'">ORDER NOW</button>
</div>


<section>
  <div id="map"></div>
</section>



   <footer class="footer" id="footer">
    <h1 style="color: #FFF;
text-align: center;
font-family: Cormorant;
font-size: 40px;
font-style: normal;
font-weight: 400;
line-height: normal; margin-top: -8rem;">Orbyt</h1>
  <div class="footer-content">
    <div class="footer-left">
      <div class="icon-left">
<img src="../assets/svg/hugeicons_maps.svg" alt="">
<p>Pogung Rejo No.423A,<br>Sleman, DIY</p>
      </div>
      <div class="icon-left">
<img src="../assets/svg/Vector.svg" alt="">
<p>hananfijananto@gmail.com</p>
      </div>

      <div class="icon-left">
<img src="../assets/svg/ri_phone-fill.svg" alt="">
<p>+6285865172878</p>
      </div>
    </div>
    
    <div class="footer-center">
      <p class="tagline">Where every bite takes you on a flavorful orbit<br>beyond the ordinary</p>
      <div class="social-icons">
        <a href="https://www.instagram.com/hnfja/" target="_blank"><span class="circle-icon"><img src="../assets/svg/iconoir_instagram.svg" alt="ig"></span></a>
        <a href="https://www.youtube.com/@hnnfja" target="_blank"><span class="circle-icon"><img src="../assets/svg/iconoir_youtube.svg" alt="yt"></span></a>
        <a href="https://www.tiktok.com/@yolo.ez?lang=en" target="_blank"><span class="circle-icon"><img src="../assets/svg/ic_baseline-tiktok.svg" alt="tt"></span></a>
      </div>
    </div>
    
    <div class="footer-right">
      <p><span class="day">MON–FRI</span> <span class="time">9.00 AM – 11.00 PM</span></p>
      <p><span class="day">SAT</span> <span class="time">9.00 AM – 4.00 PM</span></p>
      <p><span class="day">SUN</span> <span class="time">4.00 PM – 11.00 PM</span></p>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p style="color: #888;
text-align: center;
font-family: Heebo;
font-size: 20px;
font-style: normal;
font-weight: 400;
line-height: normal;margin-top: 25px;">Copyright © Orbyt. All rights reserved</p>
  </div>
</footer>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  </body>
</html>
