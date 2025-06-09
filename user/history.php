<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login dan memiliki user_id di session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Ambil semua data pesanan milik user yang sedang login, urutkan dari yang terbaru
$history_sql = "SELECT order_id, created_at, total_price, status FROM orders WHERE id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $history_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$history_result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Orbyt Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg-color: #f4f7fe;
            --card-bg: #ffffff;
            --primary-color: #00b09b;
            --text-dark: #1b254b;
            --border-color: #e2e8f0;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --success-color: #22c55e;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5rem;
            color: var(--text-dark);
        }
        .header p {
            font-size: 1.1rem;
            color: #718096;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 25px rgba(0,0,0,0.07);
        }
        .history-table th, .history-table td {
            padding: 16px 20px;
            text-align: left;
            vertical-align: middle;
        }
        .history-table thead {
            background-color: #f8fafc;
        }
        .history-table th {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .history-table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }
        .history-table tbody tr:last-child {
            border-bottom: none;
        }
        .status {
            padding: 6px 14px;
            border-radius: 15px;
            color: #fff;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
            display: inline-block;
        }
        .status.pending { background-color: var(--warning-color); }
        .status.processed { background-color: var(--info-color); }
        .status.completed { background-color: var(--success-color); }
        .btn-detail {
            background-color: var(--info-color);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        .btn-detail:hover {
            background-color: #2563eb;
        }
        .empty-history {
            text-align: center;
            padding: 50px;
            background-color: var(--card-bg);
            border-radius: 12px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-weight: 500;
            color: var(--info-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Riwayat Pesanan Anda</h1>
            <p>Berikut adalah daftar semua transaksi yang pernah Anda lakukan.</p>
        </div>

        <?php if (mysqli_num_rows($history_result) > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?= date("d F Y, H:i", strtotime($order['created_at'])) ?></td>
                            <td>Rp <?= number_format($order['total_price']) ?></td>
                            <td>
                                <span class="status <?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                            </td>
                            <td>
                                <a href="order_details.php?order_id=<?= $order['order_id'] ?>" class="btn-detail">Lihat Detail</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-history">
                <h2>Anda belum memiliki riwayat pesanan.</h2>
                <p>Ayo mulai berbelanja di katalog kami!</p>
                <a href="user.php" class="back-link">Kembali ke Katalog</a>
            </div>
        <?php endif; ?>
         <?php if (mysqli_num_rows($history_result) > 0): ?>
         <div style="text-align: center; margin-top:30px;">
         <a href="user.php" class="back-link"> &larr; Kembali ke Katalog </a>
         </div>
         <?php endif; ?>
    </div>
</body>
</html>