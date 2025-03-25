<?php
// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$product_id) {
    echo "Không tìm thấy sản phẩm!";
    exit;
}
?>

<link rel="stylesheet" href="../css/product-detail.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="js-modal1 p-t-60 p-b-20 show-modal1">
    <div class="overlay-modal1 js-hide-modal1"></div>
    <div class="container">
        <div class="bg0 p-t-60 p-b-30 p-lr-15-lg how-pos3-parent">
            <div class="row d-flex">
                <div class="col-md-7 d-flex">
                    <div class="col-md-2"></div>
                    <div id="thumbnail-list" class="product-thumbnails"></div>
                    <div class="col-md-10 p-b-30">
                        <div class="wrap-pic-w pos-relative">
                            <img id="product-image" src="" alt="IMG-PRODUCT">
                            <a id="product-image-link" class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04">
                                <i class="fa fa-expand"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 product-infor">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 id="product-name" class="mtext-105 cl2 js-name-detail p-b-14">Loading...</h4>
                        <span id="product-price" class="mtext-106 cl2">...</span>
                        <p id="product-description" class="stext-102 cl3 p-t-23">Loading...</p>

                        <div class="p-t-33">
                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-204 flex-w flex-m respon6-next" style="width: 100%;">
                                    <span>Quantity</span>
                                    <div class="wrap-num-product">
                                        <button class="btn-num-product-down">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input class="num-product" type="number" name="num-product" value="1" min="1">
                                        <button class="btn-num-product-up">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <span id="stock-quantity">... products available</span>
                                    <button class="cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail addcart">
                                        Add to cart
                                    </button>
                                    <button class="cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail buynow">
                                        Buy now
                                    </button>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Specifications -->
            <div class="variation-details bg-light p-3 rounded w-100 mt-4" style="padding-left: 8rem !important;">
                <h5 class="mb-3">Technical Specifications</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Size:</strong> <span id="watch-size"></span> mm</p>
                        <p><strong>Color:</strong> <span id="watch-color"></span></p>
                        <p><strong>Display:</strong> <span id="display-type"></span> (<span id="display-size"></span> inch)</p>
                        <p><strong>Resolution:</strong> <span id="resolution"></span> px</p>
                        <p><strong>RAM/ROM:</strong> <span id="ram-rom"></span></p>
                        <p><strong>Operating System:</strong> <span id="os-name"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Connectivity:</strong> <span id="connectivity"></span></p>
                        <p><strong>Battery:</strong> <span id="battery-life"></span> mAh</p>
                        <p><strong>Water Resistance:</strong> <span id="water-resistance"></span></p>
                        <p><strong>Sensors:</strong> <span id="sensor"></span></p>
                        <p><strong>Case Material:</strong> <span id="case-material"></span></p>
                        <p><strong>Band:</strong> <span id="band-material"></span> (<span id="band-size"></span> mm, <span id="band-color"></span>)</p>
                        <p><strong>Weight:</strong> <span id="weight"></span> g</p>
                        <p><strong>Release Date:</strong> <span id="release-date"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let productId = <?php echo json_encode($product_id); ?>;
        let maxStock = 1;

        // Fetch product details
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/products/${productId}`,
            type: "GET",
            success: function (response) {
                if (response.success && response.data) {
                    let product = response.data;
                    $("#product-name").text(product.name);
                    $("#product-description").text(product.description);
                }
            },
            error: function () {
                console.error("Error loading product details.");
            }
        });

        // Fetch product variations
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/products/variations?product_id=${productId}`,
            type: "GET",
            success: function (response) {
                if (response.success && response.data.length > 0) {
                    let variations = response.data;
                    let firstVariation = variations[0];
                    maxStock = firstVariation.stock_quantity;

                    // Update product details with first variation
                    changeMainImage(firstVariation, null);

                    // Generate thumbnails
                    let thumbnailsHtml = "";
                    variations.forEach((variation, index) => {
                        let activeClass = index === 0 ? "active" : "";
                        let thumbPath = `../backend/uploads/products/${variation.image_name}`;

                        thumbnailsHtml += `<img src="${thumbPath}" class="thumbnail-img ${activeClass}" 
                        data-variation-id="${variation.id}"
                        onclick='changeMainImage(${JSON.stringify(variation)}, this)'>`;
                    });

                    $("#thumbnail-list").html(thumbnailsHtml);
                    updateQuantityControls();
                }
            },
            error: function () {
                console.error("Error loading product variations.");
            }
        });

        $(".addcart").on("click", async function () {
            let userData = localStorage.getItem("user");
            
            let userObject = JSON.parse(userData);
            let userId = userObject?.id;  // Dùng optional chaining để tránh lỗi nếu userObject null
            let productVariationId = $(".product-thumbnails img.active").data("variation-id") || 46;
            let newQuantity = parseInt($(".num-product").val()) || 1;
            let existingQuantity = parseInt(await getQuantityOfProductInCartByUserIdAndVariationId(userId, productVariationId));
            let maxQuantity = parseInt($(".num-product").attr("max"));
            if ((existingQuantity + newQuantity) > maxQuantity) {
                updateItemToCart(userId, productVariationId, maxQuantity);
                return;
            }
            addNewItemToCart(userId, productVariationId, newQuantity);
        });
    });

    async function getQuantityOfProductInCartByUserIdAndVariationId(userId, productVariationId) {
        if (!userId || !productVariationId) {
            return 0;
        }

        try {
            let response = await $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${userId}&product_variation_id=${productVariationId}`,
                type: "GET"
            });

            if (response.success && response.data.length > 0) {
                return response.data[0].quantity;  // Nếu data là mảng, lấy phần tử đầu tiên
            } else {
                return 0;
            }
        } catch (error) {
            console.error("Error fetching cart data.", error);
            return 0;
        }
    }

    function addNewItemToCart(userId, productVariationId, quantity) {
        let requestBody = {
            user_id: userId,
            product_variation_id: productVariationId,
            quantity: quantity
        };

        $.ajax({
            url: "http://localhost:81/WebPHP_DoAn_EC/api/carts",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(requestBody),
            success: function (response) {
                if (response.success) {
                    updateCartCount();
                } else {
                    alert("Không thể thêm vào giỏ hàng. Vui lòng thử lại.");
                }
            },
            error: function () {
                alert("Lỗi kết nối, vui lòng thử lại.");
            }
        });
    }

    function updateItemToCart(userId, productVariationId, quantity){
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${userId}&product_variation_id=${productVariationId}`,
            type: "PUT",
            contentType: "application/json",
            data: JSON.stringify({ quantity: quantity }),
            success: function (response) {
                if (response.success) {
                    console.log(response.message);
                } else {
                    console.error("Cập nhật giỏ hàng thất bại.");
                }
            },
            error: function () {
                console.error("Lỗi khi gửi yêu cầu cập nhật giỏ hàng.");
            }
        });
    }

    function updateCartCount() {
        const userData = localStorage.getItem("user");

        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;

            $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    const cartCount = response.length > 0 ? response.length : 0;
                    $(".icon-header-noti").attr("data-notify", cartCount);
                },
                error: function() {
                    $(".icon-header-noti").attr("data-notify", 0);
                }
            });
        }
    }

    function changeMainImage(variation, element) {
        $("#product-image").attr("src", `../backend/uploads/products/${variation.image_name}`);
        $("#product-image-link").attr("href", `../backend/uploads/products/${variation.image_name}`);
        $("#product-price").text(new Intl.NumberFormat("vi-VN").format(variation.price_cents) + " VND");
        $("#stock-quantity").text(`${variation.stock_quantity} products available`);

        maxStock = variation.stock_quantity;
        $(".num-product").val(1).attr("max", maxStock);

        // Update specifications
        $("#watch-size").text(variation.watch_size_mm);
        $("#watch-color").text(variation.watch_color);
        $("#display-type").text(variation.display_type);
        $("#display-size").text(variation.display_size_mm);
        $("#resolution").text(`${variation.resolution_w_px} x ${variation.resolution_h_px}`);
        $("#ram-rom").text(`${variation.ram_bytes / 1024} GB / ${variation.rom_bytes / 1024} GB`);
        $("#connectivity").text(variation.connectivity);
        $("#battery-life").text(variation.battery_life_mah);
        $("#water-resistance").text(`${variation.water_resistance_value} ${variation.water_resistance_unit}`);
        $("#sensor").text(variation.sensor);
        $("#case-material").text(variation.case_material);
        $("#band-material").text(variation.band_material);
        $("#band-size").text(variation.band_size_mm);
        $("#band-color").text(variation.band_color);
        $("#weight").text(variation.weight_milligrams / 1000);
        $("#release-date").text(new Date(variation.release_date).toLocaleDateString());

        fetchOSName(variation.os_id, function (osName) {
            $("#os-name").text(osName);
        });

        if (element) {
            $(".product-thumbnails img").removeClass("active");
            $(element).addClass("active");
        }
    }

    function fetchOSName(osId, callback) {
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/products/os/${osId}`,
            type: "GET",
            success: function (response) {
                if (response.success && response.data) {
                    callback(response.data.name);
                } else {
                    callback("Unknown OS");
                }
            },
            error: function () {
                console.error("Error loading OS name.");
                callback("Unknown OS");
            }
        });
    }

    function updateQuantityControls() {
        $(".btn-num-product-down, .btn-num-product-up").off("click").on("click", function (event) {
            event.preventDefault();
            let input = $(this).closest(".wrap-num-product").find(".num-product");
            let currentValue = parseInt(input.val()) || 1;

            if ($(this).hasClass("btn-num-product-down")) {
                input.val(Math.max(currentValue - 1, 1));
            } else {
                input.val(Math.min(currentValue + 1, maxStock));
            }
        });

        $(".num-product").off("change blur").on("change blur", function () {
            let value = parseInt($(this).val()) || 1;
            $(this).val(Math.max(1, Math.min(value, maxStock)));
        });
    }
</script>