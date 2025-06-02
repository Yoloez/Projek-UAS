<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login/index.php");
    exit();
}

// Pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil data dengan LIMIT
$result = mysqli_query($conn, "SELECT foods.*, category.nama_kategori FROM foods INNER JOIN category ON foods.kategori_id = category.kategori_id LIMIT $limit OFFSET $offset");

// Hitung total data
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM foods");
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
</head>
<body>

<div class="sidebar">
<h2>Admin</h2>
<a href="index.php">Dashboard</a>
<a href="tambah.php">Tambah Menu</a>
<a href="../landing/index.php">Logout</a>
</div>
<div class="content">
        <h2>Daftar Menu</h2>
        <button onclick="window.location.href='tambah.php'" class="tambah">Tambah Menu</button>

                <div class="filter-buttons">
            <a href="index.php" class="button-link">Semua</a>
            <a href="?category=Makanan" class="button-link">Makanan</a>
            <a href="?category=Minuman" class="button-link">Minuman</a>
            </div>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Pict</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['nama_kategori'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td>
                        <img src="../uploads/<?= $row['image_url'] ?>" width="100">
                    </td>
                    <td><?= $row['stock'] ?></td>
                    <td>
                        <button onclick="window.location.href='edit.php?food_id=<?= $row['food_id'] ?>'" class="edit">Edit</button>
<button onclick="if(confirm('Yakin ingin menghapus?')) window.location.href='hapus.php?food_id=<?= $row['food_id'] ?>';" class="hapus">Hapus</button>

                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <!-- Pagination -->
<div class="pagination" style="margin-top: 20px;">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $page): ?>
            <p><?= $i ?></p>
        <?php else: ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
        </div>
    
</body>
</html>

