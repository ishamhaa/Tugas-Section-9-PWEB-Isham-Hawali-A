<?php
session_start();
include 'config.php';

// Proteksi halaman: Hanya admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$message = "";

// Ambil data produk saat ini
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    header("Location: index.php");
    exit;
}

// Proses Update Data
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];
    $target_file = $product['image_path']; // Default menggunakan gambar lama

    // Cek jika admin mengunggah gambar baru
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] !== "") {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $new_file = $target_dir . time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $new_file)) {
            $target_file = $new_file;
        }
    }

    $update_stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, price = ?, category = ?, description = ?, rating = ?, image_path = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_stmt, "sdssdsi", $name, $price, $category, $description, $rating, $target_file, $id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        header("Location: index.php");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Gagal memperbarui produk.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Edit Produk Kopipiro</h5>
                    </div>
                    <div class="card-body">
                        <?= $message ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" class="form-control" name="price" value="<?= $product['price'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category" required>
                                        <option value="kopi" <?= ($product['category'] ?? '') === 'kopi' ? 'selected' : '' ?>>Kopi</option>
                                        <option value="non-kopi" <?= ($product['category'] ?? '') === 'non-kopi' ? 'selected' : '' ?>>Non-Kopi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating (1.0 - 5.0)</label>
                                <input type="number" step="0.1" min="1" max="5" class="form-control" name="rating" value="<?= $product['rating'] ?? 4.5 ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar Produk (Biarkan kosong jika tidak ingin diubah)</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="submit" class="btn btn-dark">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>