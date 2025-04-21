<?php
session_start();
include '../includes/connection.php';

$action = $_POST['action'];

if (!isset($_SESSION['username'])) {
    echo "You are not logged in!";
    return;
}

if ($action == "insert") {
    $productId = $_POST['productId'];
    $userId = $_POST['userId'];

    if (empty($productId) || empty($userId)) {
        echo "Looks like you missed some fields. Please check and try again!";
        return;
    }

    // Get product details from DB
    $stmt = $con->prepare("SELECT id, name, price, pro_img FROM product WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "Invalid Product.";
        return;
    }

    // Prepare cart item
    $cartItem = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['pro_img'],
        'quantity' => 1,
        'size' => null
    ];

    // If product already exists in cart
    if (isset($_SESSION['cart'][$productId])) {
        echo "Product already in cart.";
        return;
    }

    // Add to session
    $_SESSION['cart'][$productId] = $cartItem;

    echo "Product Added Successfully";
    return;
}

if ($action == "delete") {
    $productId = $_POST['productId'];

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo "Product Deleted Successfully";
    } else {
        echo "Product not found in cart.";
    }
}

// Handle Checkout action
if ($action == 'checkout') {
    $cartData = json_decode($_POST['cart'], true);
    
    if (empty($cartData)) {
        echo "Your cart is empty!";
        return;
    }

    // Validate and process the cart data
    foreach ($cartData as $item) {
        $productId = $item['id'];
        $quantity = $item['qty'];
        $size = $item['size'];

        // Validate if product exists in the cart session
        if (!isset($_SESSION['cart'][$productId])) {
            echo "Invalid product data.";
            return;
        }

        // Validate size and quantity
        if (empty($size)) {
            echo "Please select a size for product: {$item['name']}.";
            return;
        }

        if ($quantity <= 0) {
            echo "Invalid quantity for product: {$item['name']}.";
            return;
        }

        // Process the order (this can involve updating inventory, inserting into orders table, etc.)
        // Placeholder logic here
        // Example: Add this to an orders table in the DB
        // (Insert order details, cart items, etc.)
    }

    // If everything is valid, proceed to the checkout page
    echo 'success';
    return;
}
?>
