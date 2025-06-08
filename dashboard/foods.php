<?php
include '../koneksi.php';
session_start();

// Validasi session admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../authentication/login/login.php");
    exit();
}

// --- LOGIKA PAGINATION ---
$limit = 3; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Ambil data makanan dengan LIMIT dan JOIN ke kategori
$result = mysqli_query($conn, "
    SELECT f.*, c.nama_kategori 
    FROM foods f
    JOIN category c ON f.kategori_id = c.kategori_id 
    ORDER BY f.food_id DESC 
    LIMIT $limit OFFSET $offset
");

// Hitung total data untuk membuat nomor halaman
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM foods");
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Admin Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --sidebar-bg: #2c211d;
            --bg-color: #f4f7fe;
            --primary-green: #00b09b;
            --secondary-green: #96c93d;
            --text-dark: #1b254b;
            --text-light: #a0aec0;
            --border-color: #e2e8f0;
            --info-color: #3b82f6;
            --danger-color: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            color: #fff;
        }
        .sidebar h2 { font-size: 1.8rem; margin-bottom: 40px; text-align: center; }
        .sidebar h2 i { color: var(--primary-green); }
        .sidebar a {
            color: var(--text-light);
            text-decoration: none;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 176, 155, 0.3);
        }
        .sidebar a.logout { margin-top: auto; }
        .content-wrapper { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .content-header h2 { font-size: 2.2rem; color: var(--text-dark); }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary { background: linear-gradient(90deg, var(--primary-green), var(--secondary-green)); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 25px rgba(0,0,0,0.05);
        }
        .data-table th, .data-table td { padding: 16px 20px; text-align: left; vertical-align: middle; }
        .data-table thead { background-color: #f8fafc; }
        .data-table th { font-weight: 600; color: var(--text-dark); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;}
        .data-table tbody tr { border-bottom: 1px solid var(--border-color); }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
        .action-buttons { display: flex; gap: 10px; }
        .btn-edit { background-color: var(--info-color); color: white; padding: 8px 15px; font-size: 0.9rem; }
        .btn-hapus { background-color: var(--danger-color); color: white; padding: 8px 15px; font-size: 0.9rem; }
        .pagination { display: flex; justify-content: center; align-items: center; margin-top: 30px; gap: 10px; }
        .pagination a, .pagination p { color: var(--text-dark); text-decoration: none; padding: 8px 15px; border: 2px solid var(--border-color); border-radius: 8px; transition: all 0.3s ease; }
        .pagination a:hover { background-color: var(--primary-green); border-color: var(--primary-green); color: white; }
        .pagination p { background: linear-gradient(90deg, var(--primary-green), var(--secondary-green)); border-color: var(--primary-green); color: white; font-weight: 700; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-rocket"></i> Admin</h2>
    <a href="dashboard.php"><i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard</a>
    <a href="foods.php" class="active"><i class="fa-solid fa-utensils fa-fw"></i> Kelola Menu</a>
    <a href="orders.php"><i class="fa-solid fa-box fa-fw"></i> Kelola Pesanan</a>
    <a href="../logout.php" class="logout"><i class="fa-solid fa-sign-out-alt fa-fw"></i> Logout</a>
</div>

<div class="content-wrapper">
    <div class="content-header">
        <h2>Kelola Menu Makanan</h2>
        <button onclick="window.location.href='tambah.php'" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Menu</button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama Menu</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="../uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"></td>
                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                    <td>Rp <?= number_format($row['price']) ?></td>
                    <td><?= $row['stock'] ?></td>
                    <td class="action-buttons">
                        <button onclick="window.location.href='edit.php?food_id=<?= $row['food_id'] ?>'" class="btn btn-edit"><i class="fa-solid fa-pencil fa-fw"></i></button>
                        <button onclick="if(confirm('Anda yakin ingin menghapus menu ini?')) window.location.href='hapus.php?food_id=<?= $row['food_id'] ?>';" class="btn btn-hapus"><i class="fa-solid fa-trash fa-fw"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px;">Belum ada menu yang ditambahkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
                <p><?= $i ?></p>
            <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

</body>
</html>