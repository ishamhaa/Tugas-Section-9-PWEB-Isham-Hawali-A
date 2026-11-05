<?php
session_start();
include 'config.php';

if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang belanja kosong!'); window.location='index.php';</script>";
    exit;
}

$is_customer = isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer';
$total_all = 0;

if (isset($_POST['checkout'])) {
    $user_id = $is_customer ? $_SESSION['user_id'] : NULL;
    $total_price = $_POST['total_price'];

    $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "id", $user_id, $total_price);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $res = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
        $row = mysqli_fetch_assoc($res);
        
        $price = $is_customer ? $row['price'] * 0.90 : $row['price'];
        
        $stmt_item = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $product_id, $qty, $price);
        mysqli_stmt_execute($stmt_item);
    }
    
    unset($_SESSION['cart']);
    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Ringkasan Pesanan</h2>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_SESSION['cart'] as $product_id => $qty):
                    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
                    $product = mysqli_fetch_assoc($res);
                    
                    $price = $product['price'];
                    if ($is_customer) {
                        $price = $price * 0.90; // Diskon 10%
                    }
                    $subtotal = $price * $qty;
                    $total_all += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $qty ?></td>
                    <td>Rp <?= number_format($price, 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total Pembayaran:</th>
                    <th>Rp <?= number_format($total_all, 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
        
        <form method="POST" action="">
            <input type="hidden" name="total_price" value="<?= $total_all ?>">
            <button type="submit" name="checkout" class="btn btn-success">Konfirmasi & Buat Pesanan</button>
            <a href="index.php" class="btn btn-secondary">Kembali Belanja</a>
        </form>
    </div>
</body>
</html>