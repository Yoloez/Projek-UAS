// Menjalankan semua kode hanya setelah seluruh dokumen HTML selesai dimuat
document.addEventListener("DOMContentLoaded", function () {
  // === LOGIKA UNTUK NAVBAR ===
  let prevScrollPos = window.scrollY;
  const navbar = document.getElementById("mainNavbar");

  window.addEventListener("scroll", () => {
    const currentScrollPos = window.scrollY;

    if (currentScrollPos > prevScrollPos) {
      // Scroll ke bawah
      navbar.style.top = "-100px";
    } else {
      // Scroll ke atas
      navbar.style.top = "0";
    }

    prevScrollPos = currentScrollPos;
  });

  // === LOGIKA UNTUK CAROUSEL KUSTOM ===
  const carousel = document.getElementById("customCarousel");
  if (carousel) {
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");
    const card = carousel.querySelector(".product-card");
    const cardStyle = window.getComputedStyle(card);
    const cardMargin = parseFloat(cardStyle.marginLeft) + parseFloat(cardStyle.marginRight);
    let scrollAmount = card.offsetWidth + cardMargin;

    const updateScrollAmount = () => {
      scrollAmount = carousel.querySelector(".product-card").offsetWidth + cardMargin;
    };

    window.addEventListener("resize", updateScrollAmount);

    nextBtn.addEventListener("click", () => {
      carousel.scrollBy({ left: scrollAmount, behavior: "smooth" });
    });

    prevBtn.addEventListener("click", () => {
      carousel.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    });
  }

  // === LOGIKA UNTUK ANIMASI SAAT SCROLL (INTERSECTION OBSERVER) ===
  const elementsToAnimate = document.querySelectorAll(".animate-on-scroll");
  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target); // Opsional: berhenti mengamati setelah animasi berjalan
          }
        });
      },
      {
        threshold: 0.1, // Memicu saat 10% elemen terlihat
      }
    );
    elementsToAnimate.forEach((el) => observer.observe(el));
  }

  // === LOGIKA UNTUK LEAFLET MAP ===
  const mapElement = document.getElementById("map");
  if (mapElement) {
    const map = L.map("map", {
      center: [-7.758598115785603, 110.37126362819654],
      zoom: 17,
      scrollWheelZoom: false, // Defaultnya zoom via scroll dinonaktifkan
      zoomControl: true,
    });

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const marker = L.marker([-7.758598115785603, 110.37126362819654]).addTo(map);
    marker.bindPopup("<b>Orbyt</b><br>The most cozy place").openPopup();

    // Logika untuk mengaktifkan zoom hanya saat tombol CTRL ditekan
    let ctrlDown = false;
    map.getContainer().addEventListener("wheel", function (e) {
      if (!ctrlDown) {
        e.preventDefault(); // Mencegah zoom jika CTRL tidak ditekan
        // Di sini Anda bisa menampilkan notifikasi "Tekan CTRL + scroll untuk zoom"
      }
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Control") {
        ctrlDown = true;
        map.scrollWheelZoom.enable();
      }
    });
    document.addEventListener("keyup", (e) => {
      if (e.key === "Control") {
        ctrlDown = false;
        map.scrollWheelZoom.disable();
      }
    });
  }
});
