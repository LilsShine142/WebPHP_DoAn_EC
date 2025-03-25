<!-- Shopping Cart -->
<form class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="m-lr-0-xl">
            <div class="wrap-table-shopping-cart">
                <table class="table-shopping-cart">
                    <tr class="table_head">
                        <th class="column-0"><input type="checkbox" class="select-all"></th>
                        <th class="column-1">Product</th>
                        <th class="column-2">Name</th>
                        <th class="column-3">Variant</th>
                        <th class="column-4">Price</th>
                        <th class="column-5">Quantity</th>
                        <th class="column-6">Total</th>
                        <th class="column-7">Action</th>
                    </tr>
                </table>
            </div>

            <!-- Fixed Purchase Bar -->
            <div class="purchase-bar">
                <div class="purchase-container">
                    <!-- cột Select -->
                    <div class="purchase-column">
                        <input type="checkbox" class="select-all">
                        <label class="select-all-label" style="font-size: 18px;">Select All</label>
                    </div>
                    <!-- cột Total -->
                    <div class="purchase-column">
                        <span class="mtext-106 cl2">Total (0 product):</span>
                        <span class="mtext-106 cl2 total-amount">$ 36.00</span>
                    </div>
                    <!-- cột Purchase -->
                    <div class="purchase-column">
                        <a href="?content=pages/cart-checkout.php">
                            <button class="purchase-button">Purchase</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>



<style>
    .variant-select {
        padding: 5px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        /* test color */
        color: #057262;
    }
    .purchase-bar {
        width: 100%;
        background: #0be7c7;
        padding: 15px;
        text-align: center;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }
    .purchase-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: auto;
    }
    .purchase-column {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .purchase-button {
        background: #057262;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 5px;
    }
    .purchase-button:hover {
        background: rgb(8, 182, 156);
    }
</style>

<script>
    $(document).ready(function () {
        const userData = localStorage.getItem("user");

        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;

            // Gọi API để lấy danh sách sản phẩm trong giỏ hàng
            $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${user_id}`,
                type: "GET",
                success: function (cartResponse) {
                    if (cartResponse.success && cartResponse.length > 0) {
                        // $(".table-shopping-cart").find(".table_row").remove(); // Xóa dữ liệu cũ
                        let totalAmount = 0; // Tổng tiền

                        cartResponse.data.forEach((cartItem) => {
                            // Gọi API để lấy chi tiết sản phẩm
                            $.ajax({
                                url: `http://localhost:81/WebPHP_DoAn_EC/api/products/variations/${cartItem.product_variation_id}`,
                                type: "GET",
                                success: function (productResponse) {
                                    if (productResponse.success) {
                                        const product = productResponse.data;
                                        const productTotal = cartItem.quantity * product.price_cents;
                                        totalAmount += productTotal;

                                        // Thêm sản phẩm vào bảng giỏ hàng
                                        $(".table-shopping-cart").append(`
                                            <tr class="table_row">
                                                <td class="column-0"><input type="checkbox" class="item-checkbox"></td>
                                                <td class="column-1">
                                                    <a href="../backend/uploads/products/${product.image_name}">
                                                       <div class="how-itemcart1">
                                                            <img src="../backend/uploads/products/${product.image_name}" alt="${product.product_id}">
                                                        </div> 
                                                    </a>
                                                </td><td class="column-2" data-product-id="${product.product_id}">Loading...</td>
                                                <td class="column-3">${product.watch_color} - ${product.watch_size_mm}mm</td>
                                                <td class="column-4">${formatCurrency(product.price_cents)}</td>
                                                <td class="column-5">
                                                    <div class="wrap-num-product wrap-num-product-grey flex-w m-l-auto m-r-0">
                                                        <div class="inside-num-product">
                                                            <div class="btn-num-product-down btn-num-product-down-grey cl8 hov-btn3 trans-04 flex-c-m">
                                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                                            </div>
                                                            <input class="mtext-104 cl3 txt-center num-product" type="number"
                                                                value="${cartItem.quantity}" min="1" max="${product.stock_quantity}">
                                                            <div class="btn-num-product-up btn-num-product-up-grey cl8 hov-btn3 trans-04 flex-c-m">
                                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="column-6">${formatCurrency(productTotal)}</td>
                                                <td class="column-7"><button class="delete-item" data-id="${cartItem.product_variation_id}">Delete</button></td>
                                            </tr>
                                        `);
                                        // Gọi hàm để cập nhật tên sản phẩm
                                        getNameOfVariation(product.product_id, `.table-shopping-cart [data-product-id='${product.product_id}']`);

                                        // Cập nhật tổng tiền
                                        $(".total-amount").text(formatCurrency(0));
                                    }
                                },
                                error: function () {
                                    console.log("Lỗi khi lấy thông tin sản phẩm.");
                                }
                            });
                        });
                    }
                },
                error: function () {
                    console.log("Lỗi khi lấy giỏ hàng.");
                }
            });
        } else {
            console.log("Không tìm thấy dữ liệu user trong localStorage.");
        }
        // Khi nhấn vào checkbox "Select All"
        $(".select-all").on("change", function () {
            $(".select-all").prop("checked", this.checked);
            $(".item-checkbox").prop("checked", this.checked);
            updateTotalAmount();
        });

        // Khi nhấn vào nhãn "Select All"
        $(".select-all-label").on("click", function () {
            const selectAllCheckbox = $(".select-all");
            selectAllCheckbox.prop("checked", !selectAllCheckbox.prop("checked")).trigger("change");
        });

        // Khi chọn/bỏ chọn từng sản phẩm
        $(document).on("change", ".item-checkbox", function () {
            const allChecked = $(".item-checkbox").length === $(".item-checkbox:checked").length;
            $(".select-all").prop("checked", allChecked);
            updateTotalAmount();
        });
    });

    // func getNameOfVariation
    function getNameOfVariation(product_id, element) {
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/products/${product_id}`,
            type: "GET",
            success: function (response) {
                if (response.success && response.data) {
                    $(element).text(response.data.name); // Cập nhật trực tiếp vào ô cột column-2
                }
            },
            error: function () {
                console.error("Error loading product details.");
            }
        });
    }

    $(document).on("click", ".delete-item", function (event) {
        event.preventDefault();
        event.stopPropagation();

        const productVariationId = $(this).data("id");
        const userData = localStorage.getItem("user");

        if (!userData) return;

        const userObject = JSON.parse(userData);
        const userId = userObject.id;

        if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?")) return;

        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${userId}&product_variation_id=${productVariationId}`,
            type: "DELETE",
            success: function (response) {
                if (response.success) {
                    $(`.delete-item[data-id='${productVariationId}']`).closest("tr").remove();
                        updateCartCount();
                }
            }
        });
    });
    
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

    $(document).on("input", ".num-product", function () {
        const $input = $(this);
        const newQuantity = parseInt($input.val());
        const productVariationId = $input.closest("tr").find(".delete-item").data("id");

        if (newQuantity <= 0 || isNaN(newQuantity)) {
            $input.val(1);
            updateCartItem(productVariationId, 1, $input);
            return;
        }
        // nếu newQuantity > max thì newQuantity = max
        const maxValue = parseInt($input.attr("max")) || Infinity;
        if (newQuantity > maxValue) {
            $input.val(maxValue);        
            updateCartItem(productVariationId, maxValue, $input);
            return;
        }
        updateCartItem(productVariationId, newQuantity, $input);
    });

    // Hàm gọi API cập nhật số lượng sản phẩm
    function updateCartItem(productVariationId, quantity, $input) {
        const userData = localStorage.getItem("user");

        if (!userData) return;

        const userObject = JSON.parse(userData);
        const userId = userObject.id;

        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=${userId}&product_variation_id=${productVariationId}`,
            type: "PUT",
            contentType: "application/json",
            data: JSON.stringify({ quantity: quantity }),
            success: function (response) {
                if (response.success) {
                    updateCartUI($input, quantity);
                } else {
                    console.error("Cập nhật giỏ hàng thất bại.");
                }
            },
            error: function () {
                console.error("Lỗi khi gửi yêu cầu cập nhật giỏ hàng.");
            }
        });
    }

    // Hàm cập nhật giao diện giỏ hàng
    function updateCartUI($input, quantity) {
        const $row = $input.closest("tr");
        const pricePerItem = 1000*parseFloat($row.find(".column-4").text().replace(/[^\d.]/g, ""));
        const totalPrice = quantity * pricePerItem;

        $row.find(".column-6").text(formatCurrency(totalPrice));

        updateTotalAmount();
    }

    // Cập nhật tổng tiền của giỏ hàng
    function updateTotalAmount() {
        let totalAmount = 0;
        let totalProducts = 0;

        $(".table-shopping-cart .table_row").each(function () {
            const $checkbox = $(this).find(".item-checkbox");
            
            if ($checkbox.prop("checked")) { // Kiểm tra checkbox có được tick không
                const itemTotal = parseFloat($(this).find(".column-6").text().replace(/[^\d.]/g, ""));
                totalAmount += itemTotal;
                totalProducts++;
            }
        });

        $(".total-amount").text(formatCurrency(totalAmount*1000));
        $(".purchase-container .mtext-106").first().text(`Total (${totalProducts} product):`);
    }

    // Hàm định dạng tiền VND
    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
    }

    $(document).on("click", ".btn-num-product-up", function () {
        const $input = $(this).closest(".wrap-num-product").find(".num-product");
        let currentValue = parseInt($input.val());
        const maxValue = parseInt($input.attr("max")) || Infinity; // Giới hạn tối đa

        if (currentValue < maxValue) {
            const newQuantity = currentValue + 1;
            $input.val(newQuantity);

            // Lấy productVariationId
            const productVariationId = $input.closest("tr").find(".delete-item").data("id");

            // Gọi hàm cập nhật giỏ hàng
            updateCartItem(productVariationId, newQuantity, $input);
        }
    });

    $(document).on("click", ".btn-num-product-down", function () {
        const $input = $(this).closest(".wrap-num-product").find(".num-product");
        let currentValue = parseInt($input.val());

        if (currentValue > 1) {
            const newQuantity = currentValue - 1;
            $input.val(newQuantity);

            // Lấy productVariationId
            const productVariationId = $input.closest("tr").find(".delete-item").data("id");

            // Gọi hàm cập nhật giỏ hàng
            updateCartItem(productVariationId, newQuantity, $input);
        }
    });
</script>