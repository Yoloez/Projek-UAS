<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Orbyt - Landing page</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant:wght@400;700&family=Heebo:wght@400;500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" href="../assets/img/kopi1.jpg" type="image/x-icon" />

    <style>
      /* Kelas awal untuk elemen yang akan dianimasikan saat scroll */
      .animate-on-scroll {
        opacity: 0;
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
      }
      .fade-up { transform: translateY(50px); }
      .fade-down { transform: translateY(-50px); }
      .fade-left { transform: translateX(-50px); }
      .fade-right { transform: translateX(50px); }
      .zoom-in { transform: scale(0.9); }
      .animate-on-scroll.is-visible {
        opacity: 1;
        transform: none;
      }
      .stagger-1 { transition-delay: 0.1s; }
      .stagger-2 { transition-delay: 0.2s; }
      .stagger-3 { transition-delay: 0.3s; }

      /* Animasi untuk Navbar saat scroll */
      .navbar.scrolled {
        background-color: rgba(0, 0, 0, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: background-color 0.4s ease;
      }

      /* Animasi untuk Tombol Order Now */
      .btn-order-now button {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .btn-order-now button:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
      }
      
      /* === CSS BARU UNTUK CAROUSEL KUSTOM === */
      .carousel-wrapper {
        position: relative;
        width: 100%;
        padding: 0 40px; /* Ruang untuk tombol navigasi */
      }

      .custom-carousel {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        /* Sembunyikan scrollbar */
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE */
      }
      .custom-carousel::-webkit-scrollbar { /* Chrome, Safari */
        display: none;
      }

      .custom-carousel .product-card {
        flex: 0 0 80%; /* Lebar kartu di mobile (1 kartu per layar) */
        scroll-snap-align: center;
        margin: 10px;
        /* Menggunakan kembali style .card dan .product-card dari style.css Anda */
      }

      .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: background-color 0.3s ease;
        z-index: 10;
      }
      .carousel-btn:hover {
        background-color: #fff;
      }
      .carousel-btn.prev { left: -10px; }
      .carousel-btn.next { right: -10px; }

      /* Tampilan Desktop (3 Kartu) */
      @media (min-width: 768px) {
        .custom-carousel .product-card {
          flex-basis: calc(100% / 2 - 20px); /* 2 kartu di tablet */
        }
      }
       @media (min-width: 992px) {
        .custom-carousel .product-card {
          flex-basis: calc(100% / 3 - 20px); /* 3 kartu di desktop */
        }
      }
    </style>
  </head>
  <body>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top animate-on-scroll fade-down">
      <div class="container-fluid">
        <a class="navbar-brand" href="../landing/index.php">Orbyt</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <div class="nav-center mx-auto" id="contentarea">
            <a class="nav-link" href="#hero">Home</a>
            <a class="nav-link" href="#best">Product</a>
            <a class="nav-link" href="#about-us">About</a>
          </div>
          <div class="ms-lg-auto mt-3 mt-lg-0" id="contentarea">
            <a class="nav-link" href="authentication/sign-up/sign-up.php" id="nav-sign-up">Sign-up</a>
            <a class="nav-link" href="#footer">Contact us</a>
          </div>
        </div>
      </div>
    </nav>

    <div class="hero" id="hero">
      <div class="hero-desc">
        <h1 class="animate-on-scroll zoom-in" style="color: #fff; text-align: center; font-family: Cormorant; font-size: 128px; font-style: normal; font-weight: 400; line-height: normal; height: 335px; flex-shrink: 0">ORBYT</h1>
        <h2 class="animate-on-scroll zoom-in stagger-1" style="color: #fff; text-align: center; font-family: Cormorant; font-size: 96px; font-style: normal; font-weight: 400; line-height: normal; margin-top: -2rem">A CAFETARIA FROM ANOTHER GALAXY</h2>
      </div>
    </div>

    <section id="about-us">
      <div class="about-us">
        <img src="assets/img/mockup-graphics-AIkFaeX9ILc-unsplash 1.png" alt="kopi-about-us" style="margin-top: 224px;" class="animate-on-scroll fade-right"/>
        <div class="about-us-desc animate-on-scroll fade-left">
          <h2 style="color: #000; font-family: Cormorant; font-size: 32px; font-style: normal; font-weight: 400; line-height: normal">About Us</h2>
          <h1 style="color: #000; font-family: Cormorant; font-size: 64px; font-style: normal; font-weight: 700; line-height: normal">we believe that coffee is more than just fuel — it's a journey.</h1>
          <p style="color: #989898; font-family: Heebo; font-size: 24px; font-style: normal; font-weight: 500; line-height: normal; align-self: stretch">
            Inspired by imagination and crafted with passion, Orbyt was born as a space where flavors, stories, and design collide. From the first sip of coffee to the last bite of dessert, we invite you to escape the ordinary and enter a world where every detail is designed to surprise and delight.
          </p>
        </div>
      </div>
    </section>

    <section class="taste">
      <h3 class="animate-on-scroll fade-up" style="color: #C4A163; text-align: center; font-family: Heebo; font-size: 24px; font-style: normal; font-weight: 400; line-height: normal;">LOVE TASTE</h3>
      <h1 class="animate-on-scroll fade-up stagger-1" style="color: #000; width: 896px; text-align: center; font-family: Cormorant; font-size: 80px; font-style: normal; font-weight: 400; line-height: normal;">FOR THE LOVE OF COFFEE AND BREAD</h1>
      <div class="taste-container">
        <div class="taste-item animate-on-scroll fade-up stagger-1">
          <img src="assets/svg/cafe 1.svg" alt="kopi" />
          <h2>Cafetaria</h2>
          <p>A warm little corner serving fresh brews and baked goods simple pleasures, done right.</p>
        </div>
        <div class="taste-item animate-on-scroll fade-up stagger-2">
          <img src="assets/svg/cheers 1.svg" alt="roti" />
          <h2>Moments</h2>
          <p>For slow mornings or lively chats — let coffee and bread accompany your favorite moments</p>
        </div>
        <div class="taste-item animate-on-scroll fade-up stagger-3">
          <img src="assets/svg/gift-box 1.svg" alt="kopi dan roti" />
          <h2>Handcrafted Flavor</h2>
          <p>Every cup and loaf is made with care — a gift of comfort, one sip and bite at a time</p>
        </div>
      </div>
    </section>

    <section class="premium" id="premium">
      <div class="premium-desc animate-on-scroll zoom-in" align="center" style="color: #fff; text-align: center; font-family: Cormorant; font-size: 96px; font-style: normal; font-weight: 400; line-height: normal; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%)">
        PREMIUM QUALITY
      </div>
    </section>

    <section class="best" id="best">
      <div class="container py-5">
        <h6 style="text-align: center;color: #C4A163; text-align: center; font-family: Heebo; font-size: 24px; font-style: normal; font-weight: 400; line-height: normal; margin-top:4rem;" class="animate-on-scroll fade-up" >OUR BEST</h6>
        <h1 class="animate-on-scroll fade-up stagger-1" style="font-family: cormorant; text-align: center; font-size:80px;">OUR FRESH PRODUCTS</h1>
        <p class="text-muted mb-4 animate-on-scroll fade-up stagger-2" align="center" style="color: #8C8C8F; text-align: center; font-family: Heebo; font-size: 24px; font-style: normal; font-weight: 400; line-height: normal; margin: 4rem 0rem !important;">
          Indulge in a curated selection of artisanal products, crafted to elevate your cafe experience with every sip and bite.
        </p>
    
        <div class="carousel-wrapper animate-on-scroll fade-up stagger-3">
          <div class="custom-carousel" id="customCarousel">
            
            <div class="card product-card">
              <img src="assets/img/kopi1.jpg" class="card-img-top" alt="Coffee Latte">
              <div class="card-body">
                <h5 class="card-title">Coffee Latte</h5>
                <p class="card-text">A smooth and creamy blend of rich espresso and steamed milk, topped with a light layer of foam.</p>
              </div>
            </div>
            
            <div class="card product-card">
              <img src="assets/img/mango.jpg" class="card-img-top" alt="Mango Mist">
              <div class="card-body">
                <h5 class="card-title">Mango Mist</h5>
                <p class="card-text">Ripe mango blended with ice, milk, and a touch of mint syrup—naturally sweet and creamy.</p>
              </div>
            </div>
    
            <div class="card product-card" id="cappucino">
              <img src="assets/img/cappucino.jpg" class="card-img-top" alt="Cappucino">
              <div class="card-body">
                <h5 class="card-title">Cappucino</h5>
                <p class="card-text">Espresso with steamed milk and velvety foam.</p>
              </div>
            </div>
    
            <div class="card product-card">
              <img src="assets/img/croissant.jpg" class="card-img-top" alt="Croissant">
              <div class="card-body">
                <h5 class="card-title">Croissant</h5>
                <p class="card-text">A flaky, golden pastry with delicate layers of buttery goodness, baked to perfection.</p>
              </div>
            </div>
    
            <div class="card product-card">
              <img src="assets/img/matcha.jpg" class="card-img-top" alt="Matcha Cloud">
              <div class="card-body">
                <h5 class="card-title">Matcha Cloud</h5>
                <p class="card-text">Japanese matcha shaken with cold milk and topped with a fluffy milk foam.</p>
              </div>
            </div>
    
            <div class="card product-card">
              <img src="assets/img/blueberry.jpg" class="card-img-top" alt="Blueberry Muffin">
              <div class="card-body">
                <h5 class="card-title">Blueberry Muffin</h5>
                <p class="card-text">A moist, fluffy muffin bursting with fresh blueberries and topped with a golden crumb.</p>
              </div>
            </div>
    
          </div>
          <button class="carousel-btn prev" id="prev-btn">&#8249;</button>
          <button class="carousel-btn next" id="next-btn">&#8250;</button>
        </div>
      </div>
    </section>

    <div class="btn btn-order-now animate-on-scroll zoom-in" style="position: relative; width: 100%;">
      <button onclick="window.location.href='authentication/sign-up/sign-up.php'">ORDER NOW</button>
    </div>

    <section>
      <div id="map"></div>
    </section>

    <footer class="footer" id="footer">
      <h1 style="color: #FFF; text-align: center; font-family: Cormorant; font-size: 40px; font-style: normal; font-weight: 400; line-height: normal; margin-top: -8rem;">Orbyt</h1>
      <div class="footer-content">
        <div class="footer-left">
          <div class="icon-left">
            <img src="assets/svg/hugeicons_maps.svg" alt="">
            <p>Pogung Rejo No.423A,<br>Sleman, DIY</p>
          </div>
          <div class="icon-left">
            <img src="assets/svg/Vector.svg" alt="">
            <p>hananfijananto@gmail.com</p>
          </div>
          <div class="icon-left">
            <img src="assets/svg/ri_phone-fill.svg" alt="">
            <p>+6285865172878</p>
          </div>
        </div>
        <div class="footer-center">
          <p class="tagline">Where every bite takes you on a flavorful orbit<br>beyond the ordinary</p>
          <div class="social-icons">
            <a href="https://www.instagram.com/hnfja/" target="_blank"><span class="circle-icon"><img src="assets/svg/iconoir_instagram.svg" alt="ig"></span></a>
            <a href="https://www.youtube.com/@hnnfja" target="_blank"><span class="circle-icon"><img src="assets/svg/iconoir_youtube.svg" alt="yt"></span></a>
            <a href="https://www.tiktok.com/@yolo.ez?lang=en" target="_blank"><span class="circle-icon"><img src="assets/svg/ic_baseline-tiktok.svg" alt="tt"></span></a>
          </div>
        </div>
        <div class="footer-right">
          <p><span class="day">MON–FRI</span> <span class="time">9.00 AM – 11.00 PM</span></p>
          <p><span class="day">SAT</span> <span class="time">9.00 AM – 4.00 PM</span></p>
          <p><span class="day">SUN</span> <span class="time">4.00 PM – 11.00 PM</span></p>
        </div>
      </div>
      <div class="footer-bottom">
        <p style="color: #888; text-align: center; font-family: Heebo; font-size: 20px; font-style: normal; font-weight: 400; line-height: normal;margin-top: 25px;">Copyright © Hanan Fijananto. All rights reserved</p>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="script.js"></script>
  </body>
</html>