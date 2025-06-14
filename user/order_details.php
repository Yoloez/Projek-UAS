<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    echo "Order ID tidak ditemukan.";
    exit();
}
$order_id = (int)$_GET['order_id'];

// Ambil data utama pesanan dan pastikan pesanan ini milik user yang login
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND id = ?";
$stmt_order = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($stmt_order, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt_order);
$order_result = mysqli_stmt_get_result($stmt_order);

if (mysqli_num_rows($order_result) === 0) {
    echo "Pesanan tidak ditemukan atau Anda tidak memiliki akses.";
    exit();
}
$order_data = mysqli_fetch_assoc($order_result);

// Ambil item-item dari pesanan
$items_sql = "SELECT oi.quantity, oi.price_per_item, oi.subtotal, f.name, f.image_url
              FROM order_items oi
              JOIN foods f ON oi.food_id = f.food_id
              WHERE oi.order_id = ?";
$stmt_items = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($stmt_items, "i", $order_id);
mysqli_stmt_execute($stmt_items);
$items_result = mysqli_stmt_get_result($stmt_items);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan #<?= $order_id ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #2c2018; color: #f0e6d2; padding: 40px 20px; }
        .container { max-width: 1000px; margin: auto; background: #403026; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        h1, h2 { text-align: center; color: #ff8c00; }
        h1 { font-size: 2.5rem; margin-bottom: 10px; }
        h2 { font-size: 1.5rem; margin-bottom: 30px; font-weight: 400; }
        .order-info { background-color: #35251c; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-around; }
        .info-item { text-align: center; }
        .info-item span { display: block; font-size: 0.9rem; color: #bdae9c; }
        .info-item strong { font-size: 1.2rem; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { padding: 15px; text-align: center; vertical-align: middle; }
        .items-table thead { border-bottom: 3px solid #ff8c00; }
        .items-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
        .back-link { display: block; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Order Successful</h1>
    <h2>Order Details#<?= htmlspecialchars($order_data['order_id']) ?></h2>

    <div class="order-info">
        <div class="info-item">
            <span>Order Date</span>
            <strong><?= date("d M Y, H:i", strtotime($order_data['created_at'])) ?></strong>
        </div>
        <div class="info-item">
            <span>Status</span>
            <strong style="text-transform: capitalize;"><?= htmlspecialchars($order_data['status']) ?></strong>
        </div>
        <div class="info-item">
            <span>Total Payment</span>
            <strong>Rp <?= number_format($order_data['total_price']) ?></strong>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><img src="../uploads/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>Rp <?= number_format($item['price_per_item']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rp <?= number_format($item['subtotal']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="user.php" class="back-link" align="center" style="color: white; text-decoration:none; border: 2px solid white; padding:1rem; width: 200px; border-radius: 50px; left:50%; position:relative; transform: translateX(-50%);">Back to Catalog</a>
</div>
</body>
</html>