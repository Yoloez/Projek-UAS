<?php
session_start();
include '../koneksi.php';

// Validasi User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// --- LOGIKA AJAX BARU UNTUK UPDATE KUANTITAS ---
if (isset($_POST['update_quantity']) && isset($_POST['food_id']) && isset($_POST['action'])) {
    $food_id = (int)$_POST['food_id'];
    $action = $_POST['action'];

    if ($action === 'add') {
        $sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE id = ? AND food_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
    } 
    elseif ($action === 'remove') {
        // Cek dulu kuantitas saat ini
        $check_sql = "SELECT quantity FROM cart_items WHERE id = ? AND food_id = ?";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $food_id);
        mysqli_stmt_execute($stmt_check);
        $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check));

        if ($item && $item['quantity'] > 1) {
            // Jika kuantitas > 1, kurangi 1
            $sql = "UPDATE cart_items SET quantity = quantity - 1 WHERE id = ? AND food_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
        } else {
            // Jika kuantitas = 1, hapus item dari keranjang
            $sql = "DELETE FROM cart_items WHERE id = ? AND food_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
        }
    }

    if (isset($stmt)) {
        mysqli_stmt_execute($stmt);
    }
    
    // Kirim respons status sukses, halaman akan di-reload oleh JS
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}


// Logika Hapus Item (dari link 'Hapus')
if (isset($_GET['hapus_item_id'])) {
    $cart_item_id = (int)$_GET['hapus_item_id'];
    $delete_sql = "DELETE FROM cart_items WHERE cart_item_id = ? AND id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $user_id);
    mysqli_stmt_execute($stmt);
    header("Location: keranjang.php?pesan=dihapus");
    exit;
}

// Ambil data keranjang dari database untuk ditampilkan
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Cormorant:wght@700&display=swap" rel="stylesheet">
    <style>
        /* --- Reset & General Styling --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2c2018;
            color: #f0e6d2;
            padding: 20px 10px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: #403026;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 { text-align: center; font-size: 2rem; margin-bottom: 20px; color: #ff8c00; font-family: 'Cormorant', serif; }
        a { color: #ff8c00; text-decoration: none; transition: color 0.3s ease; }
        a:hover { color: #ffa500; }
        .action-link { display: inline-block; background-color: #ff8c00; color: #2c2018; padding: 12px 25px; border-radius: 8px; font-weight: 600; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; width: 100%; text-align: center; }
        .action-link:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(255, 140, 0, 0.3); color: #2c2018; }
        .link-secondary { background: none; border: 2px solid #ff8c00; color: #ff8c00; }
        .link-secondary:hover { background: #ff8c00; color: #2c2018; }

        /* --- Tabel Styling (Mobile-First) --- */
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table thead { display: none; }
        .cart-table tbody tr { display: block; border: 2px solid #5a4536; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
        .cart-table td { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #5a4536; font-size: 1rem; }
        .cart-table td:last-child { border-bottom: none; }
        .cart-table td::before { content: attr(data-label); font-weight: 600; color: #f0e6d2; }
        .cart-table .product-info { display: flex; align-items: center; gap: 15px; padding: 10px 0; }
        .cart-table .product-info img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
        .cart-table .product-name { font-size: 1.2rem; font-weight: 600; }
        .cart-table .delete-link { display: inline-block; background-color: #ff6347; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: 500; }
        
        /* --- STYLING BARU: QUANTITY SELECTOR --- */
        .quantity-selector {
            display: flex;
            justify-content: flex-end; /* Posisikan ke kanan di mobile */
            align-items: center;
            gap: 15px;
            background-color: #2c2018;
            border-radius: 8px;
            padding: 5px;
        }
        .quantity-selector .quantity-btn {
            background: none;
            border: none;
            color: #ff8c00;
            font-size: 1.5rem;
            font-weight: 600;
            cursor: pointer;
            width: 40px;
            height: 40px;
            transition: background-color 0.2s ease;
        }
        .quantity-selector .quantity-btn:hover { background-color: rgba(255, 140, 0, 0.2); border-radius: 5px;}
        .quantity-selector .quantity-value { font-size: 1.2rem; font-weight: 700; }

        .product-info{
            margin-left: 2.3rem;
            display: flex !important;
            align-items: center !important;
        }

        /* --- Styling Lainnya --- */
        .total-row td { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.3rem; color: #ff8c00; padding: 20px 0; border-top: 3px solid #ff8c00; }
        .bottom-nav { display: flex; flex-direction: column; gap: 15px; }
        .empty-cart-message { text-align: center; padding: 20px 0; }
        .empty-cart-message p { font-size: 1.2rem; margin-bottom: 20px; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 2000; backdrop-filter: blur(5px); padding: 15px; }
        .modal-content { background: #4a382d; padding: 30px 25px; border-radius: 15px; text-align: center; box-shadow: 0 5px 25px rgba(0,0,0,0.4); transform: scale(0.9); opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease; max-width: 400px; width: 100%; }
        .modal-overlay.active .modal-content { transform: scale(1); opacity: 1; }
        .modal-content h2 { color: #ff8c00; margin-bottom: 15px; font-size: 1.6rem; }
        .modal-content p { color: #f0e6d2; margin-bottom: 30px; font-size: 1rem; }
        .modal-actions { display: flex; flex-direction: column; gap: 15px; }
        .modal-actions .btn { border: none; font-family: 'Poppins', sans-serif; }
        
        /* --- MEDIA QUERY UNTUK DESKTOP --- */
        @media (min-width: 768px) {
            body { padding: 40px 20px; }
            h1 { font-size: 2.5rem; }
            .container { padding: 30px; }
            .cart-table thead { display: table-header-group; }
            .cart-table tbody tr { display: table-row; border: none; padding: 0; margin-bottom: 0; }
            .cart-table td { display: table-cell; text-align: center; border-bottom: 1px solid #5a4536; }
            .cart-table td[data-label="Produk"] { text-align: left; }
            .cart-table td::before { display: none; }
            .cart-table .product-info { display: contents; }
            .quantity-selector { justify-content: center; background: none; } /* Posisikan di tengah di desktop */
            .total-row td { display: table-cell; }
            .bottom-nav { flex-direction: row; }
            .action-link { width: auto; }
            .modal-actions { flex-direction: row; }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your shopping cart</h1>
    
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'dihapus'): ?>
        <p style="text-align:center; color: #ff8c00; margin-bottom: 20px;">Item successfully remove from cart.</p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($cart_result) === 0): ?>
        <div class="empty-cart-message">
            <p>Your cart is empty.</p>
            <a href="user.php" class="action-link">Back to Catalog</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body">
            <?php
            $total = 0;
            while($item = mysqli_fetch_assoc($cart_result)):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td data-label="Produk">
                        <div class="product-info">
                            <img src="../uploads/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <span class="product-name"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td data-label="Harga">Rp <?= number_format($item['price']) ?></td>
                    <td data-label="Jumlah">
                        <div class="quantity-selector">
                            <button class="quantity-btn" data-food-id="<?= $item['food_id'] ?>" data-action="remove">-</button>
                            <span class="quantity-value"><?= $item['quantity'] ?></span>
                            <button class="quantity-btn" data-food-id="<?= $item['food_id'] ?>" data-action="add">+</button>
                        </div>
                    </td>
                    <td data-label="Subtotal">Rp <?= number_format($subtotal) ?></td>
                    <td data-label="Aksi">
                        <a href="keranjang.php?hapus_item_id=<?= $item['cart_item_id'] ?>" class="delete-link" onclick="return confirm('Anda yakin ingin menghapus item ini?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" data-label="Total Belanja"><strong>Total Order</strong></td>
                    <td colspan="2"><strong>Rp <?= number_format($total) ?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="bottom-nav">
            <a href="user.php" class="action-link link-secondary">← Back to Catalog</a>
            <a id="checkout-btn" href="#" class="action-link">Continue to checkout</a>
        </div>
    <?php endif; ?>
</div>

<div id="confirm-modal" class="modal-overlay">
    <div class="modal-content">
        <h2>Confirmation Order</h2>
        <p>Are you sure you want to continue with the payment process? </p>
        <div class="modal-actions">
            <button id="cancel-btn" class="action-link link-secondary btn">Cancel</button>
            <a id="confirm-checkout-btn" href="checkout_process.php" class="action-link btn">Yes, Continue</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logika Popup Checkout
    const checkoutBtn = document.getElementById('checkout-btn');
    const modal = document.getElementById('confirm-modal');
    if (checkoutBtn && modal) {
        const cancelBtn = document.getElementById('cancel-btn');
        function showModal() {
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }
        function hideModal() {
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }
        checkoutBtn.addEventListener('click', e => { e.preventDefault(); showModal(); });
        cancelBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', e => { if (e.target === modal) hideModal(); });
    }

    // --- LOGIKA BARU UNTUK UPDATE KUANTITAS ---
    const cartBody = document.getElementById('cart-body');
    if (cartBody) {
        cartBody.addEventListener('click', function(event) {
            if (event.target.matches('.quantity-btn')) {
                const button = event.target;
                const foodId = button.dataset.foodId;
                const action = button.dataset.action;
                updateCartQuantity(foodId, action);
            }
        });
    }

    function updateCartQuantity(foodId, action) {
        const formData = new FormData();
        formData.append('update_quantity', '1');
        formData.append('food_id', foodId);
        formData.append('action', action);

        fetch('keranjang.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Reload halaman untuk menampilkan data ter-update
                window.location.reload();
            } else {
                alert('Gagal memperbarui kuantitas.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi masalah koneksi.');
        });
    }
});
</script>

</body>
</html>