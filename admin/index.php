<?php
session_start();
include '../koneksi.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock_batch'])) {
    if (isset($_POST['food_ids']) && isset($_POST['quantities'])) {
        $food_ids = $_POST['food_ids'];
        $quantities = $_POST['quantities'];
        $updated_count = 0;
        $error_messages_stock = []; 

        if (count($food_ids) === count($quantities)) {
            for ($i = 0; $i < count($food_ids); $i++) {
                $food_id = intval($food_ids[$i]);

                $original_quantity_input = $_POST['original_quantities'][$i]; // Ambil stok asli dari hidden input
                $new_quantity = trim($quantities[$i]);

                // Hanya proses jika kuantitas baru diisi dan berbeda dari yang lama
                if ($new_quantity !== '' && $new_quantity !== $original_quantity_input) {
                    $quantity_val = intval($new_quantity);

                    if ($quantity_val < 0) {
                        // Ambil nama makanan untuk pesan error yang lebih baik
                        $food_name_query = $conn->prepare("SELECT food_name FROM food WHERE food_id = ?");
                        $food_name_query->bind_param("i", $food_id);
                        $food_name_query->execute();
                        $food_name_result = $food_name_query->get_result();
                        $food_name_row = $food_name_result->fetch_assoc();
                        $food_name = $food_name_row ? $food_name_row['food_name'] : "Item ID $food_id";
                        $food_name_query->close();

                        $error_messages_stock[] = "Stok untuk '" . htmlspecialchars($food_name) . "' tidak boleh negatif (nilai: $quantity_val). Tidak diupdate.";
                        continue;
                    }

                    $stmt_update_stock = $conn->prepare("UPDATE food SET stock = ? WHERE food_id = ?");
                    $stmt_update_stock->bind_param("ii", $quantity_val, $food_id);
                    if ($stmt_update_stock->execute()) {
                        if ($stmt_update_stock->affected_rows > 0) {
                            $updated_count++;
                        }
                        // Jika affected_rows == 0 tapi tidak ada error, berarti nilai sama, tidak perlu dihitung sbg update
                    } else {
                        $error_messages_stock[] = "Gagal update stok untuk item ID $food_id: " . htmlspecialchars($stmt_update_stock->error);
                    }
                    $stmt_update_stock->close();
                }
            }

            if ($updated_count > 0) {
                $_SESSION['success_message_stock'] = "$updated_count item stok berhasil diupdate.";
            }
            if (empty($error_messages_stock) && $updated_count == 0 && isset($_POST['update_stock_batch'])) {
                 $_SESSION['warning_message_stock'] = "Tidak ada perubahan stok yang dilakukan.";
            }
            if (!empty($error_messages_stock)) {
                $_SESSION['error_message_stock'] = implode("<br>", $error_messages_stock);
            }
        } else {
            $_SESSION['error_message_stock'] = "Terjadi kesalahan data saat update stok (jumlah ID dan kuantitas tidak cocok).";
        }
    } else {
        $_SESSION['error_message_stock'] = "Data untuk update stok tidak lengkap.";
    }
    // Redirect untuk mencegah resubmission form dan menampilkan pesan
    header("Location: index.php");
    exit;
}
// --- AKHIR LOGIKA UPDATE STOK ---


// Statistik (sudah ada sebelumnya)
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalFoodItems = $conn->query("SELECT COUNT(*) as total FROM foods")->fetch_assoc()['total'];
$pendingOrders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];
$categoriesCount = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];

// Ambil data makanan untuk form update stok
$food_items_for_stock_update_sql = "SELECT food_id, name, stock FROM foods ORDER BY name ASC";
$food_items_for_stock_update_result = $conn->query($food_items_for_stock_update_sql);


$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kafe UAS</title>
    <link rel="stylesheet" href="admin-style.css">
    </head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <a href="index.php">Kafe<strong>UAS</strong></a>
        </div>
        <ul>
            <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="manage_users.php" class="<?php echo ($currentPage == 'manage_users.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="manage_categories.php" class="<?php echo ($currentPage == 'manage_categories.php') ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Kelola Kategori</a></li>
            <li><a href="manage_food.php" class="<?php echo ($currentPage == 'manage_food.php') ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Kelola Makanan</a></li>
            <li><a href="manage_orders.php" class="<?php echo ($currentPage == 'manage_orders.php') ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Kelola Pesanan</a></li>
            </ul>
    </aside>

    <main class="main-content">
        <header class="content-header">
            <h1>Dashboard Utama</h1>
        </header>

        <div class="content-body">
            <h2>Selamat Datang, Admin!</h2>
            <p>Ini adalah ringkasan aktivitas dan data terbaru di Kafe UAS.</p>

            <?php
            // Tampilkan pesan notifikasi untuk update stok
            if (isset($_SESSION['success_message_stock'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message_stock'] . '</div>';
                unset($_SESSION['success_message_stock']);
            }
            if (isset($_SESSION['error_message_stock'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message_stock'] . '</div>';
                unset($_SESSION['error_message_stock']);
            }
            if (isset($_SESSION['warning_message_stock'])) {
                echo '<div class="alert alert-warning">' . $_SESSION['warning_message_stock'] . '</div>';
                unset($_SESSION['warning_message_stock']);
            }
            ?>

            <div class="stats-container">
                <div class="stat-box">
                    <h3>Total Pengguna</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Kategori</h3>
                    <p><?php echo $categoriesCount; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Item Menu</h3>
                    <p><?php echo $totalFoodItems; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Pesanan Pending</h3>
                    <p><?php echo $pendingOrders; ?></p>
                </div>
            </div>

            <section class="stock-update-form" style="margin-top: 30px;">
                <h3>Update Stok Cepat</h3>
                <?php if ($food_items_for_stock_update_result && $food_items_for_stock_update_result->num_rows > 0): ?>
                    <form method="POST" action="index.php">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Makanan/Minuman</th>
                                    <th style="width: 120px; text-align:center;">Stok Saat Ini</th>
                                    <th style="width: 150px; text-align:center;">Update Stok Ke</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = $food_items_for_stock_update_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                    <td style="text-align:center;"><?php echo $item['stock']; ?></td>
                                    <td style="text-align:center;">
                                        <input type="hidden" name="food_ids[]" value="<?php echo $item['food_id']; ?>">
                                        <input type="hidden" name="original_quantities[]" value="<?php echo $item['stock']; ?>">
                                        <input type="number" name="quantities[]" placeholder="<?php echo $item['stock']; ?>" min="0" class="form-control-sm">
                                        </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="update_stock_batch" class="btn btn-success">Update Stok yang Diubah</button>
                    </form>
                <?php else: ?>
                    <p>Tidak ada item makanan/minuman untuk ditampilkan.</p>
                <?php endif; ?>
            </section>
            <h3 style="margin-top:30px;">Akses Cepat Lainnya:</h3>
            <p>
                <a href="manage_orders.php?status=pending" class="btn">Lihat Pesanan Pending</a>
                <a href="manage_food.php" class="btn">Kelola Detail Makanan</a>
            </p>
        </div>

        <footer class="content-footer">
            <p>&copy; <?php echo date("Y"); ?> Kafe UAS - Sistem Admin</p>
        </footer>
    </main>

</body>
</html>
<?php
$conn->close();
?>