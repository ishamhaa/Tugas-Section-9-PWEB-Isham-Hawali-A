<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasir') {
    header("Location: index.php");
    exit;
}

$kasir_id = $_SESSION['user_id'];

if (isset($_POST['selesaikan'])) {
    $order_id = $_POST['order_id'];
    
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'completed', kasir_id = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $kasir_id, $order_id);
    mysqli_stmt_execute($stmt);
    echo "<script>alert('Pesanan berhasil diselesaikan!'); window.location='kasir_dashboard.php';</script>";
}

$pending_orders = mysqli_query($conn, "SELECT * FROM orders WHERE status = 'pending' ORDER BY order_date ASC");

$my_orders = mysqli_query($conn, "SELECT * FROM orders WHERE kasir_id = $kasir_id ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Kasir</h2>
            <div>
                <a href="index.php" class="btn btn-secondary me-2">Lihat Web</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <h4 class="mb-3">Pesanan Masuk (Pending)</h4>
        <table class="table table-bordered bg-white shadow-sm mb-5">
            <thead class="table-dark">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Waktu Pesan</th>
                    <th>Total Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($pending_orders)): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="selesaikan" class="btn btn-primary btn-sm">Proses & Selesaikan</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h4 class="mb-3">Riwayat Pesanan yang Anda Kelola</h4>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-secondary">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Waktu Pesan</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($my_orders)): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    <td><span class="badge bg-success">Selesai</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>