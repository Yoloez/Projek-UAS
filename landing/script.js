var map = L.map("map", {
  center: [-7.758598115785603, 110.37126362819654],
  zoom: 17,
  scrollWheelZoom: false,
  zoomControl: true, // Nonaktifkan zoom via scroll
});

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

var marker = L.marker([-7.758598115785603, 110.37126362819654]).addTo(map);
marker.bindPopup("<b>Orbyt</b><br>The most cozy place").openPopup();

let ctrlDown = false;

document.addEventListener("keydown", function (e) {
  if (e.key === "Control") {
    ctrlDown = true;
    map.scrollWheelZoom.enable();
  }
});

document.addEventListener("keyup", function (e) {
  if (e.key === "Control") {
    ctrlDown = false;
    map.scrollWheelZoom.disable();
  }
});

// Jika user scroll tapi tidak tekan Ctrl, tampilkan pesan
map.getContainer().addEventListener("wheel", function (e) {
  if (!ctrlDown) {
    // Bisa tambahkan notifikasi
    console.log("Tekan CTRL + scroll untuk zoom");
  }
});
