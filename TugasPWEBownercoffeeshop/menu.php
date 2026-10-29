<?php
session_start();
include 'config.php';

$result = mysqli_query($conn, "SELECT * FROM products");

while ($row = mysqli_fetch_assoc($result)) {
    $original_price = $row['price'];
    $final_price = $original_price; 
    $is_discounted = false;

    // Cek apakah pembeli sudah login sebagai customer
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
        $final_price = $original_price * 0.90; // Diskon 10%
        $is_discounted = true;
    }

    echo "<div class='product-card'>";
    echo "<img src='" . $row['image_path'] . "' alt='" . $row['name'] . "' width='200'>";
    echo "<h3>" . $row['name'] . "</h3>";
    
    if ($is_discounted) {
        echo "<p><del>Rp " . number_format($original_price, 0, ',', '.') . "</del></p>";
        echo "<p style='color: green;'><strong>Rp " . number_format($final_price, 0, ',', '.') . " (Member Price)</strong></p>";
    } else {
        echo "<p><strong>Rp " . number_format($original_price, 0, ',', '.') . "</strong></p>";
    }
    
    echo "<button>Pesan Kopi</button>";
    echo "</div>";
}
?>