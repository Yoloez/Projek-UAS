<?php
include '../koneksi.php';
session_start();

// Validasi session admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../authentication/login/login.php");
    exit();
}

// Proses update status jika ada form yang di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id_to_update = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id_to_update);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: orders.php?status_updated=1");
        exit();
    } else {
        $error_message = "Gagal mengupdate status.";
    }
}

// Ambil semua data pesanan, di-join dengan nama user, urutkan dari yang terbaru
$result = mysqli_query($conn, "
    SELECT o.order_id, o.total_price, o.status, o.created_at, u.name as user_name
    FROM orders o
    JOIN users u ON o.id = u.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Dashboard</title>
    
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
            --success-color: #22c55e;
            --warning-color: #f59e0b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            color: #fff;
        }
        .sidebar h2 {
            font-size: 1.8rem;
            margin-bottom: 40px;
            text-align: center;
        }
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

        /* --- Content Area --- */
        .content-wrapper { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .content-header h2 { font-size: 2.2rem; color: var(--text-dark); margin-bottom: 30px; }

        /* --- Alert/Notification Styling --- */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-weight: 500;
            border: 1px solid transparent;
        }
        .alert-success { background-color: #dcfce7; color: #166534; border-color: #a7f3d0; }
        .alert-error { background-color: #fee2e2; color: #991b1b; border-color: #fecaca; }

        /* --- Table Styling --- */
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
        .data-table tbody tr:hover { background-color: #f8fafc; }

        /* --- Action & Status Styling --- */
        .action-cell { display: flex; align-items: center; gap: 10px; }
        .update-form { display: flex; gap: 8px; align-items: center; }
        .status-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-update { background: linear-gradient(90deg, var(--primary-green), var(--secondary-green)); color: white; }
        .btn-detail { background-color: var(--info-color); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .status {
            padding: 5px 12px;
            border-radius: 15px;
            color: #fff;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
            display: inline-block;
        }
        .status.pending { background-color: var(--warning-color); }
        .status.processed { background-color: var(--info-color); }
        .status.completed { background-color: var(--success-color); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-rocket"></i> Admin</h2>
    <a href="dashboard.php"><i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard</a>
    <a href="tambah.php"><i class="fa-solid fa-plus fa-fw"></i> Tambah Menu</a>
    <a href="foods.php"><i class="fa-solid fa-utensils fa-fw"></i> Kelola Menu</a>
    <a href="orders.php" class="active"><i class="fa-solid fa-box fa-fw"></i> Kelola Pesanan</a>
    <a href="../logout.php" class="logout"><i class="fa-solid fa-sign-out-alt fa-fw"></i> Logout</a>
</div>

<div class="content-wrapper">
    <div class="content-header">
        <h2>Daftar Pesanan Masuk</h2>
    </div>

    <?php if(isset($_GET['status_updated'])): ?>
        <div class="alert alert-success">Status pesanan berhasil diperbarui!</div>
    <?php endif; ?>
    <?php if(isset($error_message)): ?>
        <div class="alert alert-error">Error: <?= $error_message ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong>#<?= $row['order_id'] ?></strong></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></td>
                    <td>Rp <?= number_format($row['total_price']) ?></td>
                    <td><span class="status <?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td class="action-cell">
                        <form action="orders.php" method="POST" class="update-form">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="status" class="status-select">
                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processed" <?= $row['status'] == 'processed' ? 'selected' : '' ?>>Processed</option>
                                <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-update">Update</button>
                        </form>
                        <button onclick="window.location.href='order_detail_admin.php?order_id=<?= $row['order_id'] ?>'" class="btn btn-detail"><i class="fa-solid fa-eye fa-fw"></i> Detail</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>