<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login dan memiliki user_id di session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Validasi role
if ($_SESSION['role'] !== 'user') {
    header("Location: ../authentication/login/login.php?pesan=akses_ditolak");
    exit();
}

// ---- LOGIKA AJAX UNTUK TAMBAH KERANJANG ----
if (isset($_POST['add_to_cart']) && isset($_POST['food_id'])) {
    $food_id = (int)$_POST['food_id'];

    // Cek apakah item sudah ada di keranjang user
    $check_cart_sql = "SELECT * FROM cart_items WHERE id = ? AND food_id = ?";
    $stmt = mysqli_prepare($conn, $check_cart_sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Jika sudah ada, UPDATE quantity + 1
        $update_sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE id = ? AND food_id = ?";
        $stmt_update = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt_update, "ii", $user_id, $food_id);
        mysqli_stmt_execute($stmt_update);
    } else {
        // Jika belum ada, INSERT item baru
        $insert_sql = "INSERT INTO cart_items (id, food_id, quantity) VALUES (?, ?, 1)";
        $stmt_insert = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt_insert, "ii", $user_id, $food_id);
        mysqli_stmt_execute($stmt_insert);
    }

    // Hitung ulang total item di keranjang untuk badge notifikasi
    $count_sql = "SELECT SUM(quantity) as total_items FROM cart_items WHERE id = ?";
    $stmt_count = mysqli_prepare($conn, $count_sql);
    mysqli_stmt_bind_param($stmt_count, "i", $user_id);
    mysqli_stmt_execute($stmt_count);
    $count_result = mysqli_stmt_get_result($stmt_count);
    $total_items = mysqli_fetch_assoc($count_result)['total_items'] ?? 0;

    // Kirim respons JSON
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Produk berhasil ditambahkan ke keranjang!',
        'cart_item_count' => $total_items
    ]);
    exit;
}

// Hitung jumlah item keranjang untuk pemuatan halaman awal
$count_sql = "SELECT SUM(quantity) as total_items FROM cart_items WHERE id = ?";
$stmt_count_page = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt_count_page, "i", $user_id);
mysqli_stmt_execute($stmt_count_page);
$count_result_page = mysqli_stmt_get_result($stmt_count_page);
$cart_item_count = mysqli_fetch_assoc($count_result_page)['total_items'] ?? 0;

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
    <div class="nav-left">
        <i class="fa-solid fa-user"></i>
        <span>Selamat datang, <strong><?php echo htmlspecialchars($username); ?></strong></span>
    </div>
    <div class="nav-right">
        <a href="history.php" class="nav-link"><i class="fa-solid fa-history fa-fw"></i> Riwayat Pesanan</a>
        <a href="../logout.php" class="nav-link logout-btn"><i class="fa-solid fa-sign-out-alt fa-fw"></i> Logout</a>
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