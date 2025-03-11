<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body>
    <button type="button" class="btn btn-primary" style="margin: 15px 20px -20px;" onclick="window.location.href='index.php?page=pages/User/list.php'">Quay về</button>
    <div class="container mt-5">
        <h2>Add New User</h2>
        <form action="index.php?page=pages/User/create.php" method="POST" id="user-form" enctype="multipart/form-data">
            <input type="hidden" id="userId" name="userId">

            <div class="form-group">
                <label for="fullname">Full name:</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone number:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="phone">password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Select role:</label>
                <select class="form-control" id="role" name="role" required></select>
            </div>
            <!-- =============================== Call API để lấy danh sách các role =============================== -->
            <script>
                $(document).ready(function() {
                    $.ajax({
                        url: `${BASE_API_URL}/api/users/roles`,
                        type: "GET",
                        contentType: "application/json",
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                let roles = response.data;
                                let roleSelect = $("#role");
                                roles.forEach(role => {
                                    roleSelect.append(`<option value="${role.id}">${role.name}</option>`);
                                });
                            } else {
                                alert("Lỗi khi lấy danh sách role: " + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Lỗi:", xhr.responseText);
                            alert("Có lỗi xảy ra: " + xhr.responseText);
                        }
                    });
                });
            </script>

            <div class="form-group">
                <label for="city_province">City province:</label>
                <select class="form-control" id="city_province" name="city_province" required></select>
            </div>

            <div class="form-group">
                <label for="district">District:</label>
                <select class="form-control" id="district" name="district" required></select>
            </div>

            <div class="form-group">
                <label for="ward">Ward:</label>
                <select class="form-control" id="ward" name="ward" required></select>
            </div>

            <div class="form-group">
                <label for="street">Street:</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>
            <div class="form-group">
                <label for="apartment_number">Apartment number:</label>
                <input type="text" class="form-control" id="apartment_number" name="apartment_number" required>
            </div>

            <button type="submit" class="btn btn-primary btn-adduser" id="btn-adduser">Thêm Người dùng</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            //==================================== CALL API LẤY DANH SÁCH TỈNH/THÀNH PHỐ,.... ==================================== 
            //Lấy danh sách tỉnh/thành phố khi trang tải
            fetch("https://provinces.open-api.vn/api/?depth=1")
                .then(response => response.json())
                .then(data => {
                    let provinceSelect = document.getElementById("city_province");
                    provinceSelect.innerHTML = '<option value="">Select province</option>';
                    data.forEach(province => {
                        let option = document.createElement("option");
                        option.value = province.code;
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Lỗi khi tải danh sách tỉnh:", error));

            // Khi chọn tỉnh/thành phố, lấy danh sách quận/huyện
            document.getElementById("city_province").addEventListener("change", function() {
                let provinceCode = this.value;
                fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        let districtSelect = document.getElementById("district");
                        districtSelect.innerHTML = '<option value="">Select district</option>';
                        data.districts.forEach(district => {
                            let option = document.createElement("option");
                            option.value = district.code;
                            option.textContent = district.name;
                            districtSelect.appendChild(option);
                        });

                        // Xóa danh sách phường/xã khi thay đổi tỉnh
                        document.getElementById("ward").innerHTML = '<option value="">Select ward</option>';
                    })
                    .catch(error => console.error("Lỗi khi tải danh sách quận/huyện:", error));
            });

            // Khi chọn quận/huyện, lấy danh sách phường/xã
            document.getElementById("district").addEventListener("change", function() {
                let districtCode = this.value;
                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        let wardSelect = document.getElementById("ward");
                        wardSelect.innerHTML = '<option value="">Select ward</option>';
                        data.wards.forEach(ward => {
                            let option = document.createElement("option");
                            option.value = ward.code;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Lỗi khi tải danh sách phường/xã:", error));
            });
            $(document).ready(function() {
                $("#btn-adduser").click(function(e) {
                    e.preventDefault();

                    if (!validateForm(false)) {
                        return;
                    }

                    let userData = {
                        full_name: $("#fullname").val().trim(),
                        email: $("#email").val().trim(),
                        phone_number: $("#phone").val().trim(),
                        password: $("#password").val().trim()
                    };

                    $.ajax({
                        url: `${BASE_API_URL}/api/users`,
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(userData),
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                let userId = response.data.id;
                                console.log("User ID:", userId);

                                // Gọi API phân quyền trước
                                let roleData = {
                                    user_id: userId,
                                    role_id: $("#role").val()
                                };

                                $.ajax({
                                    url: `${BASE_API_URL}/api/users/user_roles`,
                                    type: "POST",
                                    contentType: "application/json",
                                    data: JSON.stringify(roleData),
                                    dataType: "json",
                                    success: function(roleResponse) {
                                        if (roleResponse.success) {
                                            console.log("Phân quyền thành công");

                                            // Sau khi phân quyền thành công, gọi API lưu địa chỉ
                                            // Vì api lưu địa chỉ cần user_id, name, phone nên phải thêm thông tin vào addressData
                                            let addressData = {
                                                ...userData,
                                                user_id: userId,
                                                name: userData.full_name,
                                                phone_number: userData.phone_number,
                                                city_province: $("#city_province option:selected").text(),
                                                district: $("#district option:selected").text(),
                                                ward: $("#ward option:selected").text(),
                                                street: $("#street").val().trim(),
                                                apartment_number: $("#apartment_number").val().trim(),
                                                is_default: true
                                            };
                                            console.log("Address data:", addressData);

                                            $.ajax({
                                                url: `${BASE_API_URL}/api/users/addresses`,
                                                type: "POST",
                                                contentType: "application/json",
                                                data: JSON.stringify(addressData),
                                                dataType: "json",
                                                success: function(addressResponse) {
                                                    console.log("Lưu địa chỉ:", addressResponse);
                                                    if (addressResponse.success) {
                                                        toast("Thêm người dùng, phân quyền và địa chỉ thành công!", "success");
                                                        $("#user-form")[0].reset();
                                                    } else {
                                                        alert("Lỗi khi lưu địa chỉ: " + addressResponse.message);
                                                    }
                                                },
                                                error: function(xhr) {
                                                    console.error("Lỗi khi lưu địa chỉ:", xhr.responseText);
                                                    alert("Lỗi khi lưu địa chỉ: " + xhr.responseText);
                                                }
                                            });

                                        } else {
                                            alert("Thêm user thành công nhưng lỗi khi phân quyền: " + roleResponse.message);
                                        }
                                    },
                                    error: function(xhr) {
                                        console.error("Lỗi phân quyền:", xhr.responseText);
                                        alert("Lỗi phân quyền: " + xhr.responseText);
                                    }
                                });

                            } else {
                                alert("Lỗi khi thêm user: " + response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi tạo user:", xhr.responseText);
                            alert("Lỗi khi tạo user: " + xhr.responseText);
                        }
                    });
                });
            });

            // Hàm hiển thị thông báo
            function toast(message, type = "success") {
                console.log("Toast function called with:", message, type); // Kiểm tra xem có gọi được không

                let colors = {
                    success: "#28a745", // Xanh lá
                    error: "#dc3545", // Đỏ
                    warning: "#ffc107", // Vàng
                    info: "#17a2b8" // Xanh dương
                };

                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: colors[type] || "#343a40", // Mặc định là màu xám
                }).showToast();
            }
        });
    </script>
    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="assets/js/User/validationUser.js"></script>
    <script src="./assets/js/User/userActions.js"></script>
</body>

</html>