<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Error: Order ID tidak ditemukan.");
}
$order_id = (int)$_GET['order_id'];

// Ambil data utama pesanan
$order_result = mysqli_query($conn, "
    SELECT o.order_id, o.total_price, o.status, o.created_at, u.name as user_name
    FROM orders o
    JOIN users u ON o.id = u.id
    WHERE o.order_id = $order_id
");
if(mysqli_num_rows($order_result) == 0) die("Pesanan tidak ditemukan.");
$order_data = mysqli_fetch_assoc($order_result);

// Ambil item-item dari pesanan
$items_result = mysqli_query($conn, "
    SELECT oi.quantity, oi.price_per_item, oi.subtotal, f.name as food_name
    FROM order_items oi
    JOIN foods f ON oi.food_id = f.food_id
    WHERE oi.order_id = $order_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Detail Pesanan #<?= $order_id ?></title>
</head>
<body>

<div class="sidebar">
    <h2>Admin</h2>
    <a href="index.php">Dashboard</a>
    <a href="tambah.php">Tambah Menu</a>
    <a href="orders.php">Kelola Pesanan</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="content">
    <h2>Detail Pesanan #<?= $order_data['order_id'] ?></h2>
    <div style="margin-bottom: 20px;">
        <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($order_data['user_name']) ?></p>
        <p><strong>Tanggal Pesan:</strong> <?= date("d M Y, H:i", strtotime($order_data['created_at'])) ?></p>
        <p><strong>Status:</strong> <span style="text-transform: capitalize;"><?= htmlspecialchars($order_data['status']) ?></span></p>
        <p><strong>Total Bayar:</strong> Rp <?= number_format($order_data['total_price']) ?></p>
    </div>

    <h3>Item yang Dipesan:</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><?= htmlspecialchars($item['food_name']) ?></td>
                <td>Rp <?= number_format($item['price_per_item']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rp <?= number_format($item['subtotal']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br>
    <button onclick="window.location.href='orders.php'">Kembali ke Daftar Pesanan</button>
</div>
</body>
</html>