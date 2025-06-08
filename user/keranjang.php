<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Hapus item dari keranjang
if (isset($_GET['hapus_item_id'])) {
    $cart_item_id = (int)$_GET['hapus_item_id'];
    $delete_sql = "DELETE FROM cart_items WHERE cart_item_id = ? AND id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $user_id);
    mysqli_stmt_execute($stmt);
    header("Location: keranjang.php?pesan=dihapus");
    exit;
}

// Ambil data keranjang dari database
$cart_sql = "SELECT c.cart_item_id, c.quantity, f.food_id, f.name, f.price, f.image_url 
             FROM cart_items c
             JOIN foods f ON c.food_id = f.food_id
             WHERE c.id = ?";
$stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Orbyt Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- Reset & General Styling --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2c2018;
            color: #f0e6d2;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: #403026;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 { text-align: center; font-size: 2.5rem; margin-bottom: 30px; color: #ff8c00; text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3); }
        a { color: #ff8c00; text-decoration: none; transition: color 0.3s ease; }
        a:hover { color: #ffa500; }
        .action-link {
            display: inline-block;
            background-color: #ff8c00;
            color: #2c2018;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .action-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(255, 140, 0, 0.3);
            color: #2c2018;
        }
        .link-secondary {
            background: none;
            border: 2px solid #ff8c00;
            color: #ff8c00;
        }
        .link-secondary:hover { background: #ff8c00; color: #2c2018; }

        /* --- Tabel Styling --- */
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table th, .cart-table td { padding: 15px; text-align: center; vertical-align: middle; }
        .cart-table thead { background-color: #35251c; }
        .cart-table th { font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; color: #ff8c00; border-bottom: 3px solid #ff8c00; }
        .cart-table tbody tr { border-bottom: 1px solid #5a4536; }
        .cart-table tbody tr:last-child { border-bottom: none; }
        .cart-table td { font-size: 1.1rem; }
        .cart-table .product-name { text-align: left; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 2px solid #5a4536; }
        .cart-table .delete-link { font-size: 0.9rem; color: #ff6347; font-weight: 600; }
        .cart-table .delete-link:hover { color: #ff0000; }
        .total-row td { font-weight: 700; font-size: 1.3rem; color: #ff8c00; border-top: 3px solid #ff8c00; padding-top: 20px; }
        .empty-cart-message, .bottom-nav { text-align: center; padding: 20px; }
        .empty-cart-message p { font-size: 1.2rem; margin-bottom: 20px; }
        .bottom-nav { display: flex; justify-content: space-between; align-items: center; }

        /* --- STYLING UNTUK POPUP MODERN (MODAL) --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: none; /* Disembunyikan secara default */
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: #4a382d;
            padding: 30px 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.4);
            transform: scale(0.9);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        /* Style saat modal aktif/ditampilkan */
        .modal-overlay.active .modal-content {
            transform: scale(1);
            opacity: 1;
        }

        .modal-content h2 {
            color: #ff8c00;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .modal-content p {
            color: #f0e6d2;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .modal-actions .btn {
            border: none;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Keranjang Belanja Anda</h1>

    <?php if (mysqli_num_rows($cart_result) === 0): ?>
        <div class="empty-cart-message">
            <p>Keranjang Anda masih kosong.</p>
            <a href="user.php" class="action-link">Kembali ke Katalog</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            while($item = mysqli_fetch_assoc($cart_result)):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><img src="../uploads/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                    <td class="product-name"><?= htmlspecialchars($item['name']) ?></td>
                    <td>Rp <?= number_format($item['price']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rp <?= number_format($subtotal) ?></td>
                    <td><a href="keranjang.php?hapus_item_id=<?= $item['cart_item_id'] ?>" class="delete-link" onclick="return confirm('Anda yakin?')">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;"><strong>Total Belanja</strong></td>
                    <td colspan="2" style="text-align: center;"><strong>Rp <?= number_format($total) ?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="bottom-nav">
            <a href="user.php" class="action-link link-secondary">← Lanjut Belanja</a>
            <a id="checkout-btn" href="#" class="action-link">Lanjut ke Checkout</a>
        </div>
    <?php endif; ?>
</div>

<div id="confirm-modal" class="modal-overlay">
    <div class="modal-content">
        <h2>Konfirmasi Pesanan</h2>
        <p>Anda yakin ingin melanjutkan ke proses checkout?</p>
        <div class="modal-actions">
            <button id="cancel-btn" class="action-link link-secondary btn">Batal</button>
            <a id="confirm-checkout-btn" href="checkout_process.php" class="action-link btn">Ya, Lanjutkan</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const modal = document.getElementById('confirm-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const confirmCheckoutBtn = document.getElementById('confirm-checkout-btn');

    // Tampilkan popup saat tombol checkout diklik
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah link berpindah halaman
            modal.style.display = 'flex';
            // Tambahkan kelas 'active' untuk memicu animasi
            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
        });
    }

    // Fungsi untuk menyembunyikan popup
    function hideModal() {
        modal.classList.remove('active');
        // Tunggu animasi selesai sebelum menyembunyikan display
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    // Sembunyikan popup saat tombol 'Batal' diklik
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideModal);
    }

    // Sembunyikan popup saat area luar popup diklik
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
            }
        });
    }

    // (Opsional) pastikan link konfirmasi benar
    // Ini sudah diatur di HTML, tapi bisa juga diatur di sini jika perlu
    // if(confirmCheckoutBtn) {
    //     confirmCheckoutBtn.href = 'checkout_process.php';
    // }
});
</script>

</body>
</html>