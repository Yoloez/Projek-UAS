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
    <title>Order History - Orbyt Cafe</title>
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
            --text-light-gray: #718096;
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
            margin-bottom: 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.8rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .header p {
            font-size: 1.1rem;
            color: var(--text-light-gray);
            margin-top: 10px;
        }
        
        /* --- Styling Tabel Riwayat (Desktop) --- */
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
            transition: background-color 0.2s ease;
        }
        .history-table tbody tr:hover {
            background-color: #f9fafc;
        }
        .history-table tbody tr:last-child {
            border-bottom: none;
        }
        
        /* --- Elemen Umum --- */
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-detail:hover {
            background-color: #2563eb;
        }

        .empty-history {
            text-align: center;
            padding: 50px;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.05);
        }
        .empty-history .icon {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 20px;
        }
        .empty-history h2 { margin-bottom: 10px; }
        .empty-history p { color: var(--text-light-gray); margin-bottom: 25px;}

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-weight: 500;
            color: var(--info-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #2563eb;
        }

        /* --- MEDIA QUERY UNTUK RESPONSIVE (MOBILE) --- */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            .history-table thead {
                display: none; /* Sembunyikan header tabel di mobile */
            }
            .history-table, .history-table tbody, .history-table tr, .history-table td {
                display: block;
                width: 100%;
            }
            .history-table tr {
                border: 1px solid var(--border-color);
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 15px;
            }
            .history-table td {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px dashed var(--border-color);
            }
            .history-table td:last-child {
                border-bottom: none;
            }
            .history-table td::before {
                content: attr(data-label); /* Tampilkan label dari atribut data-label */
                font-weight: 600;
                color: var(--text-dark);
            }
            .btn-detail {
                width: 100%;
                justify-content: center;
                padding: 12px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Order History</h1>
            <p>Here's your history's list of transaction </p>
        </div>

        <?php if (mysqli_num_rows($history_result) > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Order id</th>
                        <th>Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td data-label="ID Pesanan"><strong>#<?= $order['order_id'] ?></strong></td>
                            <td data-label="Tanggal"><?= date("d F Y, H:i", strtotime($order['created_at'])) ?></td>
                            <td data-label="Total Harga">Rp<?= number_format($order['total_price']) ?></td>
                            <td data-label="Status">
                                <span class="status <?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                            </td>
                            <td data-label="Aksi">
                                <a href="order_details.php?order_id=<?= $order['order_id'] ?>" class="btn-detail" style="color: white;padding: 12px 1px; width:50%">  
                                    <i class="fa-solid fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-history">
                <div class="icon"><i class="fa-solid fa-box-open"></i></div>
                <h2>You don't order yet.</h2>
                <p>Let's buy some of our stuff!</p>
                <a href="user.php" class="btn-detail" style="background-color: var(--success-color);">Start Shopping</a>
            </div>
        <?php endif; ?>

         <?php if (mysqli_num_rows($history_result) > 0): ?>
         <div style="text-align: center; margin-top:30px;">
             <a href="user.php" class="back-link"> &larr; Back to Catalog</a>
         </div>
         <?php endif; ?>
    </div>
</body>
</html>