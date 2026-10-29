<?php
session_start();

if(isset($_GET['id'])){
    $product_id = $_GET['id'];

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id] += 1;
    }else{
        $_SESSION['cart'][$product_id] = 1;
    }
}

header("Location: checkout.php");
exit;
?>