<?php
session_start();
include 'config.php';

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop</title>
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
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="add_product.php" class="btn btn-outline-light btn-sm me-2">Kelola Produk</a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                <?php else: ?>
                   
                    <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    
    <div class="container mt-5">
        <h2 class="mb-4">Menu Kopi</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $original_price = $row['price'];
                    $final_price = $original_price;
                    $is_discounted = false;

                    
                    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
                        $final_price = $original_price * 0.90;
                        $is_discounted = true;
                    }
                ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm">
                        
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                            
                            <p class="text-muted small mb-1 fw-bold"><?= ucfirst($row['category']) ?> | @ <?= $row['rating'] ?>/5.0</p>
                            <p class="card-text text-muted small" style="font-size: 12px;"><?= htmlspecialchars($row['description']) ?></p>

                            <?php if ($is_discounted): ?>
                                <p class="text-muted text-decoration-line-through mb-0">Rp <?= number_format($original_price, 0, ',', '.') ?></p>
                                <h5 class="text-success fw-bold">Rp <?= number_format($final_price, 0, ',', '.') ?></h5>
                                <span class="badge bg-success mb-3 w-50 align-self-center">Diskon 10%</span>
                            <?php else: ?>
                                <h5 class="fw-bold mt-3 mb-4">Rp <?= number_format($original_price, 0, ',', '.') ?></h5>
                            <?php endif; ?>
                            
                            <a href="order.php?id=<?= $row['id'] ?>" class="btn btn-dark mt-auto w-100">Pesan</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>