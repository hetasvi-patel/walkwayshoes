<?php
include '../includes/connection.php';

$action = $_POST['action'];
if ($action == 'insert') {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $address_id = $_POST['address_id'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $totalprice = $_POST['totalprice'];

    if ($user_id == "" || $product_id == "" || $address_id == "" || $address_id == "" || $price == "" || $size == "" || $quantity == "" || $totalprice == "") {
        echo "Looks like you missed some fields. Please check and try again!";
        return;
    }

    $sql = "INSERT INTO `order` (user_id,product_id,address_id,rate,pro_size,quantity,totalprice,status) VALUES('$user_id','$product_id','$address_id','$price','$size','$quantity','$totalprice','pending')";

    if (mysqli_query($con, $sql)) {
        echo "Your Order is successfully added";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
}
