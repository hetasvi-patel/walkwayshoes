<?php
include '../includes/header.php';

if (!isset($_SESSION['username'])) {
	echo "<script>window.location.href='/theshoesbox/pages/login.php';</script>";
	exit;
}

include '../includes/connection.php';

$userId = $_SESSION['user_id'];
$proId = $_GET['pro_id'] ?? 0;

// Store cart data in session if passed
if (isset($_GET['cart'])) {
	$cartJson = urldecode($_GET['cart']);
	$_SESSION['cart_data'] = json_decode($cartJson, true);
}

$userQuery = mysqli_query($con, "SELECT * FROM users WHERE id = $userId");
$userData = mysqli_fetch_assoc($userQuery);
?>

<style>
	.form-control { color: #000 !important; font-weight: bold; }
	label { color: #000 !important; }
</style>

<section class="ftco-section">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-10 ftco-animate">
				<form id="addressForm" class="billing-form">
					<h3 class="mb-4 billing-heading">Billing Details</h3>
					<div class="row align-items-end">
						<div class="col-md-12">
							<div class="form-group">
								<label for="fname">First Name</label>
								<input type="text" name="fname" id="fname" class="form-control"
									value="<?= htmlspecialchars($userData['name'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label for="address">Street Address</label>
								<input type="text" name="address" id="address" class="form-control"
									value="<?= htmlspecialchars($userData['address'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="city">Town / City</label>
								<input type="text" name="city" id="city" class="form-control"
									value="<?= htmlspecialchars($userData['city'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="state">State</label>
								<input type="text" name="state" id="state" class="form-control"
									value="<?= htmlspecialchars($userData['state'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="pincode">Postcode / ZIP *</label>
								<input type="number" name="pincode" id="pincode" class="form-control"
									value="<?= htmlspecialchars($userData['pincode'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="phoneno">Phone</label>
								<input type="number" name="phoneno" id="phoneno" class="form-control"
									value="<?= htmlspecialchars($userData['phoneno'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="email">Email Address</label>
								<input type="email" name="email" id="email" class="form-control"
									value="<?= htmlspecialchars($userData['email'] ?? '', ENT_QUOTES) ?>">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group mt-3">
								<button type="submit" id="order" class="btn btn-primary py-3 px-5">Proceed To an Order</button>
							</div>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
	$("#addressForm").on("submit", function (e) {
		e.preventDefault();

		var data = {
			userId: <?= (int)$userId ?>,
			proId: <?= (int)$proId ?>,
			fname: $("#fname").val().trim(),
			address: $("#address").val().trim(),
			city: $("#city").val().trim(),
			state: $("#state").val().trim(),
			pincode: $("#pincode").val().trim(),
			phoneno: $("#phoneno").val().trim(),
			email: $("#email").val().trim()
		};

		// Fixed validation logic
		for (const key in data) {
			if (String(data[key]).trim() === "") {
				swal("Oops!", "Fill all fields!", "warning");
				return;
			}
		}

		// AJAX form submission
		$.post("/theshoesbox/processes/address-process.php", data, function (res) {
			if (res.trim() === "Address Register Successfully.") {
				swal({ title: "Success", text: res, type: "success" }, function () {
					if (data.proId > 0) {
						window.location.href = "/theshoesbox/pages/checkout.php?pro_id=" + data.proId;
					} else {
						window.location.href = "/theshoesbox/pages/checkout.php";
					}
				});
			} else {
				swal("Oops!", res, "error");
			}
		});
	});
</script>
