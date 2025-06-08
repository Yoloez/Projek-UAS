<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu.');</script>";
    echo "<script>window.location.href = '../login/login.php';</script>";
    exit;
}

$cart = $_SESSION['cart'];
$user_name = $_SESSION['user'];

// Ambil user_id berdasarkan nama
$userResult = mysqli_query($conn, "SELECT id FROM users WHERE name = '$user_name'");
$userRow = mysqli_fetch_assoc($userResult);
$user_id = $userRow['id'];

// Hitung total pesanan
$total = 0;
foreach ($cart as $food_id => $qty) {
    $foodResult = mysqli_query($conn, "SELECT price FROM foods WHERE food_id = $food_id");
    $food = mysqli_fetch_assoc($foodResult);
    $total += $food['price'] * $qty;
}

// Buat order baru
mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, created_at) VALUES ($user_id, $total, NOW())");
$order_id = mysqli_insert_id($conn); // ambil id order terakhir

// Simpan item ke dalam order_items
foreach ($cart as $food_id => $qty) {
    $foodResult = mysqli_query($conn, "SELECT price FROM foods WHERE food_id = $food_id");
    $food = mysqli_fetch_assoc($foodResult);
    $price = $food['price'];

    mysqli_query($conn, "INSERT INTO order_items (order_id, food_id, quantity, price) 
                         VALUES ($order_id, $food_id, $qty, $price)");
}

// Bersihkan keranjang
unset($_SESSION['cart']);

echo "<script>alert('Checkout berhasil! Pesanan Anda sedang diproses.');</script>";
echo "<script>window.location.href = 'user.php';</script>";
exit;
?>
