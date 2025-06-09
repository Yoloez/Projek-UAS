<?php
session_start();
include '../koneksi.php';

// Validasi User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
if ($_SESSION['role'] !== 'user') {
    header("Location: ../authentication/login/login.php?pesan=akses_ditolak");
    exit();
}

// --- LOGIKA AJAX BARU UNTUK UPDATE KERANJANG (TAMBAH & KURANG) ---
if (isset($_POST['update_cart']) && isset($_POST['food_id']) && isset($_POST['action'])) {
    $food_id = (int)$_POST['food_id'];
    $action = $_POST['action'];

    $check_sql = "SELECT quantity FROM cart_items WHERE id = ? AND food_id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $food_id);
    mysqli_stmt_execute($stmt_check);
    $cart_item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check));
    $current_quantity = $cart_item['quantity'] ?? 0;

    if ($action === 'add') {
        if ($current_quantity > 0) {
            $sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE id = ? AND food_id = ?";
        } else {
            $sql = "INSERT INTO cart_items (quantity, id, food_id) VALUES (1, ?, ?)";
        }
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
    } 
    elseif ($action === 'remove') {
        if ($current_quantity > 1) {
            $sql = "UPDATE cart_items SET quantity = quantity - 1 WHERE id = ? AND food_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
        } else {
            $sql = "DELETE FROM cart_items WHERE id = ? AND food_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $food_id);
        }
    }

    if (isset($stmt)) {
        mysqli_stmt_execute($stmt);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// --- AMBIL SEMUA DATA KERANJANG USER SAAT HALAMAN DIBUKA ---
$cart_quantities = [];
$cart_total_price = 0;

$cart_items_sql = "
    SELECT c.food_id, c.quantity, f.price 
    FROM cart_items c
    JOIN foods f ON c.food_id = f.food_id
    WHERE c.id = ?
";
$stmt_items = mysqli_prepare($conn, $cart_items_sql);
mysqli_stmt_bind_param($stmt_items, "i", $user_id);
mysqli_stmt_execute($stmt_items);
$cart_items_result = mysqli_stmt_get_result($stmt_items);
while ($item = mysqli_fetch_assoc($cart_items_result)) {
    $cart_quantities[$item['food_id']] = $item['quantity'];
    $cart_total_price += $item['quantity'] * $item['price'];
}
$cart_item_count = array_sum($cart_quantities);

// Ambil data produk
$sql_foods = "SELECT f.*, c.nama_kategori FROM foods f JOIN category c ON f.kategori_id = c.kategori_id";
$result_foods = mysqli_query($conn, $sql_foods);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orbyt Cafe Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<nav>
    <div class="nav-logo"><a href="#">Orbyt Cafe</a></div>
    <div class="nav-menu" id="nav-menu">
        <a href="history.php" class="nav-link"><i class="fa-solid fa-history fa-fw"></i> History</a>
        <a href="keranjang.php" class="nav-link cart-icon-container">
            <i class="fa-solid fa-cart-shopping fa-fw"></i> Cart
            <span class="cart-badge" id="cart-badge" style="<?= $cart_item_count > 0 ? '' : 'display: none;' ?>"><?= $cart_item_count ?></span>
        </a>
        <a href="../logout.php" class="nav-link logout-btn"><i class="fa-solid fa-sign-out-alt fa-fw"></i> Logout</a>
    </div>
    <button class="hamburger" id="hamburger-btn"><i class="fa-solid fa-bars"></i></button>
</nav>

<div class="hero">
    <h2>Welcome, <?= htmlspecialchars(explode(' ', $username)[0]) ?></h2>
    <h1>Orbyt Cafe</h1>
</div>

<section class="katalog"><h1>Our Signature Menu</h1></section>

<div class="filter-container">
    <button class="filter-btn active" data-filter="all">All</button>
    <button class="filter-btn" data-filter="makanan">Foods</button>
    <button class="filter-btn" data-filter="minuman">Drinks</button>
</div>

<div class="produk-container">
    <?php while ($row = mysqli_fetch_assoc($result_foods)): ?>
        <div class="produk" data-category="<?= strtolower(htmlspecialchars($row['nama_kategori'])) ?>">
            <img src="../uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <p><strong>Rp <?= number_format($row['price']) ?></strong></p>
            <div class="cart-action">
                <?php $quantity_in_cart = $cart_quantities[$row['food_id']] ?? 0; ?>
                <?php if ($quantity_in_cart > 0): ?>
                    <div class="quantity-selector">
                        <button class="quantity-btn" data-food-id="<?= $row['food_id'] ?>" data-action="remove">-</button>
                        <span class="quantity-value"><?= $quantity_in_cart ?></span>
                        <button class="quantity-btn" data-food-id="<?= $row['food_id'] ?>" data-action="add">+</button>
                    </div>
                <?php else: ?>
                    <button class="add-to-cart-btn" data-food-id="<?= $row['food_id'] ?>" data-action="add">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart 
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="bottom-nav-bar" id="bottom-nav-bar" style="<?= $cart_item_count > 0 ? 'display: flex;' : 'display: none;' ?>">
    <div class="total-price-container">
        <span>Total</span>
        <strong id="cart-total-price">Rp <?= number_format($cart_total_price) ?></strong>
    </div>
    <a href="keranjang.php" class="btn-checkout">
        View Cart <i class="fa-solid fa-arrow-right"></i>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    hamburgerBtn.addEventListener('click', () => navMenu.classList.toggle('active'));

    const filterButtons = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.produk');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const filterValue = this.getAttribute('data-filter');
            productCards.forEach(card => {
                card.style.display = (filterValue === 'all' || card.getAttribute('data-category') === filterValue) ? 'flex' : 'none';
            });
        });
    });

    const productContainer = document.querySelector('.produk-container');
    productContainer.addEventListener('click', function(event) {
        const button = event.target.closest('.quantity-btn, .add-to-cart-btn');
        if (button) {
            const foodId = button.dataset.foodId;
            const action = button.dataset.action;
            updateCart(foodId, action);
        }
    });

    function updateCart(foodId, action) {
        const formData = new FormData();
        formData.append('update_cart', '1');
        formData.append('food_id', foodId);
        formData.append('action', action);

        fetch(window.location.pathname, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert('Gagal memperbarui keranjang.');
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