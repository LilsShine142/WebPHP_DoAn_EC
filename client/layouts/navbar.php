<header>
	<!-- Header desktop -->
	<div class="container-menu-desktop">
		<!-- Topbar -->
		<div class="top-bar">
			<div class="content-topbar flex-sb-m h-full container">
				<div class="left-top-bar">
					Garmin - Engineered on the inside for life on the outside.
				</div>

				<div class="right-top-bar flex-w h-full">
					<a href="#" class="flex-c-m trans-04 p-lr-25 account-menu">
						Account
					</a>
					<div class="dropdown-account">
						<a class="login-tab" href="./pages/login.php">Login</a>
						<a href="?content=pages/profile.php">Profile</a>
						<a href="?content=pages/user-order.php">Order</a>
						<a id="logout-btn" href="#">Logout</a>
					</div>
				</div>
			</div>
		</div>

		<div class="wrap-menu-desktop">
			<nav class="limiter-menu-desktop container">

				<!-- Logo desktop -->
				<a href="index.php" class="logo">
					<img src="images/icons/logo-garmin.png" alt="IMG-LOGO">
				</a>

				<!-- Menu desktop -->
				<div class="menu-desktop">
					<ul class="main-menu">
						<!-- class="active-menu" -->
						<li>
							<a href="index.php">Home</a>
						</li>

						<li>
							<a href="?content=pages/shop.php">Shop</a>
						</li>

						<li class="label1" data-label1="hot">
							<a href="?content=pages/smartwatch.php">Smartwatch</a>
						</li>

						<li>
							<a href="?content=pages/cables.php">Cables</a>
						</li>

						<li>
							<a href="?content=pages/chargers.php">Chargers</a>
						</li>

						<li>
							<a href="?content=pages/bands.php">Bands</a>
						</li>
					</ul>
				</div>

				<!-- Icon header -->
				<div class="wrap-icon-header flex-w flex-r-m">
					<!-- <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 nav-search-container">
						<div class="nav-search-input">
							<input class="nav-search-input-input" type="text" name="search" placeholder="Search...">
						</div>
						<i class="zmdi zmdi-search search-icon"></i>
					</div> -->

					<a href="?content=pages/shopping-cart.php">
						<div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="0">
							<i class="zmdi zmdi-shopping-cart"></i>
						</div>
					</a>
				</div>
			</nav>
		</div>
	</div>
</header>
<style>
	.nav-search-container{
		position: relative;
		display: flex;
		align-items: center;
	}
	.nav-search-input-input{
		border-radius: 20px;
		border: 1px solid #ccc;
		/* chỉnh cỡ chữ placeholder */
		font-size: 14px;
		padding: 10px 20px;
		display: none;
	}
	.search-icon{
		position: absolute;
		right: 20px;
		cursor: pointer;
	}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	// $(".search-icon").click(function() {
	// 	$(".nav-search-input-input").toggle(); 
	// 	$(".nav-search-input-input").focus(); 
	// });

	// // sự kiện nhập từ khóa và nhấn enter
	// $(".nav-search-input-input").on("keypress", function(event) {
	// 	if (event.which === 13) { // Kiểm tra nếu phím Enter được nhấn
	// 		event.preventDefault(); // Ngăn chặn hành động mặc định của phím Enter
	// 		const keyword = $(this).val(); // Lấy giá trị từ ô tìm kiếm
	// 		if (keyword) {
				
	// 		}
	// 	}
	// });

	$(document).ready(function() {
		// Lấy dữ liệu từ localStorage
		const userData = localStorage.getItem("user");

		// Kiểm tra nếu dữ liệu tồn tại
		if (userData) {
			// ẩn class login-tab
			$(".login-tab").hide();
			const userObject = JSON.parse(userData); // Chuyển từ JSON string thành object
			const user_id = userObject.id; // Lấy id
			// call ajax http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=1 to take the response.length
			$.ajax({
				url: `${BASE_API_URL}/api/carts?user_id=${user_id}`,
				type: "GET",
				success: function(response) {
					if (response.length > 0) {
						$(".icon-header-noti").attr("data-notify", response.length);
					}
				},
				error: function() {
					console.log("Có lỗi xảy ra.");
				}
			});

		} else {
			console.log("Không tìm thấy dữ liệu user trong localStorage.");
		}
	});
</script>