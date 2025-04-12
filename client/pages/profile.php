<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        .profilePage-container {
            display: flex;
        }

        .profilePage-slidebar {
            width: 250px;
            padding: 20px;
            background: #fff;
            border-right: 1px solid #ddd;
        }

        .profile-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .username {
            font-size: 18px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        nav ul li.active,
        nav ul li:hover {
            background: #f5f5f5;
        }

        .profile-section {
            flex-grow: 1;
            padding: 20px;
        }

        h2 {
            margin-bottom: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            max-width: 500px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .gender-options {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            border: none;
            background: #ff5a5f;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #e04e50;
        }

        button {
            margin-top: 0;
            padding: 2px 6px;
        }
    </style>
</head>

<body>
    <div class="profilePage-container" style="margin: 40px 200px;">
        <aside class="profilePage-slidebar">
            <nav>
                <ul>
                    <li class="li-profile active">Profile</li>
                    <li class="li-addresses">Addresses</li>
                    <li class="li-changePass">Change Password</li>
                </ul>
            </nav>
        </aside>
        <main class="profile-section">
            <div class="user-infor">
                <h2>My Profile</h2>
                <p>Manage and protect your account</p>
                <form>

                    <label>Full name</label>
                    <input id="fullName" type="text" value="Đào Thanh Tú">

                    <label>Email</label>
                    <input id="email" type="email" value="th************@gmail.com" disabled>

                    <label>Phone Number</label>
                    <input id="phoneNumber" type="text" value="********43">

                    <button class="update-user-infor" style="margin-top: 20px;">Save</button>
                </form>
            </div>
            <!-- div Addresses -->
            <div class="addresses" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>My Addresses</h2>
                    <button id="add-new-address" style="padding: 10px; background: #5cb85c; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 0px;">
                        Add New Address
                    </button>
                </div>

                <div class="address-list">
                    <!-- Danh sách địa chỉ sẽ được đổ vào đây -->
                </div>
            </div>


            <div class="change-password" style="display: none;">
                <h2>Change Password</h2>
                <div class="input-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" placeholder="Enter your current password">
                    <!-- hiện thông báo -->
                    <div class="message" id="current-password-message" style="color: red; display: none;">Current password is incorrect</div>
                </div>

                <div class="input-group">
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" placeholder="Enter your new password">
                    <div class="message" id="new-password-message" style="color: red; display: none;">New password must be at least 8 characters long and contain at least one number and one letter</div>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <input type="password" id="confirm-password" placeholder="Confirm your new password">
                    <div class="message" id="confirm-password-message" style="color: red; display: none;">Passwords do not match</div>
                </div>

                <button class="button" id="change-password" style="background-color: #007bff;">Change Password</button>
                <div class="message" id="message"></div>
            </div>
        </main>
    </div>
    <!-- Modal Update Address -->
    <div id="updateAddressModal" class="modal">
        <div class="modal-content update-address-modal-content">
            <h2 class="modal-title" class="m-b-20">Update Address</h2>
            <div id="addressUpdate">
                <div class="input-row">
                    <div class="input-group">
                        <label for="fullName">Name</label>
                        <input type="text" class="fullName" value="">
                    </div>
                    <div class="input-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" class="phoneNumber" value="">
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
                    <input type="text" class="specificAddress" value="">
                </div>
            </div>
            <div class="buttons">
                <button class="closeUpdateAddressModal">Back</button>
                <button id="confirmChangeAddress">Confirm</button>
            </div>
        </div>
    </div>
</body>

</html>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // xử lí sự kiên nhấn các li 
    $(".li-profile").click(function() {
        $(".user-infor").show();
        $(".addresses").hide();
        $(this).addClass("active");
        $(".li-addresses").removeClass("active");
        $(".li-changePass").removeClass("active");
        $(".change-password").hide();
    });
    $(".li-addresses").click(function() {
        $(".user-infor").hide();
        $(".addresses").show();
        $(this).addClass("active");
        $(".li-profile").removeClass("active");
        $(".li-changePass").removeClass("active");
        $(".change-password").hide();
    });
    $(".li-changePass").click(function() {
        $(".user-infor").hide();
        $(".addresses").hide();
        $(".change-password").show();
        $(".li-changePass").addClass("active");
        $(this).addClass("active");
        $(".li-profile").removeClass("active");
        $(".li-addresses").removeClass("active");
    });

    selectedProvince = "";
    selectedDistrict = "";
    selectedWard = "";
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
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            // call ajax ${BASE_API_URL}/api/users/${user_id} to take the response
            $.ajax({
                url: `${BASE_API_URL}/api/users/${user_id}`,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        // hiển thị thông tin user lên form
                        $("#fullName").val(response.data.full_name);
                        $("#email").val(response.data.email);
                        $("#phoneNumber").val(response.data.phone_number);
                    } else {
                        console.error("Error fetching user data:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching user data:", error);
                }
            });
            // sự kiện update-user-infor
            $(".update-user-infor").on("click", function(event) {
                event.preventDefault(); // Ngăn chặn hành động mặc định của nút submit
                const updatedUserData = {
                    full_name: $("#fullName").val(),
                    email: $("#email").val(),
                    phone_number: $("#phoneNumber").val()
                };
                $.ajax({
                    url: `${BASE_API_URL}/api/users/${user_id}`,
                    type: "PUT",
                    contentType: "application/json",
                    data: JSON.stringify(updatedUserData),
                    success: function(response) {
                        if (response.success) {
                            alert("Cập nhật thông tin thành công!");
                            location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                        } else {
                            alert("Có lỗi xảy ra khi cập nhật thông tin.");
                        }
                    },
                    error: function() {
                        alert("Không thể kết nối đến server.");
                    }
                });
            });

            // sự kiện add-new-address
            $.ajax({
                url: `${BASE_API_URL}/api/users/addresses?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    if (response.success === true) {
                        $(".address-list").empty(); // Clear the list before adding the default address
                        response.data.sort((a, b) => {
                            return parseInt(a.is_default) - parseInt(b.is_default);
                        });
                        response.data.forEach(address => {
                            $(".address-list").prepend(`
                                    <div class="address-item" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between;">
                                        <div>
                                            <p><strong>${address.name}</strong> <br> <span>${address.phone_number}</span> <br> ${address.apartment_number} ${address.street} <br> ${address.ward}, ${address.district}, ${address.city_province}</p>
                                            ${address.is_default == 1 ? '<span style="background: #5cb85c; color: white; padding: 2px 5px; border-radius: 3px; font-size: 12px;">Default</span>' : ''}
                                        </div>
                                        <div style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center;">
                                            <input id="${address.id}" name="selectedAddress" value="${address.id}" style="display: none;">
                                            ${address.is_default == 0 ? '<button class="set-default" style="background: #f0ad4e; color: white; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 5px;">Set as default</button>' : ''}
                                            <button class="edit-address" style="background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 5px;">Edit</button>
                                            ${address.is_default == 0 ? '<button class="delete-address" style="background: #d9534f; color: white; border: none; border-radius: 5px; cursor: pointer;">Delete</button>' : ''}
                                        </div>
                                    </div>`);
                        });
                        $(document).on("click", ".edit-address", function() {
                            // lấy thông tin address cần update
                            const selectedAddressId = $(this).siblings("input[name='selectedAddress']").val();
                            const selectedAddress = response.data.find(address => address.id == selectedAddressId);
                            // cập nhật biến tỉnh, huyện, xã toàn cục
                            selectedProvince = selectedAddress.city_province;
                            selectedDistrict = selectedAddress.district;
                            selectedWard = selectedAddress.ward;
                            // hiển thị modal update
                            $("#updateAddressModal").show();
                            // hiển thị dữ liệu của address cần update
                            $(".fullName").val(selectedAddress.name);
                            $(".phoneNumber").val(selectedAddress.phone_number);
                            // pdw-address
                            $("#pdw-address").val(`${selectedAddress.city_province}, ${selectedAddress.district}, ${selectedAddress.ward}`);
                            // specificAddress
                            $(".specificAddress").val(`${selectedAddress.apartment_number} ${selectedAddress.street}`);


                            // Xử lý khi nhấn nút Confirm Change Address trong updateAddressModal
                            $("#confirmChangeAddress").on("click", function() {

                                // Tạo body dữ liệu từ các input trong modal
                                const updatedAddressData = {
                                    name: $(".fullName").val(),
                                    // lấy hết phần tử trừ phần tử đầu tiên
                                    street: $(".specificAddress").val().split(" ").slice(1).join(" "), // Lấy tên đường
                                    apartment_number: $(".specificAddress").val().split(" ")[0], // Lấy số nhà
                                    ward: selectedWard,
                                    district: selectedDistrict,
                                    city_province: selectedProvince,
                                    phone_number: $(".phoneNumber").val(),
                                    is_default: false
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


                        // Xử lý sự kiện nhấn nút Delete
                        $(document).on("click", ".delete-address", function() {
                            const selectedAddressId = $(this).siblings("input[name='selectedAddress']").val();
                            if (confirm("Bạn có chắc chắn muốn xóa địa chỉ này không?")) {
                                $.ajax({
                                    url: `${BASE_API_URL}/api/users/addresses/${selectedAddressId}`,
                                    type: "DELETE",
                                    success: function(response) {
                                        if (response.success) {
                                            alert("Xóa địa chỉ thành công!");
                                            location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                                        } else {
                                            alert("Có lỗi xảy ra khi xóa địa chỉ.");
                                        }
                                    },
                                    error: function() {
                                        alert("Không thể kết nối đến server.");
                                    }
                                });
                            }
                        });

                        // Đóng modal khi nhấn nút X hoặc nút Cancel
                        $("#updateAddressModal .closeUpdateAddressModal").on("click", function() {
                            $("#updateAddressModal").hide();
                        });

                        // Ẩn modal khi click ra ngoài
                        $(window).on("click", function(event) {
                            if ($(event.target).is("#updateAddressModal")) {
                                $("#updateAddressModal").hide();
                            }
                        });

                        // set-default
                        $(".set-default").on("click", function() {
                            const addressId = $(this).siblings("input[name='selectedAddress']").val();
                            $.ajax({
                                url: `${BASE_API_URL}/api/users/addresses?user_id=${user_id}`,
                                type: "PUT",
                                contentType: "application/json",
                                data: JSON.stringify({
                                    is_default: false
                                }),
                                success: function(response) {
                                    if (response.success) {
                                        $.ajax({
                                            url: `${BASE_API_URL}/api/users/addresses/${addressId}`,
                                            type: "PUT",
                                            contentType: "application/json",
                                            data: JSON.stringify({
                                                is_default: true
                                            }),
                                            success: function(response) {
                                                if (response.success) {
                                                    alert("Đặt địa chỉ làm mặc định thành công!");
                                                    location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                                                } else {
                                                    alert("Có lỗi xảy ra khi đặt địa chỉ làm mặc định.");
                                                }
                                            },
                                            error: function() {
                                                alert("Không thể kết nối đến server.");
                                            }
                                        });
                                    } else {
                                        alert("Có lỗi xảy ra khi đặt địa chỉ làm mặc định.");
                                    }
                                },
                                error: function() {
                                    alert("Không thể kết nối đến server.");
                                }
                            });
                        });
                    }

                    // Xử lý sự kiện nhấn nút Add New Address
                    $("#add-new-address").on("click", function() {
                        // reset dữ liệu trong modal
                        $(".modal-title").text("Add New Address");
                        $(".fullName").val("");
                        $(".phoneNumber").val("");
                        $("#pdw-address").val("");
                        $(".specificAddress").val("");
                        // hiển thị modal update
                        $("#updateAddressModal").show();
                        // gọi api thêm address khi nhấn nút comfirm
                        $("#confirmChangeAddress").on("click", function() {
                            // Tạo body dữ liệu từ các input trong modal
                            const newAddressData = {
                                user_id: user_id,
                                name: $(".fullName").val(),
                                street: $(".specificAddress").val().split(" ").slice(1).join(" "), // Lấy tên đường
                                apartment_number: $(".specificAddress").val().split(" ")[0], // Lấy số nhà
                                ward: selectedWard,
                                district: selectedDistrict,
                                city_province: selectedProvince,
                                phone_number: $(".phoneNumber").val(),
                                is_default: false
                            };

                            // Gọi API thêm địa chỉ mới
                            $.ajax({
                                url: `${BASE_API_URL}/api/users/addresses?user_id=${user_id}`,
                                type: "POST",
                                contentType: "application/json",
                                data: JSON.stringify(newAddressData),
                                success: function(response) {
                                    if (response.success) {
                                        alert("Thêm địa chỉ thành công!");
                                        $("#updateAddressModal").hide();
                                        location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                                    } else {
                                        alert("Có lỗi xảy ra khi thêm địa chỉ.");
                                    }
                                },
                                error: function() {
                                    console.log("Dữ liệu gửi lên:", newAddressData);

                                    alert("Không thể kết nối đến server.");
                                }
                            });
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching addresses:", error);
                }
            });

            // sự kiện change-password
            $("#change-password").on("click", function(event) {
                event.preventDefault(); // Ngăn chặn hành động mặc định của nút submit
                let email = userObject.email;
                const currentPassword = $("#current-password").val();
                const newPassword = $("#new-password").val();
                const confirmPassword = $("#confirm-password").val();
                $.ajax({
                    url: `${BASE_API_URL}/api/users?email=${encodeURIComponent(email)}&password=${encodeURIComponent(currentPassword)}`,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Nếu mật khẩu hiện tại đúng, tiến hành cập nhật mật khẩu mới
                            $("#current-password-message").hide();
                        } else {
                            alert("Mật khẩu hiện tại không đúng!");
                        }
                    },
                    error: function(xhr) {
                        //show div message 
                        $("#current-password-message").show();
                        console.error("Error fetching user data:", xhr.responseText);
                    }
                });

                if (newPassword !== confirmPassword) {
                    $("#confirm-password-message").show();
                    $("#new-password-message").hide();
                    return;
                } else if (!newPassword || !currentPassword || !confirmPassword) {
                    alert("Vui lòng nhập đầy đủ thông tin!");
                    return;
                } else if (newPassword.length < 8 || !/\d/.test(newPassword) || !/[a-zA-Z]/.test(newPassword)) {
                    $("#new-password-message").show();
                    $("#confirm-password-message").hide();
                    return;
                } else {
                    $("#new-password-message").hide();
                    $("#confirm-password-message").hide();
                    $.ajax({
                        url: `${BASE_API_URL}/api/users/${user_id}`,
                        method: "PUT",
                        contentType: "application/json",
                        data: JSON.stringify({
                            password: newPassword
                        }),
                        success: function(response) {
                            if (response.success) {
                                alert("Đổi mật khẩu thành công!");
                                location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                            } else {
                                alert("Có lỗi xảy ra khi đổi mật khẩu.");
                            }
                        },
                        error: function() {
                            alert("Không thể kết nối đến server.");
                        }
                    });
                }

            });
        } else {
            console.error("User data not found in localStorage.");
        }
    });
</script>