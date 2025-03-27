<div class="m-b-50 m-t-50">
    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
        <h4 class="mtext-109 cl2 p-b-40" style="font-size: 24px;">
            Checkout
        </h4>

        <div class="flex-w flex-t bor12 p-b-30">
            <div class="size-208">
                <span class="stext-110 cl2">
                    Delivery Address
                </span>
            </div>

            <div class="size-209">
                <span id="" class="mtext-110 cl2 address-out">
                    address..
                </span>
                <span class="default-tag" style="background-color: #ffcc00; color: #fff; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px; display: none;">
                    Default
                </span>
                <button class="change-address" style="margin-left: 10px; padding: 5px 10px; background-color: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer;">
                    Change
                </button>
            </div>
        </div>

        <div class="flex-w flex-t bor12 p-t-15 p-b-30">

            <div class="wrap-table-shopping-cart size-212">
                <table class="table-shopping-cart">
                    <tr class="table_head">
                        <th class="column-1 column-1-tiny">Product</th>
                        <th class="column-2">Name</th>
                        <th class="column-3">Variant</th>
                        <th class="column-4">Price</th>
                        <th class="column-5">Quantity</th>
                        <th class="column-6">Total</th>
                    </tr>
                </table>
            </div>

            <div class="flex-w flex-t p-t-80">
                <div class="">
                    <span class="mtext-101 cl2">
                        Phương thức thanh toán:
                    </span>
                </div>

                <div class="p-l-40">
                    <div type="checkbox" class="flex-w flex-t">
                        <div class="flex-w flex-t p-l-20" style="display: flex;
    align-items: center;">
                            <input class="m-r-10" type="radio" id="cod" name="payment" value="cod">
                            <label class="m-b-0" for="cod">Payment on Receipt</label>
                        </div>
                        <div class="flex-w flex-t p-l-20" style="display: flex;
    align-items: center;">
                            <input class="m-r-10" type="radio" id="momo" name="payment" value="momo">
                            <label class="m-b-0" for="cod">Momo Payment</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex-w flex-t m-t-40" style="justify-content: center;">
            <div class="size-203">
                <span class="mtext-101 cl2">
                    Total:
                </span>
            </div>

            <div class="size-208 p-t-1">
                <span class="mtext-110 cl2 total">
                    
                </span>
            </div>
            <button class="checkout-button flex-c-m stext-101 cl0 bg3 bor7 hov-btn3 p-lr-15 p-tb-8 trans-04 pointer">
                Checkout
            </button>
        </div>
    </div>
</div>

<!-- Modal chọn địa chỉ -->
<div id="addressModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 class="m-b-20">Delivery address</h2>
        <div id="addressList">
            <!-- Danh sách địa chỉ sẽ được đổ vào đây -->
             
        </div>
        <div class="buttons">
            <!-- btn cancel -->
            <button class="close-btn">Cancel</button>
            <!-- btn confirm -->
            <button id="confirmAddress">Confirm</button>
        </div>
    </div>
</div>

<!-- CSS cho modal -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 8px;
        width: 50%;
        text-align: center;
        position: relative;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
    }
    #confirmAddress {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }
    .close-btn {
        padding: 10px 20px;
        background-color:rgb(255, 219, 222);
        color: #333;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }
    .close-btn:hover {
        background-color:rgb(200, 101, 101);
        color: #fff;
    }
    .buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }
</style>


<script>
    $(document).ready(function () {
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            const user_name = userObject.full_name;
            let addressOutId = null;
            $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/users/addresses?user_id=${user_id}`,
                type: "GET",
                success: function (response) {
                    if (response.success === true) {
                        response.data.forEach(address => {
                            if (parseInt(address.is_default) === 1) {
                                $(".address-out").text(`${user_name}, ${address.phone_number}, ${address.name}, ${address.apartment_number} ${address.street}, ${address.ward}, ${address.district}, ${address.city_province}`);
                                $(".default-tag").show();
                                addressOutId = address.id;
                            }
                        });
                        // Hiển thị modal khi nhấn nút "Change address"
                        const modal = $("#addressModal");
                        const addressList = $("#addressList");

                        $(".change-address").on("click", function () {
                            modal.show();
                            addressList.empty(); // Xóa danh sách cũ

                            response.data.forEach((address, index) => {
                                let isDefault = parseInt(address.is_default) === 1;
                                let defaultTag = isDefault 
                                    ? `<span style="background-color: #ffcc00; color: #fff; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px;">
                                        Default
                                    </span>` 
                                    : "";
                                addressList.append(`
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px;">
                                        <input type="radio" id="${address.id}" name="selectedAddress" value="${address.id}" ${address.id == addressOutId ? "checked" : ""}>
                                        <label class="address-in" for="${address.id}" style="cursor: pointer; margin-bottom: 0;">
                                            ${user_name}, ${address.phone_number}, ${address.apartment_number} ${address.street}, ${address.ward}, ${address.district}, ${address.city_province}
                                        </label>
                                        ${defaultTag}
                                        <span style="background-color:rgb(231, 227, 214); color: #333; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px;">
                                            ${address.name}
                                        </span>
                                        <button class="edit-address" style="background-color: #007bff; color: #fff; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px; cursor: pointer;">
                                            Update
                                        </button>
                                    </div>
                                `);
                            });
                        });
                        // ckeck radio when click on address-in 
                        $(document).on("click", ".address-in", function () {
                            $(this).prev("input[type='radio']").prop("checked", true);
                        });

                        // address-out = address-in khi nhấn nút "Confirm"
                        $("#confirmAddress").on("click", function () {
                            const selectedAddressId = $("input[name='selectedAddress']:checked").val();
                            const selectedAddress = response.data.find(address => address.id == selectedAddressId);
                            $(".address-out").text(`${user_name}, ${selectedAddress.phone_number}, ${selectedAddress.name}, ${selectedAddress.apartment_number} ${selectedAddress.street}, ${selectedAddress.ward}, ${selectedAddress.district}, ${selectedAddress.city_province}`);
                            addressOutId = selectedAddressId;
                            // don't show default tag if selectedAddress.is_default = 0
                            if (parseInt(selectedAddress.is_default) === 1) {
                                $(".default-tag").show();
                            } else {
                                $(".default-tag").hide();
                            }
                            modal.hide();
                        });

                        // Đóng modal khi nhấn nút X
                        $(".close").on("click", function () {
                            modal.hide();
                        });

                        $(".close-btn").on("click", function () {
                            modal.hide();
                        });

                        // Ẩn modal khi click ra ngoài
                        $(window).on("click", function (event) {
                            if ($(event.target).is(modal)) {
                                modal.hide();
                            }
                        });
                    }
                },
                error: function () {
                    console.log("Có lỗi xảy ra.");
                }
            });
        } else {
            console.log("Không tìm thấy dữ liệu user trong localStorage.");
        }

        // Xử lý sessionStorage và hiển thị giỏ hàng
        const selected_products = JSON.parse(sessionStorage.getItem("selected_products"));
        if (selected_products.length > 0) {
            let productHTML = "";
            let total = 0;

            selected_products.forEach(product => {
                let productTotal = product.price * product.quantity;
                total += productTotal;

                productHTML += `
                    <tr class="table_row">
                        <td class="column-1">
                            <div class="how-itemcart1">
                                <img src="${product.image}" alt="IMG">
                            </div>
                        </td>
                        <td class="column-2">${product.name}</td>
                        <td class="column-3">${product.variant}</td>
                        <td class="column-4">${formatCurrency(1000 * product.price)}</td>
                        <td class="column-5">${product.quantity}</td>
                        <td class="column-6">${formatCurrency(1000 * productTotal)}</td>
                    </tr>
                `;
            });

            $(".table-shopping-cart").append(productHTML);
            $(".total").text(formatCurrency(1000 * total));
        }
    });
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
    }
    $(".checkout-button").click(function() {
        sessionStorage.removeItem("selected_products");
    });
</script>
