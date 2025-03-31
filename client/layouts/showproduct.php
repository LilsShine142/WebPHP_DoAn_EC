<!-- Slider -->
<?php include("slider.php") ?>
<!-- Banner -->
<?php include("small-banner.php") ?>
<section class="bg0 p-t-23 p-b-140">
	<div class="container">
		<div class="p-b-10">
			<h3 class="ltext-103 cl5">
				Product Overview
			</h3>
		</div>

		<div class="flex-w flex-sb-m p-b-52">
			<div class="flex-w flex-l-m filter-tope-group m-tb-10">
				<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
					All Products
				</button>

				<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter="smartwatch">
					Smartwatch
				</button>

				<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter="cable">
					Cable
				</button>

				<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter="charger">
					Charger
				</button>

				<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter="band">
					Band
				</button>

			</div>

			<div class="flex-w flex-c-m m-tb-10">
				<div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
					<i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
					<i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
					Filter
				</div>

				<div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
					<i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
					<i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
					Search
				</div>
			</div>

			<!-- Search product -->
			<div class="dis-none panel-search w-full p-t-10 p-b-15">
				<div class="bor8 dis-flex p-l-15">
					<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
						<i class="zmdi zmdi-search"></i>
					</button>

					<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Search">
				</div>
			</div>

			<!-- Filter -->
			<div class="dis-none panel-filter w-full p-t-10">
				<div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
					<div class="filter-col1 p-r-15 p-b-27">
						<div class="mtext-102 cl2 p-b-15">
							Sort By
						</div>

						<ul>
							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									Price: Low to High
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									Price: High to Low
								</a>
							</li>
						</ul>
					</div>

					<div class="filter-col2 p-r-15 p-b-27">
						<div class="mtext-102 cl2 p-b-15">
							Price
						</div>

						<ul>
							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04 filter-link-active">
									All
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									$0.00 - $50.00
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									$50.00 - $100.00
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									$100.00 - $150.00
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									$150.00 - $200.00
								</a>
							</li>

							<li class="p-b-6">
								<a href="#" class="filter-link stext-106 trans-04">
									$200.00+
								</a>
							</li>
						</ul>
					</div>

					<div class="filter-col3 p-r-15 p-b-27">
						<div class="mtext-102 cl2 p-b-15">
							Color
						</div>

						<ul>
							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #222;">
									<i class="zmdi zmdi-circle"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04">
									Black
								</a>
							</li>

							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #4272d7;">
									<i class="zmdi zmdi-circle"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04 filter-link-active">
									Blue
								</a>
							</li>

							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #b3b3b3;">
									<i class="zmdi zmdi-circle"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04">
									Grey
								</a>
							</li>

							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #00ad5f;">
									<i class="zmdi zmdi-circle"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04">
									Green
								</a>
							</li>

							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #fa4251;">
									<i class="zmdi zmdi-circle"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04">
									Red
								</a>
							</li>

							<li class="p-b-6">
								<span class="fs-15 lh-12 m-r-6" style="color: #aaa;">
									<i class="zmdi zmdi-circle-o"></i>
								</span>

								<a href="#" class="filter-link stext-106 trans-04">
									White
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="row isotope-grid" id="product-list">

		</div>

		<div id="pagination"></div>
	</div>
</section>
<style>
	#product-list {
		height: 100% !important;
	}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {
		let currentPage = 1;
		let productsPerPage = 16;
		let totalProducts = 0;
		let allProducts = []; // Lưu trữ toàn bộ sản phẩm
		let categoriesMap = {}; // Lưu trữ danh mục sản phẩm
		let activeCategory = "*"; // Lưu danh mục đang được chọn

		/** 📌 Gọi API lấy danh mục sản phẩm */
		function fetchCategories() {
			return $.ajax({
				url: `${BASE_API_URL}/api/products/categories`,
				type: "GET"
			}).then(response => {
				if (response.success) {
					response.data.forEach(category => {
						let categoryKey = category.name.replace(/\s+/g, "-").toLowerCase();
						categoriesMap[category.id] = categoryKey;
					});
				}
			});
		}

		/** 📌 Gọi API lấy toàn bộ sản phẩm */
		function fetchAllProducts() {
			return $.ajax({
				url: `${BASE_API_URL}/api/products`,
				type: "GET"
			}).then(response => {
				if (response.success) {
					allProducts = response.data;
					totalProducts = allProducts.length;
				}
			});
		}

		/** 📌 Hiển thị danh sách sản phẩm theo phân trang */
		function loadProducts(page, categoryFilter = "*", searchKeyword = "") {
			let filteredProducts = allProducts;

			// 🔹 Lọc theo danh mục nếu không phải "All Products"
			if (categoryFilter !== "*") {
				filteredProducts = filteredProducts.filter(product => categoriesMap[product.category_id] === categoryFilter);
			}

			// 🔹 Lọc theo từ khóa tìm kiếm
			if (searchKeyword) {
				filteredProducts = filteredProducts.filter(product =>
					product.name.toLowerCase().includes(searchKeyword)
				);
			}

			let start = (page - 1) * productsPerPage;
			let end = start + productsPerPage;
			let productsToShow = filteredProducts.slice(start, end);
			let totalPages = Math.ceil(filteredProducts.length / productsPerPage);
			let productHtml = "";

			let variationPromises = productsToShow.map(product => {
				return $.ajax({
					url: `${BASE_API_URL}/api/products/variations?product_id=${product.id}`,
					type: "GET"
				}).then(variationResponse => ({
					product: product,
					variations: variationResponse.success ? variationResponse.data : []
				}));
			});

			Promise.all(variationPromises).then(results => {
				results.forEach(({
					product,
					variations
				}) => {
					let firstVariation = variations.length > 0 ? variations[0] : null;
					let imageName = firstVariation ? firstVariation.image_name : "default.webp";
					let price = firstVariation ? firstVariation.price_cents : "N/A";
					let categoryName = categoriesMap[product.category_id] || "uncategorized";

					productHtml += `
						<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item ${categoryName}">
							<div class="block2">
								<div class="block2-pic hov-img0">
									<a href="index.php?content=pages/product-detail.php&id=${product.id}" class="js-show-modal1">
										<img src="../backend/uploads/products/${imageName}" alt="${product.name}" onerror="this.onerror=null; this.src='../backend/uploads/products/applewatch_black.webp';">
									</a>
								</div>
								<div class="block2-txt flex-w flex-t p-t-14">
									<div class="block2-txt-child1 flex-col-l">
										<a href="index.php?content=pages/product-detail.php&id=${product.id}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
											${product.name}
										</a>
										<span class="stext-105 cl3">
											${price} VND
										</span>
									</div>
								</div>
							</div>
						</div>`;
				});

				$("#product-list").html(productHtml);
				renderPagination(totalPages, page);
			});
		}

		/** 📌 Hiển thị phân trang */
		function renderPagination(totalPages, currentPage) {
			let paginationHtml = "";
			for (let i = 1; i <= totalPages; i++) {
				paginationHtml += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
			}
			$("#pagination").html(paginationHtml);
		}

		/** 📌 Xử lý sự kiện phân trang */
		$(document).on("click", ".pagination-btn", function() {
			currentPage = $(this).data("page");
			let searchKeyword = $("input[name='search-product']").val().toLowerCase();
			loadProducts(currentPage, activeCategory, searchKeyword);
		});

		/** 📌 Xử lý sự kiện lọc theo danh mục */
		$(".filter-tope-group button").on("click", function() {
			$(".filter-tope-group button").removeClass("how-active1");
			$(this).addClass("how-active1");

			activeCategory = $(this).data("filter").replace(".", ""); // Loại bỏ dấu `.`
			currentPage = 1;
			let searchKeyword = $("input[name='search-product']").val().toLowerCase();
			loadProducts(currentPage, activeCategory, searchKeyword);
		});

		/** 📌 Xử lý sự kiện tìm kiếm */
		$("input[name='search-product']").on("input", function() {
			let searchKeyword = $(this).val().toLowerCase();
			currentPage = 1;
			loadProducts(currentPage, activeCategory, searchKeyword);
		});

		/** 📌 Gọi API danh mục trước, sau đó tải sản phẩm */
		fetchCategories().then(() => {
			fetchAllProducts().then(() => {
				loadProducts(currentPage);
			});
		});
	});
</script>