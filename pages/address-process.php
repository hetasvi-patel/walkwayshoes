<?php
session_start();
include '../includes/connection.php';

$userId = $_POST['userId'] ?? '';
$proId = $_POST['proId'] ?? '';
$fname = $_POST['fname'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$phoneno = $_POST['phoneno'] ?? '';
$email = $_POST['email'] ?? '';
// Check for empty fields (proId can be blank in case of cart order)
if (empty($userId) || empty($fname) || empty($address) || empty($city) || empty($state) || empty($pincode) || empty($phoneno) || empty($email)) {
    echo "Fill all fields!";
    exit;
}

// Validate pincode (Indian format: 6 digits, optional space after 3)
$pincode_pattern = '/^[1-9][0-9]{2}\s?[0-9]{3}$/';
if (!preg_match($pincode_pattern, $pincode)) {
    echo "Invalid Pincode";
    exit;
}

// Validate phone number (10 digits)
$phoneno_pattern = '/^[0-9]{10}$/';
if (!preg_match($phoneno_pattern, $phoneno)) {
    echo "Invalid Phone Number";
    exit;
}

// Validate email
$email_pattern = '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i';
if (!preg_match($email_pattern, $email)) {
    echo "Invalid email address.";
    exit;
}

// Escape data to prevent SQL injection
$fname = mysqli_real_escape_string($con, $fname);
$address = mysqli_real_escape_string($con, $address);
$city = mysqli_real_escape_string($con, $city);
$state = mysqli_real_escape_string($con, $state);
$pincode = mysqli_real_escape_string($con, $pincode);
$phoneno = mysqli_real_escape_string($con, $phoneno);
$email = mysqli_real_escape_string($con, $email);
$proId = mysqli_real_escape_string($con, $proId);

// Check if the product exists in the product table
$productCheck = mysqli_query($con, "SELECT id FROM product WHERE id = '$proId'");
echo "Product ID: " . $proId;

if (mysqli_num_rows($productCheck) == 0) {
    echo "Invalid Product ID.";
    exit;
}

// Insert into addressdetails table
$addressSql = "INSERT INTO addressdetails(user_id, pro_id, name, address, city, state, pincode, phoneno, email)
               VALUES('$userId', '$proId', '$fname', '$address', '$city', '$state', '$pincode', '$phoneno', '$email')";

if (mysqli_query($con, $addressSql)) {
    $addressId = mysqli_insert_id($con);


    $orderSql = "INSERT INTO `order` (user_id, address_id, pro_id, order_status)
                 VALUES ('$userId', '$addressId', '$proId', 'Pending')";
    
    if (mysqli_query($con, $orderSql)) {
        echo "Address Registered and Order Placed Successfully.";
    } else {
        echo "Error: Could not place order. " . mysqli_error($con);
    }
} else {
    echo "Error: Could not register address. " . mysqli_error($con);
}

mysqli_close($con);
?>
