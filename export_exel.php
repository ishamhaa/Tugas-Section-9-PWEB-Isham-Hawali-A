<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: index.php");
    exit;
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_Kopipiro.xls");

$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
</head>
<body>
    <h3>Laporan Transaksi Kopipiro</h3>
    <table border="1">
        <tr>
            <th>ID Pesanan</th>
            <th>Tanggal Transaksi</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>ID Kasir</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['order_date'] ?></td>
            <td><?= $row['total_price'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= $row['kasir_id'] ? $row['kasir_id'] : 'Belum diproses' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>