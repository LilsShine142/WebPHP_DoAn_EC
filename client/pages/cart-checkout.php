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
                <span class="mtext-110 cl2 address">
                    address..
                </span>
                <!-- tag Default and btn Change -->
                 
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
                            <label class="m-b-0" for="cod">Thanh toán khi nhận hàng</label>
                        </div>
                        <div class="flex-w flex-t p-l-20" style="display: flex;
    align-items: center;">
                            <input class="m-r-10" type="radio" id="momo" name="payment" value="momo">
                            <label class="m-b-0" for="cod">Thanh toán Momo</label>
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

<script>
    $(document).ready(function() {
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            const user_name = userObject.full_name;
            $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/users/addresses?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    if (response.success === true) {
                        // if response.data[i].is_default === 1 then display that address
                        response.data.forEach(address => {
                            if (parseInt(address.is_default) === 1) {
                                $(".address").text(`${user_name}, ${address.phone_number}, ${address.street}, ${address.apartment_number}, ${address.ward}, ${address.district}, ${address.city_province}`);
                            }
                        });
                    }
                },
                error: function() {
                    console.log("Có lỗi xảy ra.");
                }
            });
        } else {
            console.log("Không tìm thấy dữ liệu user trong localStorage.");
        }
        // session storage
        const selected_products = JSON.parse(sessionStorage.getItem("selected_products"));
        // hiển thị lên giao diện
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
