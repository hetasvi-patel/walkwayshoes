<?php
include '../includes/header.php';
if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='/theshoesbox/pages/login.php';</script>";
    exit;
}
?>

<div class="hero-wrap hero-bread" style="background-image: url('/theshoesbox/assets/images/bg_6.jpg');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs"><span class="mr-2"><a href="/theshoesbox/index.php">Home</a></span> <span>Cart</span></p>
                <h1 class="mb-0 bread">My Cart</h1>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section ftco-cart">
    <div class="container">
        <div class="row">
            <div class="col-md-12 ftco-animate">
                <div class="cart-list">
                    <table class="table">
                        <thead class="thead-primary">
                            <tr class="text-center">
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $cart = $_SESSION['cart'] ?? [];
                        if (!empty($cart)) {
                            foreach ($cart as $item) {
                                echo '<tr class="text-center product-row" data-product-id="' . $item['id'] . '">';
                                echo '  <td class="product-remove align-middle"><a href="#" class="delete-product" data-productid="' . $item['id'] . '"><span class="ion-ios-close"></span></a></td>';
                                echo '  <td class="image-prod align-middle"><div class="img" style="background-image:url(/theshoesbox/admin/assets/images/product/' . $item['image'] . '); width:60px; height:60px; background-size:contain;"></div></td>';
                                echo '  <td class="product-name align-middle"><h3>' . htmlspecialchars($item['name']) . '</h3></td>';
                                echo '  <td class="price align-middle">₹ ' . number_format($item['price'], 2) . '</td>';
                                echo '  <td class="quantity align-middle p-0">';
                                echo '    <div class="input-group input-group-sm" style="width:120px; margin:auto;">';
                                echo '      <button class="btn btn-outline-secondary quantity-minus" type="button" style="width: 30px;">-</button>';
                                echo '      <input type="text" class="quantity form-control text-center" value="' . $item['quantity'] . '" min="1" max="100">';
                                echo '      <button class="btn btn-outline-secondary quantity-plus" type="button" style="width: 30px;">+</button>';
                                echo '    </div>';
                                echo '  </td>';
                                $line = $item['price'] * $item['quantity'];
                                echo '  <td class="total align-middle">₹ ' . number_format($line, 2) . '</td>';
                                echo '  <td class="align-middle"><button class="btn btn-primary btn-sm purchase-btn" data-product-id="' . $item['id'] . '">Purchase</button></td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">No items in the cart.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row justify-content-start">
            
            <div class="col-lg-5 col-md-6 mt-5 cart-wrap ftco-animate">
                <div class="cart-total mb-3">
                    <h3>Cart Totals</h3>
                    <hr>
                    <p class="d-flex total-price"><span>Total</span><span id="cart-total">₹ 0.00</span></p>
                </div>
                <div class="col-md-6 ">
    <button id="purchase-all-btn" class="btn btn-primary btn-sm purchase-btn">Purchase All</button>
</div>

            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
$(function() {
    function updateCartTotal() {
        var total = 0;
        $('.product-row').each(function() {
            var price = parseFloat($(this).find('.price').text().replace(/[^0-9.]/g, ''));
            var qty = parseInt($(this).find('input.quantity').val(), 10) || 1;
            var line = price * qty;
            $(this).find('.total').text('₹ ' + line.toFixed(2));
            total += line;
        });
        $('#cart-total').text('₹ ' + total.toFixed(2));
    }

    $('.quantity-minus').on('click', function() {
        var $inp = $(this).siblings('input.quantity');
        var v = parseInt($inp.val(), 10) || 1;
        if (v > 1) $inp.val(v - 1);
        updateCartTotal();
    });

    $('.quantity-plus').on('click', function() {
        var $inp = $(this).siblings('input.quantity');
        var v = parseInt($inp.val(), 10) || 1;
        $inp.val(v + 1);
        updateCartTotal();
    });

    updateCartTotal();

    // Purchase (without size)
    $('.purchase-btn').on('click', function() {
        var $row = $(this).closest('.product-row');
        var pid = $(this).data('product-id');
        var qty = $row.find('input.quantity').val();
        var url = '/theshoesbox/pages/address-details.php?pro_id=' + pid + '&qty=' + qty;
        window.location.href = url;
    });
        // Purchase all
    $('#purchase-all-btn').on('click', function() {
        var cartItems = [];

        $('.product-row').each(function() {
            var $row = $(this);
            var pid = $row.data('product-id');
            var qty = parseInt($row.find('input.quantity').val(), 10) || 1;

            cartItems.push({
                id: pid,
                qty: qty
                // If size feature is required later, add size here
            });
        });

        if (cartItems.length === 0) {
            swal('Oops!', 'Your cart is empty!', 'warning');
            return;
        }

        var cartJson = encodeURIComponent(JSON.stringify(cartItems));
        var url = '/theshoesbox/pages/address-details.php?cart=' + cartJson;
        window.location.href = url;
    });


    // Delete
    $(document).on('click', '.delete-product', function(e) {
        e.preventDefault();
        var pid = $(this).data('productid');
        swal({
            title: 'Are you sure?',
            text: 'You won\'t get it back!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }, function() {
            $.post('/theshoesbox/processes/cart-process.php', { action: 'delete', productId: pid }, function(res) {
                if (res.includes('Deleted')) location.reload();
                else swal('Error', res, 'error');
            });
        });
    });
});
</script>
