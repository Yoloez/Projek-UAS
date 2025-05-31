<?php
include 'db.php';
session_start();
// Autentikasi admin (jika diperlukan)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//    header("Location: login.php");
//    exit;
// }

// Proses hapus data jika ada parameter 'delete' (sudah termasuk hapus gambar)
if (isset($_GET['delete'])) {
    $food_id_to_delete = intval($_GET['delete']);
    // Ambil nama file gambar untuk dihapus dari server
    $stmt_get_image = $conn->prepare("SELECT image_url FROM food WHERE food_id = ?");
    $stmt_get_image->bind_param("i", $food_id_to_delete);
    $stmt_get_image->execute();
    $result_image = $stmt_get_image->get_result();
    $image_to_delete = null;
    if($row_image = $result_image->fetch_assoc()){
        $image_to_delete = $row_image['image_url'];
    }
    $stmt_get_image->close();

    // Hapus data dari database
    $sql_delete = "DELETE FROM food WHERE food_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $food_id_to_delete);

    if ($stmt_delete->execute()) {
        // Jika data berhasil dihapus dari DB, hapus file gambar dari server
        if (!empty($image_to_delete) && file_exists("uploads/" . $image_to_delete)) {
            @unlink("uploads/" . $image_to_delete); // @ untuk menekan error jika file tidak ada
        }
        echo "<script>alert('Data makanan berhasil dihapus.'); window.location.href='manage_food.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . htmlspecialchars($stmt_delete->error) . "');</script>";
    }
    $stmt_delete->close();
    exit; // Penting untuk menghentikan eksekusi setelah redirect/alert
}

// Ambil semua data makanan beserta nama kategori
$sql = "SELECT f.*, c.category_name FROM food f JOIN categories c ON f.category_id = c.category_id ORDER BY f.food_name ASC";
$result = $conn->query($sql);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Makanan & Minuman - Admin Kafe UAS</title>
    <link rel="stylesheet" href="admin_style.css">
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
            <h1>Kelola Makanan & Minuman</h1>
        </header>

        <div class="content-body">
            <a href="add_food.php" class="add-button" style="margin-bottom: 20px;"> <i class="fas fa-plus"></i> Tambah Item Baru</a>

            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga (Rp)</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Tersedia</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['food_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td>
                                <?php if (!empty($row['image_url']) && file_exists("uploads/" . $row['image_url'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" width="60" style="border-radius:4px;">
                                <?php else: ?>
                                    <small>N/A</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['is_available'] ? '<span style="color:green;font-weight:bold;">Ya</span>' : '<span style="color:red;">Tidak</span>'; ?></td>
                            <td class="action-links">
                                <a href="edit_food.php?id=<?php echo $row['food_id']; ?>" class="edit-link"><i class="fas fa-edit"></i> Edit</a>
                                <a href="manage_food.php?delete=<?php echo $row['food_id']; ?>" class="delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini: \'<?php echo htmlspecialchars(addslashes($row['food_name'])); ?>\'? Tindakan ini tidak dapat diurungkan.');"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada data makanan atau minuman. <a href="add_food.php">Tambahkan sekarang</a>.</p>
            <?php endif; ?>
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