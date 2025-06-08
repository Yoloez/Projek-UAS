<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 1. Ambil semua item dari keranjang user
$cart_sql = "SELECT c.quantity, f.food_id, f.price 
             FROM cart_items c
             JOIN foods f ON c.food_id = f.food_id
             WHERE c.id = ?";
$stmt_cart = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($stmt_cart, "i", $user_id);
mysqli_stmt_execute($stmt_cart);
$cart_result = mysqli_stmt_get_result($stmt_cart);

if (mysqli_num_rows($cart_result) === 0) {
    // Jika keranjang kosong, kembalikan ke halaman keranjang
    header("Location: keranjang.php?pesan=keranjang_kosong");
    exit();
}

// Simpan item ke array dan hitung total harga
$cart_items = [];
$total_price = 0;
while ($item = mysqli_fetch_assoc($cart_result)) {
    $cart_items[] = $item;
    $total_price += $item['price'] * $item['quantity'];
}

// Mulai transaksi database untuk memastikan semua query berhasil
mysqli_begin_transaction($conn);

try {
    // 2. Insert ke tabel 'orders'
    $order_sql = "INSERT INTO orders (id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())";
    $stmt_order = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($stmt_order, "id", $user_id, $total_price);
    mysqli_stmt_execute($stmt_order);

    // Dapatkan ID dari order yang baru saja dibuat
    $new_order_id = mysqli_insert_id($conn);
    if ($new_order_id === 0) {
        throw new Exception("Gagal membuat pesanan.");
    }
    
    // 3. Pindahkan setiap item dari keranjang ke 'order_items'
    $order_item_sql = "INSERT INTO order_items (order_id, food_id, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = mysqli_prepare($conn, $order_item_sql);
    
    foreach ($cart_items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        mysqli_stmt_bind_param($stmt_item, "iiidd", $new_order_id, $item['food_id'], $item['quantity'], $item['price'], $subtotal);
        mysqli_stmt_execute($stmt_item);
    }
    
    // 4. Kosongkan keranjang user
    $delete_cart_sql = "DELETE FROM cart_items WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $delete_cart_sql);
    mysqli_stmt_bind_param($stmt_delete, "i", $user_id);
    mysqli_stmt_execute($stmt_delete);
    
    // Jika semua berhasil, commit transaksi
    mysqli_commit($conn);
    
    // Arahkan ke halaman detail pesanan yang baru dibuat
    header("Location: order_details.php?order_id=" . $new_order_id);
    exit();

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($conn);
    // Anda bisa mengarahkan ke halaman error atau kembali ke keranjang
    header("Location: keranjang.php?pesan=checkout_gagal");
    exit();
}