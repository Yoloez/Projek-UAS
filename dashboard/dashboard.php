<?php
include '../koneksi.php';
session_start();

// Validasi session admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../authentication/login/login.php");
    exit();
}

// --- PENGAMBILAN DATA STATISTIK ---

// 1. Total Pendapatan (dari pesanan yang sudah 'completed')
$revenue_result = mysqli_query($conn, "SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'completed'");
$total_pendapatan = mysqli_fetch_assoc($revenue_result)['total_revenue'] ?? 0;

// 2. Total Pesanan
$orders_result = mysqli_query($conn, "SELECT COUNT(*) as total_orders FROM orders");
$total_pesanan = mysqli_fetch_assoc($orders_result)['total_orders'];

// 3. Total Menu
$foods_result = mysqli_query($conn, "SELECT COUNT(*) as total_foods FROM foods");
$total_menu = mysqli_fetch_assoc($foods_result)['total_foods'];

// 4. Total Pelanggan (yang rolenya 'user')
$users_result = mysqli_query($conn, "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$total_pelanggan = mysqli_fetch_assoc($users_result)['total_users'];

// --- DATA UNTUK GRAFIK PENDAPATAN 7 HARI TERAKHIR ---
$sales_data = [];
$sales_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days"));
    $sales_labels[] = date("d M", strtotime("-$i days"));
    $query = "SELECT SUM(total_price) as daily_revenue FROM orders WHERE status = 'completed' AND DATE(created_at) = '$date'";
    $daily_result = mysqli_query($conn, $query);
    $daily_revenue = mysqli_fetch_assoc($daily_result)['daily_revenue'] ?? 0;
    $sales_data[] = $daily_revenue;
}

// --- DATA UNTUK GRAFIK STATUS PESANAN ---
$status_result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$status_labels = [];
$status_data = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_labels[] = ucfirst($row['status']);
    $status_data[] = $row['count'];
}

// --- DATA UNTUK TABEL PESANAN TERBARU ---
$recent_orders = mysqli_query($conn, "
    SELECT o.order_id, o.total_price, o.status, u.name as user_name
    FROM orders o
    JOIN users u ON o.id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Orbyt Cafe</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-bg: #2c211d;
            --bg-color: #f4f7fe;
            --card-bg: rgba(255, 255, 255, 0.6);
            --primary-green: #00b09b;
            --secondary-green: #96c93d;
            --text-dark: #1b254b;
            --text-light: #a0aec0;
            --border-color: #e2e8f0;
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

        /* --- Statistic Cards --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .stat-card .icon {
            font-size: 2.5rem;
            padding: 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-card .value { font-size: 2rem; font-weight: 700; color: var(--text-dark); }
        .stat-card .label { font-size: 1rem; color: var(--text-light); }
        .icon.revenue { color: #22c55e; background-color: #dcfce7; }
        .icon.orders { color: #3b82f6; background-color: #dbeafe; }
        .icon.foods { color: #f97316; background-color: #ffedd5; }
        .icon.users { color: #8b5cf6; background-color: #ede9fe; }

        /* --- Charts --- */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .chart-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        .chart-container h3 { margin-bottom: 20px; color: var(--text-dark); }
        
        /* --- Recent Orders Table --- */
        .recent-orders h3 { margin-bottom: 20px; color: var(--text-dark); }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .data-table th, .data-table td { padding: 15px; text-align: left; }
        .data-table thead { background-color: #f8fafc; }
        .data-table th { font-weight: 600; color: var(--text-dark); }
        .data-table tbody tr { border-bottom: 1px solid var(--border-color); }
        .data-table tbody tr:last-child { border-bottom: none; }
        .status { padding: 5px 12px; border-radius: 15px; color: #fff; font-size: 0.8em; font-weight: 600; text-transform: capitalize; }
        .status.pending { background-color: #f59e0b; }
        .status.processed { background-color: #3b82f6; }
        .status.completed { background-color: #22c55e; }

    </style>
</head>
<body>

<div class="sidebar">
    <h2> Admin</h2>
    <a href="dashboard.php" class="active"><i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard</a>
    <a href="tambah.php"><i class="fa-solid fa-plus fa-fw"></i> Tambah Menu</a>
    <a href="foods.php"><i class="fa-solid fa-utensils fa-fw"></i> Kelola Menu</a>
    <a href="orders.php"><i class="fa-solid fa-box fa-fw"></i> Kelola Pesanan</a>
    <a href="../logout.php" class="logout"><i class="fa-solid fa-sign-out-alt fa-fw"></i> Logout</a>
</div>

<div class="content-wrapper">
    <div class="content-header">
        <h2>Dashboard</h2>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon revenue"><i class="fa-solid fa-sack-dollar"></i></div>
            <div>
                <div class="value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                <div class="label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon orders"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <div class="value"><?= $total_pesanan ?></div>
                <div class="label">Total Pesanan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon foods"><i class="fa-solid fa-utensils"></i></div>
            <div>
                <div class="value"><?= $total_menu ?></div>
                <div class="label">Jumlah Menu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon users"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="value"><?= $total_pelanggan ?></div>
                <div class="label">Total User</div>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-container">
            <h3>Tren Pendapatan (7 Hari Terakhir)</h3>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Status Pesanan</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="recent-orders">
        <h3>Pesanan Terbaru</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                <tr>
                    <td><strong>#<?= $order['order_id'] ?></strong></td>
                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                    <td>Rp <?= number_format($order['total_price']) ?></td>
                    <td><span class="status <?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data dari PHP untuk Chart.js
    const salesLabels = <?= json_encode($sales_labels) ?>;
    const salesData = <?= json_encode($sales_data) ?>;
    const statusLabels = <?= json_encode($status_labels) ?>;
    const statusData = <?= json_encode($status_data) ?>;

    // Grafik Tren Pendapatan (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Pendapatan',
                data: salesData,
                backgroundColor: 'rgba(0, 176, 155, 0.1)',
                borderColor: 'rgba(0, 176, 155, 1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(0, 176, 155, 1)',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Grafik Status Pesanan (Doughnut Chart)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: statusData,
                backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>

</body>
</html>