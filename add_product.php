<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    $target_dir = "uploads/";
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
    
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Memasukkan name, price, image_path, category, description, dan rating
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, image_path, category, description, rating) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sdsssd", $name, $price, $target_file, $category, $description, $rating);
            mysqli_stmt_execute($stmt);
            $message = "<div class='alert alert-success'>Produk berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Gagal memindahkan file gambar.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>File yang diunggah bukan gambar.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Tambah Menu Kopipiro</h5>
                    </div>
                    <div class="card-body">
                        <?= $message ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" class="form-control" name="price" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category" required>
                                        <option value="kopi">Kopi</option>
                                        <option value="non-kopi">Non-Kopi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating Awal (1.0 - 5.0)</label>
                                <input type="number" step="0.1" min="1" max="5" value="4.5" class="form-control" name="rating" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Jelaskan cita rasa, bahan, dll..." required></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="image" accept="image/*" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="submit" class="btn btn-dark">Simpan Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>