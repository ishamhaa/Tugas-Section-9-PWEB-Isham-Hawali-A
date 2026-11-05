<?php
session_start();
include 'config.php';

$category_filter = $_GET['category'] ?? 'all';

if ($category_filter !== 'all') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $category_filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Coffee Shop</a>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-white me-3">
                        Halo, <?= htmlspecialchars($_SESSION['username']) ?> 
                        <span class="badge bg-secondary"><?= $_SESSION['role'] ?></span>
                    </span>
                    <?php if ($_SESSION['role'] === 'owner'): ?>
                        <a href="add_product.php" class="btn btn-outline-light btn-sm me-2">Kelola Produk</a>
                        <a href="owner_dashboard.php" class="btn btn-warning btn-sm me-2">Dashboard Owner</a>
                    <?php elseif ($_SESSION['role'] === 'kasir'): ?>
                        <a href="kasir_dashboard.php" class="btn btn-warning btn-sm me-2">Dashboard Kasir</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-3 text-center">Menu Kopipiro</h2>

        <!-- Filter Kategori -->
        <div class="d-flex justify-content-center gap-2 mb-4">
            <a href="index.php" class="btn btn-sm <?= $category_filter === 'all' ? 'btn-dark' : 'btn-outline-dark' ?>">Semua</a>
            <a href="index.php?category=kopi" class="btn btn-sm <?= $category_filter === 'kopi' ? 'btn-dark' : 'btn-outline-dark' ?>">Kopi</a>
            <a href="index.php?category=non-kopi" class="btn btn-sm <?= $category_filter === 'non-kopi' ? 'btn-dark' : 'btn-outline-dark' ?>">Non-Kopi</a>
        </div>

        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $original_price = $row['price'];
                    $final_price = $original_price;
                    $is_discounted = false;

                    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
                        $final_price = $original_price * 0.90; // Perbaikan perkalian diskon 10%
                        $is_discounted = true;
                    }
                ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body text-center d-flex flex-column">
                            <span class="badge bg-secondary mb-2 align-self-center"><?= ucfirst($row['category'] ?? 'kopi') ?></span>
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                            
                            <div class="text-warning mb-2">★ <?= $row['rating'] ?? '5.0' ?> / 5.0</div>

                            <!-- Menampilkan Harga Produk -->
                            <div class="mb-3">
                                <?php if ($is_discounted): ?>
                                    <small class="text-decoration-line-through text-muted">Rp <?= number_format($original_price, 0, ',', '.') ?></small><br>
                                    <span class="fw-bold text-success">Rp <?= number_format($final_price, 0, ',', '.') ?></span>
                                    <span class="badge bg-success ms-1">Member 10%</span>
                                <?php else: ?>
                                    <span class="fw-bold text-dark">Rp <?= number_format($original_price, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Tombol Modal Deskripsi -->
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#descModal<?= $row['id'] ?>">
                                Detail Produk
                            </button>

                            <!-- Tombol Pesan -->
                            <a href="order.php?id=<?= $row['id'] ?>" class="btn btn-dark mt-auto w-100">Pesan</a>

                            <!-- Tombol Edit Khusus Owner -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'owner'): ?>
                                <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm mt-2 w-100">Edit Produk</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modal Popup untuk Deskripsi Detail -->
                <div class="modal fade" id="descModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <p><strong>Kategori:</strong> <?= ucfirst($row['category'] ?? 'kopi') ?></p>
                                <p><strong>Rating:</strong> ★ <?= $row['rating'] ?? '5.0' ?> / 5.0</p>
                                <hr>
                                <p><strong>Deskripsi:</strong></p>
                                <p><?= nl2br(htmlspecialchars($row['description'] ?? 'Belum ada deskripsi untuk produk ini.')) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>