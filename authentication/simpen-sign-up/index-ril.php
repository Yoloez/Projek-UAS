<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="daftar.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  </head>
  <body>
    <h1></h1>
    <h1></h1>
    <h1></h1>
    <div class="wrap">
      <h1>Daftar</h1>
      <!-- Tambahkan action ke halaman landingpage -->
      <form id="loginForm">
        <div class="kata"><input type="text" id="username" placeholder="Username" /><i class="bx bxs-user"></i></div>
        <div class="kata">
          <input type="password" id="password" placeholder="Password" />
          <button type="button" class="mata" onclick="togglePassword()">
            <i id="eyeIcon" class="bx bx-show"></i>
          </button>
        </div>

        <div class="login">
          <!-- Hilangkan elemen <a> di dalam tombol -->
          <button type="submit" class="tombol">DAFTAR</button>
        </div>

        <div class="sign-in">
          <a> sudah punya akun?</a>
          <a class="login-di-sini" href="../masuk/login.html">login di sini</a>
        </div>
      </form>
    </div>

    <script>
      function togglePassword() {
        const passwordField = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        // Ganti ikon mata sesuai status password
        if (passwordField.type === "password") {
          passwordField.type = "text";
          eyeIcon.classList.replace("bx-show", "bx-hide");
        } else {
          passwordField.type = "password";
          eyeIcon.classList.replace("bx-hide", "bx-show");
        }

        // Tambahkan kelas animasi dan hapus setelah animasi selesai
        eyeIcon.classList.add("animate");
        setTimeout(() => {
          eyeIcon.classList.remove("animate");
        }, 300); // Durasi animasi (300ms)
      }
      document.getElementById("loginForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Mencegah form terkirim

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        // Cek apakah username dan password diisi
        if (!username && !password) {
          alert("Masukkan username dan password");
        } else if (!username) {
          alert("Masukkan username");
        } else if (!password) {
          alert("Masukkan password");
        } else {
          // Jika keduanya terisi, arahkan ke halaman home.html
          window.location.href = "/landingpage/home.html";
        }
      });
    </script>
  </body>
</html>
