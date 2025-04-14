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
                <span id="" class="mtext-110 cl2 address-out"></span>
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
                    <div class="flex-w flex-t">
                        <div class="flex-w flex-t p-l-20" style="display: flex;
    align-items: center;">
                            <input class="m-r-10" type="radio" id="cod" name="payment" value="COD">
                            <label class="m-b-0" for="cod">COD</label>
                        </div>
                        <div class="flex-w flex-t p-l-20" style="display: flex;
    align-items: center;">
                            <input class="m-r-10" type="radio" id="momo" name="payment" value="Momo">
                            <label class="m-b-0" for="momo">Momo Payment</label>
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
            <button name="momo" class="checkout-button flex-c-m stext-101 cl0 bg3 bor7 hov-btn3 p-lr-15 p-tb-8 trans-04 pointer">
                Checkout
            </button>
        </div>
    </div>
</div>


<!-- Modal chọn địa chỉ -->
<div id="addressModal" class="modal">
    <div class="modal-content">
        <h2 class="m-b-20">Delivery address</h2>
        <div id="addressList">
            <!-- Danh sách địa chỉ sẽ được đổ vào đây -->

        </div>
        <div class="buttons">
            <!-- btn cancel -->
            <button class="closeSwitchAddressModal">Cancel</button>
            <!-- btn confirm -->
            <button id="confirmSwitchAddress">Confirm</button>
        </div>
    </div>
</div>

<!-- Modal Update Address -->
<div id="updateAddressModal" class="modal">
    <div class="modal-content update-address-modal-content">
        <h2 class="m-b-20">Update Address</h2>
        <div id="addressUpdate">
            <div class="input-row">
                <div class="input-group">
                    <label for="fullName">Name</label>
                    <input type="text" id="fullName" value="Đào Thanh Tú">
                </div>
                <div class="input-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" value="(+84) 886 766 143">
                </div>
            </div>

            <div class="input-group">
                <label for="prov-dist-ward">Province/City, District, Ward/Commune</label>
                <input id="pdw-address" type="text">
                <div class="prov-dist-ward" style="display: none;">
                    <div class="pdw-container">
                        <div class="switch-active province-btn pdw-active">Provinve</div>
                        <div class="switch-active district-btn">District</div>
                        <div class="switch-active ward-btn">Ward</div>
                    </div>
                    <div class="p-w-r-choices">
                        <div class="choices">
                            <!-- Danh sách tỉnh/thành phố sẽ được đổ vào đây -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="specificAddress">Specific Address</label>
                <input type="text" id="specificAddress" value="Tạp Quá Cô Huế">
            </div>
            <div class="address-type">
                <!-- checkbox Set as default address -->
                <input type="checkbox" id="defaultAddress" name="defaultAddress">
                <label style="margin-bottom: 0;" for="defaultAddress">Set as default address</label>
            </div>
        </div>
        <div class="buttons">
            <button class="closeUpdateAddressModal">Back</button>
            <button id="confirmChangeAddress">Confirm</button>
        </div>
    </div>
</div>


<!-- CSS cho modal -->
<style>
    .choice {
        cursor: pointer;
        padding: 0px 10px;
        text-align: start;
    }

    .choice span {
        color: rgb(76 48 48);
    }

    .choice:hover {
        background-color: #f9f9f9;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 8px;
        width: 60%;
        text-align: center;
        position: relative;
    }

    .input-group {
        flex: 1;
        /* Chia đều không gian */
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .input-group select {
        width: 100%;
    }

    .address-type {
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }



    #confirmSwitchAddress,
    #confirmChangeAddress {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }

    .closeUpdateAddressModal,
    .closeSwitchAddressModal {
        padding: 10px 20px;
        background-color: rgb(255, 219, 222);
        color: #333;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }

    .closeUpdateAddressModal:hover,
    .closeSwitchAddressModal:hover {
        background-color: rgb(200, 101, 101);
        color: #fff;
    }

    .buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }

    .input-row {
        display: flex;
        gap: 20px;
        /* Khoảng cách giữa các ô nhập */
    }

    .update-address-modal-content {
        margin: 4% auto;
        width: 36%;
    }

    .prov-dist-ward {
        display: flex;
        margin-bottom: 20px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        flex-direction: column;
    }

    .pdw-container {
        width: 100%;
        display: flex;
        border-bottom: 1px solid #ccc;
        justify-content: space-between;
    }

    .p-w-r-choices {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .choices {
        width: 100%;
        max-height: 100px;
        /* Giới hạn chiều cao để kích hoạt thanh cuộn */
        overflow-y: auto;
        /* Bật thanh cuộn theo chiều dọc */
    }

    /* Thiết lập kiểu thanh cuộn cho trình duyệt WebKit (Chrome, Edge, Safari) */
    .choices::-webkit-scrollbar {
        width: 8px;
        /* Độ rộng thanh cuộn */
    }

    .choices::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Màu nền của thanh cuộn */
        border-radius: 5px;
    }

    .choices::-webkit-scrollbar-thumb {
        background: #888;
        /* Màu của thanh cuộn */
        border-radius: 5px;
    }

    .choices::-webkit-scrollbar-thumb:hover {
        background: #555;
        /* Màu thanh cuộn khi hover */
    }

    .pdw-container div {
        width: 33.33%;
        padding: 8px 0;
    }

    .pdw-active {
        color: #ff6b6b;
        border-bottom: 3px solid #ff6b6b;
        font-weight: bold;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    let selectedProvince = "";
    let selectedDistrict = "";
    let selectedWard = "";
    let selectedProvinceCode = null;
    let selectedDistrictCode = null;

    // load dự liệu tỉnh huyện xã Việt Nam
    document.addEventListener("DOMContentLoaded", function() {
        const choicesContainer = document.querySelector(".choices");
        const provinceBtn = document.querySelector(".province-btn");
        const districtBtn = document.querySelector(".district-btn");
        const wardBtn = document.querySelector(".ward-btn");
        const pdwAddressInput = document.getElementById("pdw-address");

        function loadProvinces(selectedCode = null) {
            fetch("https://provinces.open-api.vn/api/p/")
                .then(response => response.json())
                .then(data => {
                    renderChoices(data, "province", selectedCode);
                })
                .catch(error => console.error("Lỗi khi lấy danh sách tỉnh:", error));
        }

        function loadDistricts(provinceCode, selectedCode = null) {
            fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    renderChoices(data.districts, "district", selectedCode);
                })
                .catch(error => console.error("Lỗi khi lấy danh sách huyện:", error));
        }

        function loadWards(districtCode, selectedCode = null) {
            fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    renderChoices(data.wards, "ward", selectedCode);
                })
                .catch(error => console.error("Lỗi khi lấy danh sách xã:", error));
        }

        function renderChoices(data, type, selectedCode = null) {
            choicesContainer.innerHTML = "";

            data.forEach(item => {
                const choiceDiv = document.createElement("div");
                choiceDiv.classList.add("choice");

                const spanElement = document.createElement("span");
                spanElement.textContent = item.name;
                choiceDiv.appendChild(spanElement);
                choicesContainer.appendChild(choiceDiv);

                if (selectedCode && item.code === selectedCode) {
                    choiceDiv.classList.add("selected");
                }

                choiceDiv.addEventListener("click", function() {
                    if (type === "province") {
                        selectedProvince = item.name;
                        selectedProvinceCode = item.code;
                        selectedDistrict = "";
                        selectedWard = "";
                        pdwAddressInput.value = selectedProvince;
                        switchTab(districtBtn, () => loadDistricts(selectedProvinceCode));
                    } else if (type === "district") {
                        selectedDistrict = item.name;
                        selectedDistrictCode = item.code;
                        selectedWard = "";
                        pdwAddressInput.value = `${selectedProvince}, ${selectedDistrict}`;
                        switchTab(wardBtn, () => loadWards(selectedDistrictCode));
                    } else if (type === "ward") {
                        selectedWard = item.name;
                        pdwAddressInput.value = `${selectedProvince}, ${selectedDistrict}, ${selectedWard}`;
                        document.querySelector(".prov-dist-ward").style.display = "none";
                    }
                });
            });
        }

        function switchTab(targetBtn, loadFunction) {
            document.querySelectorAll(".switch-active").forEach(btn => btn.classList.remove("pdw-active"));
            targetBtn.classList.add("pdw-active");
            loadFunction();
        }

        provinceBtn.addEventListener("click", function() {
            switchTab(provinceBtn, () => loadProvinces(selectedProvinceCode));
        });

        districtBtn.addEventListener("click", function() {
            if (selectedProvinceCode) {
                switchTab(districtBtn, () => loadDistricts(selectedProvinceCode, selectedDistrictCode));
            } else {
                alert("Vui lòng chọn tỉnh/thành trước!");
            }
        });

        wardBtn.addEventListener("click", function() {
            if (selectedDistrictCode) {
                switchTab(wardBtn, () => loadWards(selectedDistrictCode));
            } else {
                alert("Vui lòng chọn quận/huyện trước!");
            }
        });

        pdwAddressInput.addEventListener("click", function() {
            document.querySelector(".prov-dist-ward").style.display = "block";
        });

        document.addEventListener("click", function(event) {
            if (!event.target.closest("#pdw-address, .prov-dist-ward")) {
                document.querySelector(".prov-dist-ward").style.display = "none";
            }
        });

        function initializeAddressSelection() {
            if (selectedProvince) {
                pdwAddressInput.value = `${selectedProvince}${selectedDistrict ? ", " + selectedDistrict : ""}${selectedWard ? ", " + selectedWard : ""}`;
                loadProvinces(selectedProvinceCode);
                if (selectedDistrict) {
                    loadDistricts(selectedProvinceCode, selectedDistrictCode);
                }
                if (selectedWard) {
                    loadWards(selectedDistrictCode);
                }
            } else {
                loadProvinces();
            }
        }

        initializeAddressSelection();
    });

    // Xử lý sự kiện switch-active cho tỉnh huyện xã btn
    document.addEventListener("DOMContentLoaded", function() {
        const switchButtons = document.querySelectorAll(".switch-active");

        switchButtons.forEach(button => {
            button.addEventListener("click", function() {
                // Xóa class "pdw-active" khỏi tất cả các nút
                switchButtons.forEach(btn => btn.classList.remove("pdw-active"));

                // Thêm class "pdw-active" cho nút được nhấn
                this.classList.add("pdw-active");
            });
        });
    });

    $(document).ready(function() {
        $("#pdw-address").on("click", function(event) {
            $(".prov-dist-ward").show(); // Hiển thị danh sách
            event.stopPropagation(); // Ngăn sự kiện lan ra ngoài
        });

        // Ẩn khi click ra ngoài
        $(document).on("click", function(event) {
            if (!$(event.target).closest("#pdw-address, .prov-dist-ward").length) {
                $(".prov-dist-ward").hide();
            }
        });

        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            let addressOutId = null;
            $.ajax({
                url: `${BASE_API_URL}/api/users/addresses?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    if (response.success === true) {
                        response.data.forEach(address => {
                            if (parseInt(address.is_default) === 1) {
                                $(".address-out").text(`${address.name}, ${address.phone_number}, ${address.apartment_number} ${address.street}, ${address.ward}, ${address.district}, ${address.city_province}`);
                                $(".default-tag").show();
                                addressOutId = address.id;
                            }
                        });
                        // Hiển thị modal khi nhấn nút "Change address"
                        const modal = $("#addressModal");
                        const addressList = $("#addressList");

                        $(".change-address").on("click", function() {
                            modal.show();
                            addressList.empty(); // Xóa danh sách cũ

                            // Sắp xếp địa chỉ, đưa địa chỉ mặc định lên đầu tiên
                            response.data.sort((a, b) => {
                                return parseInt(b.is_default) - parseInt(a.is_default);
                            });

                            response.data.forEach((address, index) => {
                                let isDefault = parseInt(address.is_default) === 1;
                                let defaultTag = isDefault ?
                                    `<span style="background-color: #ffcc00; color: #fff; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px;">
                                        Default
                                    </span>` :
                                    "";
                                addressList.append(`
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px;">
                                        <input type="radio" id="${address.id}" name="selectedAddress" value="${address.id}" ${address.id == addressOutId ? "checked" : ""}>
                                        <label class="address-in" for="${address.id}" style="cursor: pointer; margin-bottom: 0;">
                                            ${address.name}, ${address.phone_number}, ${address.apartment_number} ${address.street}, ${address.ward}, ${address.district}, ${address.city_province}
                                        </label>
                                        ${defaultTag}
                                        <button class="edit-address" style="background-color: #007bff; color: #fff; font-size: 12px; padding: 3px 6px; border-radius: 3px; margin-left: 10px; cursor: pointer;">
                                            Update
                                        </button>
                                    </div>
                                `);
                            });
                        });
                        // ckeck radio when click on address-in 
                        $(document).on("click", ".address-in", function() {
                            $(this).prev("input[type='radio']").prop("checked", true);
                        });

                        // address-out = address-in khi nhấn nút "Confirm"
                        $("#confirmSwitchAddress").on("click", function() {
                            const selectedAddressId = $("input[name='selectedAddress']:checked").val();
                            const selectedAddress = response.data.find(address => address.id == selectedAddressId);
                            $(".address-out").text(`${selectedAddress.name}, ${selectedAddress.phone_number}, ${selectedAddress.name}, ${selectedAddress.apartment_number} ${selectedAddress.street}, ${selectedAddress.ward}, ${selectedAddress.district}, ${selectedAddress.city_province}`);
                            addressOutId = selectedAddressId;
                            // don't show default tag if selectedAddress.is_default = 0
                            if (parseInt(selectedAddress.is_default) === 1) {
                                $(".default-tag").show();
                            } else {
                                $(".default-tag").hide();
                            }
                            modal.hide();
                        });

                        $(".closeSwitchAddressModal").on("click", function() {
                            modal.hide();
                        });

                        // Ẩn modal khi click ra ngoài
                        $(window).on("click", function(event) {
                            if ($(event.target).is(modal)) {
                                modal.hide();
                            }
                        });

                        $(document).on("click", ".edit-address", function() {
                            // lấy thông tin address cần update
                            const selectedAddressId = $(this).siblings("input[name='selectedAddress']").val();
                            const selectedAddress = response.data.find(address => address.id == selectedAddressId);
                            // đóng modal address
                            modal.hide();
                            // cập nhật biến tỉnh, huyện, xã toàn cục
                            selectedProvince = selectedAddress.city_province;
                            selectedDistrict = selectedAddress.district;
                            selectedWard = selectedAddress.ward;
                            // hiển thị modal update
                            $("#updateAddressModal").show();
                            // hiển thị dữ liệu của address cần update
                            $("#fullName").val(selectedAddress.name);
                            $("#phoneNumber").val(selectedAddress.phone_number);
                            // pdw-address
                            $("#pdw-address").val(`${selectedAddress.city_province}, ${selectedAddress.district}, ${selectedAddress.ward}`);
                            // specificAddress
                            $("#specificAddress").val(`${selectedAddress.apartment_number} ${selectedAddress.street}`);
                            // defaultAddress
                            if (parseInt(selectedAddress.is_default) === 1) {
                                $("#defaultAddress").prop("checked", true);
                            } else {
                                $("#defaultAddress").prop("checked", false);
                            }

                            // Xử lý khi nhấn nút Confirm Change Address trong updateAddressModal
                            $("#confirmChangeAddress").on("click", function() {

                                // Tạo body dữ liệu từ các input trong modal
                                const updatedAddressData = {
                                    name: $("#fullName").val(),
                                    street: $("#specificAddress").val().split(" ").slice(1).join(" "), // Lấy tên đường
                                    apartment_number: $("#specificAddress").val().split(" ")[0], // Lấy số nhà
                                    ward: selectedWard,
                                    district: selectedDistrict,
                                    city_province: selectedProvince,
                                    phone_number: $("#phoneNumber").val(),
                                    is_default: $("#defaultAddress").is(":checked") ? true : false
                                };

                                // Gọi API cập nhật địa chỉ
                                $.ajax({
                                    url: `${BASE_API_URL}/api/users/addresses/${selectedAddressId}`,
                                    type: "PUT",
                                    contentType: "application/json",
                                    data: JSON.stringify(updatedAddressData),
                                    success: function(response) {
                                        if (response.success) {
                                            alert("Cập nhật địa chỉ thành công!");
                                            $("#updateAddressModal").hide();
                                            location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                                            modal.show();
                                        } else {
                                            alert("Có lỗi xảy ra khi cập nhật địa chỉ.");
                                        }
                                    },
                                    error: function() {
                                        console.log("Dữ liệu gửi lên:", updatedAddressData);

                                        alert("Không thể kết nối đến server.");
                                    }
                                });
                            });

                        });

                        // Đóng modal khi nhấn nút X hoặc nút Cancel
                        $("#updateAddressModal .closeUpdateAddressModal").on("click", function() {
                            $("#updateAddressModal").hide();
                            // Hiển thị lại modal chọn địa chỉ
                            modal.show();
                        });

                        // Ẩn modal khi click ra ngoài
                        $(window).on("click", function(event) {
                            if ($(event.target).is("#updateAddressModal")) {
                                $("#updateAddressModal").hide();
                                modal.show();
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
                        <td class="column-4">${formatCurrency(product.price)}</td>
                        <td class="column-5">${product.quantity}</td>
                        <td class="column-6">${formatCurrency(productTotal)}</td>
                    </tr>
                `;
            });

            $(".table-shopping-cart").append(productHTML);
            $(".total").text(formatCurrency(total));
        }
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD"
        }).format(amount);
    }

    function unformatCurrency(amount) {
        return parseFloat(amount.replace(/[^0-9.-]+/g, ""));
    }

    async function getExchangeRate() {
        const response = await fetch('https://open.er-api.com/v6/latest/USD');
        const data = await response.json();
        const rate = data.rates.VND;
        return rate;
    }

    $(".checkout-button").click(function() {
        if($(".address-out").text() == "") {
            Swal.fire({
                icon: "warning",
                title: "Please select a delivery address!",
                text: "You need to select a delivery address before checking out.",
                showConfirmButton: true,
                confirmButtonText: "Add address",
                confirmButtonColor: "#3085d6"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `${BASE_API_URL}/client/index.php?content=pages/profile.php`;
                }
            });
        }
        const userData = localStorage.getItem("user");
        if (!userData) {
            Swal.fire({
                icon: "warning",
                title: "You are not logged in!",
                text: "Please log in to buy products.",
                showConfirmButton: true,
                confirmButtonText: "Log in now",
                confirmButtonColor: "#3085d6"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../client/pages/login.php";
                }
            });
        }
        const userObject = JSON.parse(userData);
        const user_id = userObject.id;
        const total = $(".total").text().replace(/[^0-9]/g, "");
        const deliveryAddress = $(".address-out").text();
        const orderDate = new Date().toISOString().slice(0, 19).replace("T", " ");
        const paymentMethod = $("input[name='payment']:checked").val();
        const estimateReceivedDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().slice(0, 19).replace("T", " ");
        const receivedDate = new Date(Date.now() + 4 * 24 * 60 * 60 * 1000).toISOString().slice(0, 19).replace("T", " ");

        if (paymentMethod === "Momo") {
            let randomOrderId = Math.floor(Math.random() * 1000000);
            let amount = $(".total").text().replace(/[^0-9]/g, "");

            let formData = new FormData();
            formData.append("orderId", randomOrderId);
            getExchangeRate().then(rate => {
                if (rate !== null) {
                    amount = parseInt((parseFloat(amount) * rate / 100) / 1000) * 1000;
                    formData.append("amount", amount);
                    formData.append("paymentMethod", "Momo");

                    formData.append("userId", user_id);
                    formData.append("total_cents", total);
                    formData.append("delivery_address", deliveryAddress);
                    formData.append("order_date", orderDate);
                    formData.append("estimate_received_date", estimateReceivedDate);
                    formData.append("received_date", receivedDate);
                    const selected_products = JSON.parse(sessionStorage.getItem("selected_products"));
                    selected_products.forEach(product => {
                        formData.append("product_variation_id[]", product.product_variation_id);
                        formData.append("quantity[]", product.quantity);
                        formData.append("price[]", product.price);
                    });

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", `${BASE_API_URL}/client/pages/momoCheckout.php`, true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            try {
                                let response = JSON.parse(xhr.responseText);
                                if (response.status === "success") {
                                    window.location.href = `${BASE_API_URL}/client/pages/momoCheckout.php`;

                                } else {
                                    alert("Lỗi: " + response.message);
                                }
                            } catch (error) {
                                console.error("Lỗi phân tích JSON:", error);
                                alert("Lỗi hệ thống, vui lòng thử lại!");
                            }
                        } else {
                            console.error("Lỗi khi gửi đơn hàng.");
                        }
                    };

                    xhr.onerror = function() {
                        console.error("Không thể kết nối đến server.");
                    };

                    xhr.send(formData);
                } else {
                    console.log('Không thể lấy tỷ giá.');
                }
            });


        } else if (paymentMethod === "COD") {
            createOrder();
        }

        // Gọi API tạo đơn hàng
        function createOrder() {
            $.ajax({
                url: `${BASE_API_URL}/api/orders`,
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    user_id: user_id,
                    total_cents: total/100,
                    delivery_address: deliveryAddress,
                    order_date: orderDate,
                    delivery_state_id: 1,
                    estimate_received_date: estimateReceivedDate,
                    received_date: receivedDate,
                    payment_method: paymentMethod
                }),
                success: function(response) {
                    if (response.success) {
                        console.log("response1", response);
                        const order_id = response.data.id;
                        // Lưu order_id vào sessionStorage để sử dụng sau này
                        sessionStorage.setItem("order_id", order_id);

                        const selected_products = JSON.parse(sessionStorage.getItem("selected_products"));
                        selected_products.forEach(product => {
                            $.ajax({
                                url: `${BASE_API_URL}/api/carts?user_id=${user_id}&product_variation_id=${product.product_variation_id}`,
                                type: "DELETE",
                                success: function(response) {
                                    if (response.success) {
                                        console.log("Xóa sản phẩm khỏi giỏ hàng thành công!");
                                    } else {
                                        alert("Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng.");
                                    }
                                }
                            });
                            // gọi api get /products/instances?product_variation_id=455&quantity=2 để lấy mảng sku cho variation
                            $.ajax({
                                url: `${BASE_API_URL}/api/products/instances?product_variation_id=${product.product_variation_id}&quantity=${product.quantity}`,
                                type: "GET",
                                success: function(response) {
                                    if (response.success) {
                                        response.data.forEach(instance => {
                                            const sku = instance.sku;
                                            $.ajax({
                                                url: `${BASE_API_URL}/api/orders/items`,
                                                type: "POST",
                                                contentType: "application/json",
                                                data: JSON.stringify({
                                                    order_id: order_id,
                                                    product_instance_sku: sku,
                                                    price_cents: product.price
                                                }),
                                                success: function(response) {
                                                    if (response.success) {
                                                        console.log("Đặt hàng sku thành công!");
                                                    } else {
                                                        alert("Có lỗi xảy ra khi đặt hàng.");
                                                    }
                                                },
                                                error: function() {
                                                    alert("Không thể kết nối đến server.");
                                                }
                                            });
                                        });
                                    } else {
                                        alert("Có lỗi xảy ra khi lấy SKU.");
                                    }
                                },
                                error: function() {
                                    alert("Không thể kết nối đến server.");
                                }
                            });

                        });
                    } else {
                        alert("Có lỗi xảy ra khi đặt hàng.");
                    }
                    sessionStorage.removeItem("selected_products");
                    sessionStorage.removeItem("order_id");
                    alert("Đặt hàng thành công!");
                    window.location.href = `${BASE_API_URL}/client/index.php?content=pages/shopping-cart.php`; // Chuyển hướng về trang giỏ hàng
                },
                error: function() {
                    alert("Không thể kết nối đến server.");
                }
            });
        }
    });
</script>