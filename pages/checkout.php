<?php
include '../includes/header.php';
include '../includes/connection.php';

if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='/theshoesbox/pages/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['pro_id'])) {
    echo "<script>alert('No product selected.'); window.location.href='/theshoesbox/index.php';</script>";
    exit;
}

$pro_id = intval($_GET['pro_id']); // safe conversion to int

// Fetch Address
$sql = "SELECT * FROM addressdetails WHERE user_id=$user_id AND pro_id=$pro_id";
$data = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Please enter address first.'); window.location.href='/theshoesbox/pages/address-details.php?pro_id=$pro_id';</script>";
    exit;
}
$address_id = $row['id'];

// Fetch Product Details
$sql = "SELECT brand.name AS bname, product.id, product.name, product.price, product.pro_img 
        FROM brand, product 
        WHERE brand.id = product.brand_id AND product.id = '$pro_id'";
$product_data = mysqli_query($con, $sql);
$product = mysqli_fetch_assoc($product_data);

if (!$product) {
    echo "<script>alert('Product not found.'); window.location.href='/theshoesbox/index.php';</script>";
    exit;
}

$price = $product['price'];
?>

<section class="ftco-section contact-section bg-light">
    <div class="container">
        <h3 class="mb-4 billing-heading">Order</h3>
        <div class="row">
            <!-- Product Details -->
            <div class="col-md-6 mb-5">
                <div class="card shadow">
                    <div class="bg-white p-5 contact-form">
                        <h3 class="billing-heading mb-4 text-center">Product Details</h3>
                        <div class="row">
                            <div class="col-lg-4 mb-3 ftco-animate">
                                <img src="/theshoesbox/admin/assets/images/product/<?php echo $product['pro_img'] ?>" height="200px" width="200px" class="img-fluid">
                            </div>
                            <div class="col-lg-7 product-details pl-md-3 ftco-animate">
                                <h3><?php echo $product['name'] ?></h3>
                                <p><strong>Brand:</strong> <?php echo $product['bname'] ?></p>
                                <p class="price"><span>₹ <?php echo $price ?></span></p>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <span>Product Size</span>
                                        <div class="form-group d-flex">
                                            <div class="select-wrap">
                                                <select name="size" id="size" class="form-control">
                                                    <?php for ($i = 4; $i <= 10; $i++) : ?>
                                                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment & Quantity -->
            <div class="col-md-6 mb-5">
                <div class="card shadow">
                    <div class="info bg-white p-4">
                        <div class="cart-detail cart-total bg-light p-3 p-md-4">
                            <h3 class="billing-heading mb-4 text-center">Total Payment</h3>
                            <p class="d-flex">
                                <span>Price</span>
                                <span class="price" id="price">₹ <?php echo $price ?></span>
                            </p>
                            <div class="d-flex">
                                <span>Quantity</span>
                                <div class="quantity">
                                    <div class="input-group mb-3">
                                        <button class="quantity-minus px-2 border-0" type="button">-</button>
                                        <input type="number" name="quantity" id="quantity" class="quantity form-control input-number" value="1" min="1" max="100" oninput="updateCartTotal();">
                                        <button class="quantity-plus px-2 border-0" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <p class="d-flex">
                                <span>Total Price</span>
                                <span class="total" id="totalprice">₹ <?php echo $price ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-5 mb-5">
                <div class="card shadow">
                    <div class="info bg-white p-4 text-center">
                        <div class="cart-detail bg-light p-3 p-md-4">
                            <h3 class="billing-heading mb-4">Payment Method</h3>

                            <div class="form-check text-left">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                            </div>
                            <div class="form-check text-left mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">Pay with PayPal</label>
                            </div>

                            <!-- PayPal Form -->
                            <form id="paypal-form" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" style="display:none;">
                                <input type="hidden" name="notify_url" value="http://localhost/theshoesbox/pages/ipn_listener.php">
                                <input type="hidden" name="business" value="sb-23lya34085897@business.example.com">
                                <input type="hidden" name="cmd" value="_xclick">
                                <input type="hidden" name="item_name" value="<?php echo $product['name']; ?>">
                                <input type="hidden" name="amount" id="paypal-amount" value="<?php echo $price; ?>">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="return" value="http://localhost/theshoesbox/pages/success.php?status=success">
                                <input type="hidden" name="cancel_return" value="http://localhost/theshoesbox/pages/cancel.php?status=cancel">
                                <button type="submit" style="display:none;">Pay with PayPal</button>
                            </form>

                            <!-- COD Form -->
                            <form id="cod-form" action="/theshoesbox/pages/cod_order.php" method="POST" style="display:none;">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="address_id" value="<?php echo $address_id; ?>">
                                <input type="hidden" name="rate" value="<?php echo $price; ?>">
                                <input type="hidden" name="pro_size" id="cod-size">
                                <input type="hidden" name="quantity" id="cod-quantity">
                                <input type="hidden" name="totalprice" id="cod-total">
                                <input type="hidden" name="status" value="Pending">
                                <button type="submit" style="display:none;">Submit COD</button>
                            </form>

                            <!-- Place Order Button -->
                            <p>
                                <button class="btn btn-primary py-3 px-5 mt-3" id="place-order">Place an Order</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function updateCartTotal() {
    const price = parseFloat(document.getElementById('price').innerText.replace('₹', '').trim());
    const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
    const total = price * quantity;

    document.getElementById('totalprice').innerText = '₹ ' + total.toFixed(2);
    document.getElementById('paypal-amount').value = total.toFixed(2);
    document.getElementById('cod-quantity').value = quantity;
    document.getElementById('cod-total').value = total.toFixed(2);
}

document.querySelector('.quantity-minus').addEventListener('click', function () {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value, 10) || 1;
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateCartTotal();
    }
});

document.querySelector('.quantity-plus').addEventListener('click', function () {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value, 10) || 1;
    if (currentValue < 100) {
        quantityInput.value = currentValue + 1;
        updateCartTotal();
    }
});

document.getElementById('place-order').addEventListener('click', function () {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedMethod) {
        alert('Please select a payment method.');
        return;
    }

    updateCartTotal();

    const method = selectedMethod.value;

    if (method === 'cod') {
        const selectedSize = document.getElementById('size').value;
        if (!selectedSize) {
            alert('Please select a size.');
            return;
        }

        document.getElementById('cod-size').value = selectedSize;
        document.querySelector('#cod-form button[type="submit"]').click();
    } else {
        document.querySelector('#paypal-form button[type="submit"]').click();
    }
});

document.querySelectorAll('input[name="payment_method"]').forEach((input) => {
    input.addEventListener('change', function () {
        if (this.value === 'paypal') {
            document.getElementById('paypal-form').style.display = 'block';
            document.getElementById('cod-form').style.display = 'none';
        } else {
            document.getElementById('paypal-form').style.display = 'none';
            document.getElementById('cod-form').style.display = 'block';
        }
    });
});

updateCartTotal();
</script>
