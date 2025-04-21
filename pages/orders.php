<?php
include '../includes/header.php';
if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='/theshoesbox/pages/login.php';</script>";
}
include '../includes/connection.php';
?>
<?php
$user_id = $_SESSION['user_id'];
?>

<div class="hero-wrap hero-bread" style="background-image: url('/theshoesbox/assets/images/bg_6.jpg');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs"><span class="mr-2"><a href="/theshoesbox/index.php">Home</a></span> <span>Order</span></p>
                <h1 class="mb-0 bread">Order</h1>
            </div>
        </div>
    </div>
</div>
<section class="ftco-section contact-section bg-light">
    <div class="container">
        <h3 class="mb-4 billing-heading">Order</h3>
        <div class="row">
            <div class="col-md-12 mb-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title">Order History</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>ProductName</th>
                                        <th>ProductImage</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT o.*, p.name AS product_name, p.pro_img AS pro_img, a.name AS address_name FROM `order` o
                                        LEFT JOIN users u ON o.user_id = u.id
                                        LEFT JOIN product p ON o.product_id = p.id
                                        LEFT JOIN addressdetails a ON o.address_id = a.id
                                        WHERE o.user_id ='$user_id' ORDER BY o.id desc";
                                    $data = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($data)) {
                                        echo "<tr>
                                            <td>" . $row['address_name'] . "</td>
                                            <td>" . $row['product_name'] . "</td>
                                            <td><img src='/theshoesbox/admin/assets/images/product/" . $row['pro_img'] . "' alt='Product Image' height='100'></td>
                                            <td>" . $row['pro_size'] . "</td>
                                            <td>₹ " . number_format($row['rate'], 2) . "</td>
                                            <td>" . $row['quantity'] . "</td>
                                            <td>₹ " . number_format($row['totalprice'], 2) . "</td>
                                            <td>";
                                        switch ($row['status']) {
                                            case 'cancel':
                                                echo "<span class='badge badge-danger'>" . $row['status'] . "</span>";
                                                break;
                                            case 'pending':
                                                echo "<span class='badge badge-warning'>" . $row['status'] . "</span>";
                                                break;
                                            case 'confirm':
                                                echo "<span class='badge badge-success'>" . $row['status'] . "</span>";
                                                break;
                                            default:
                                                echo "<span class='badge badge-info'>" . $row['status'] . "</span>";
                                        }
                                        echo "</td>
                                            <td>";
                                        switch ($row['status']) {
                                            case 'cancel':
                                                echo "<button type='button' class='btn btn-primary re-btn' data-id='" . $row['id'] . "'>Re-Order</button>";
                                                break;
                                            default:
                                                echo "<button type='submit' class='btn btn-danger cancel-btn' data-id='" . $row['id'] . "'>Cancel</button>";
                                        }
                                        echo "</td>
                                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>
<script>
    // Re-Order
    $(".re-btn").on("click", function() {
        var id = $(this).data('id');
        updateOrder(id, 'pending');
    });
    // Cancel Order
    $(".cancel-btn").on("click", function() {
        var id = $(this).data('id');
        updateOrder(id, 'cancel');
    });

    function updateOrder(id, status) {
        $.ajax({
            type: "POST",
            url: "/theshoesbox/processes/order-process.php",
            data: {
                action: "pending",
                status: status,
                id: id,
            },
            success: function(res) {
                console.log(res);
                var successMessage = "";

                if (status === "pending") {
                    successMessage = "Order Is Replaced";
                } else if (status === "cancel") {
                    successMessage = "Order Is Canceled";
                }

                swal({
                    title: "Success",
                    text: successMessage,
                    type: "success"
                }, function() {
                    window.location = '/theshoesbox/pages/orders.php';
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal({
                    title: "Error",
                    text: "AJAX Error: " + status + " - " + error,
                    type: "error"
                });
            }
        });
    }
const url = new URL(window.location.href);
    if (url.searchParams.has("order_id")) {
        url.searchParams.delete("order_id");
        window.history.replaceState({}, document.title, url.pathname);
    }
</script>