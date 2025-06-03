<?php
session_start();
include '../koneksi.php'; 

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_item_count = array_sum($_SESSION['cart']);
}


if (isset($_POST['add_to_cart'])) {
    $food_id = $_POST['food_id'];


    if (!isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id] = 1;
    } else {
        $_SESSION['cart'][$food_id] += 1; 
    }

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cart_item_count = array_sum($_SESSION['cart']);
    } else {

        $cart_item_count = 0;
    }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Heebo:wght@100..900&family=Petrona:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Heebo:wght@100..900&family=Petrona:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div class="hero">
        <h2>Welcome to Our Cafe</h2>
        <h1>Orbyt</h1>
    </div>

<nav>
    <a href="keranjang.php" class="cart-icon-container"> 
        <i class="fa-solid fa-cart-shopping"></i>
        <?php if ($cart_item_count > 0): ?>
            <span class="cart-badge"><?= $cart_item_count ?></span>
        <?php endif; ?>
    </a>
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
        $sql = "SELECT f.*, c.nama_kategori 
                FROM foods f
                JOIN category c ON f.kategori_id = c.kategori_id";

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
                    <button type="submit" name="add_to_cart">+ Keranjang</button>
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

   <script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
    </script>
</body>
</html>