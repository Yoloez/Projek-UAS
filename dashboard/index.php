<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login/index.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM foods");
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
<a href="../../logout.php">Logout</a>
</div>
<div class="content">
        <h2>Daftar Menu</h2>
        <button onclick="window.location.href='../landing/index.php'" class="tambah">Tambah Menu</button>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['stock'] ?></td>
                    <td>
                        <button onclick="window.location.href='edit.php?food_id=<?= $row['food_id'] ?>'" class="edit">Edit</button>
                        <a href="hapus.php?id=<?= $row['food_id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    
</body>
</html>

