<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: index.php");
    exit;
}

$chart_data = mysqli_query($conn, "SELECT DATE(order_date) as tgl, SUM(total_price) as total FROM orders WHERE status = 'completed' GROUP BY DATE(order_date)");
$labels = [];
$totals = [];
while ($row = mysqli_fetch_assoc($chart_data)) {
    $labels[] = $row['tgl'];
    $totals[] = $row['total'];
}

$all_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Owner - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Laporan Penjualan (Owner)</h2>
            <div>
                <a href="index.php" class="btn btn-secondary me-2">Lihat Web</a>
                <a href="export_excel.php" class="btn btn-success me-2">Unduh Excel</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-dark text-white">Grafik Penjualan Harian</div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>

        <h4>Semua Riwayat Transaksi</h4>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Waktu Pesan</th>
                    <th>Total</th>
                    <th>ID Kasir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($all_orders)): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    <td><?= $row['kasir_id'] ? 'Kasir #'.$row['kasir_id'] : '-' ?></td>
                    <td>
                        <?php if($row['status'] === 'completed'): ?>
                            <span class="badge bg-success">Selesai</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Bisa diganti 'line' jika ingin grafik garis
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Penjualan Harian (Rp)',
                    data: <?= json_encode($totals) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>