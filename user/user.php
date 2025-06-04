<?php
session_start();
include '../koneksi.php';

// Inisialisasi keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hitung jumlah item keranjang untuk pemuatan halaman awal
$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_item_count = array_sum($_SESSION['cart']);
}

// Jika user menambahkan ke keranjang (akan ditangani oleh AJAX)
if (isset($_POST['add_to_cart']) && isset($_POST['food_id'])) {
    $food_id = $_POST['food_id'];

    if (!isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id] = 1;
    } else {
        $_SESSION['cart'][$food_id] += 1;
    }

    // Hitung ulang jumlah item keranjang saat ini
    $current_cart_item_count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $current_cart_item_count = array_sum($_SESSION['cart']);
    }

    // Kirim respons JSON untuk permintaan AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Produk berhasil ditambahkan ke keranjang!',
        'cart_item_count' => $current_cart_item_count
    ]);
    exit; // Hentikan eksekusi skrip lebih lanjut untuk permintaan AJAX
}
// $cart_item_count yang dihitung di atas akan digunakan untuk render halaman awal jika bukan permintaan add_to_cart
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Heebo:wght@100..900&family=Petrona:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <title>Orbyt Cafe Menu</title>
</head>
<body>
    <div class="hero">
        <h2>Welcome to Our Cafe</h2>
        <h1>Orbyt</h1>
    </div>

    <div class="keranjang">

        <a href="keranjang.php" class="cart-icon-container">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php if ($cart_item_count > 0): ?>
                <span class="cart-badge"><?= $cart_item_count ?></span>
                <?php endif; ?>
            </a>
        </div>
    
<nav>
    <div class="log-out">
        <p align="right">
            Logout
        </p>
    </div>
</nav>

    <section class="katalog">
        <h1>Our Signature Menu</h1>
    </section>

    <div class="filter-container">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="makanan">Foods</button>
        <button class="filter-btn" data-filter="minuman">Drinks</button>
    </div>
    <div class="produk-container">
        <?php
        // Query Anda sudah benar menggunakan 'category' sebagai nama tabel dan 'kategori_id'
        $sql = "SELECT f.*, c.nama_kategori 
                FROM foods f
                JOIN category c ON f.kategori_id = c.kategori_id"; // tabel 'category', kolom 'kategori_id'

        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)):
        ?>
            <div class="produk" data-category="<?= strtolower(htmlspecialchars($row['nama_kategori'])) ?>">
                <img src="../uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <p><strong>Rp <?= number_format($row['price']) ?></strong></p>
                <form method="POST" action=""> 
                    <input type="hidden" name="food_id" value="<?= htmlspecialchars($row['food_id']) ?>">
                    <input type="hidden" name="add_to_cart" value="1">
                    <button type="submit">+ Keranjang</button>
                </form>
            </div>
        <?php
            endwhile;
        } else {
            echo "<p style='text-align:center; color: #aaa; width:100%;'>Belum ada produk yang tersedia.</p>";
        }
        ?>
    </div>

    <br>
    <a href="keranjang.php">Lihat Keranjang</a>

    <div id="toast-notification" class="toast-notification">
        <p id="toast-message"></p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Filter Logic (dari kode Anda sebelumnya, sudah baik) ---
        const filterButtons = document.querySelectorAll('.filter-btn');
        const productCards = document.querySelectorAll('.produk');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filterValue = this.getAttribute('data-filter');
                productCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (filterValue === 'all' || cardCategory === filterValue) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // --- AJAX Add to Cart & Toast Notification Logic ---
        const addToCartForms = document.querySelectorAll('.produk form');
        const toastNotification = document.getElementById('toast-notification');
        const toastMessageElement = document.getElementById('toast-message');
        const cartIconContainer = document.querySelector('.cart-icon-container'); // Target container ikon keranjang

        addToCartForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah submit form standar (yang menyebabkan reload)

                const formData = new FormData(form);
                // 'add_to_cart' sudah ada sebagai input hidden di form

                fetch(form.action || window.location.pathname, { // Post ke URL saat ini
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        // Mencoba parse error dari server jika ada, atau fallback ke error umum
                        return response.json().catch(() => {
                            throw new Error('Respon jaringan bermasalah. Status: ' + response.status);
                        });
                    }
                    return response.json(); // Mengambil data JSON dari respons
                })
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message);
                        updateCartBadge(data.cart_item_count);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan tak terduga.', true); // true untuk error styling
                    }
                })
                .catch(error => {
                    console.error('Error saat menambahkan ke keranjang:', error);
                    showToast('Gagal menambahkan ke keranjang. Error: ' + error.message, true);
                });
            });
        });

        function showToast(message, isError = false) {
            toastMessageElement.textContent = message;
            toastNotification.className = 'toast-notification'; // Reset kelas
            if (isError) {
                toastNotification.classList.add('error'); // Tambah kelas error jika ada
            }
            toastNotification.classList.add('show'); // Tampilkan toast

            // Sembunyikan toast setelah beberapa detik
            setTimeout(() => {
                toastNotification.classList.remove('show');
            }, 3000); // Durasi toast 3 detik
        }

        function updateCartBadge(count) {
            let badge = cartIconContainer.querySelector('.cart-badge');
            if (count > 0) {
                if (!badge) { // Jika badge belum ada, buat baru
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    cartIconContainer.appendChild(badge);
                }
                badge.textContent = count;
            } else { // Jika count 0, hapus badge
                if (badge) {
                    badge.remove();
                }
            }
        }
    });
    </script>
</body>
</html>