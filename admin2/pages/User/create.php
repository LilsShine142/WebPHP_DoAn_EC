<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body>
    <button type="button" class="btn btn-primary" style="margin: 15px 20px -20px;" onclick="window.location.href='index.php?page=pages/User/list.php'">Quay về</button>
    <div class="container mt-5">
        <h2>Thêm Người dùng Mới</h2>
        <form action="index.php?page=pages/User/create.php" method="POST" id="user-form" enctype="multipart/form-data">
            <input type="hidden" id="customerId" name="customerId">

            <div class="form-group">
                <label for="fullname">Họ và tên:</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
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
                <label for="province">Tỉnh/Thành phố:</label>
                <select class="form-control" id="province" name="province" required></select>
            </div>

            <div class="form-group">
                <label for="district">Quận/Huyện:</label>
                <select class="form-control" id="district" name="district" required></select>
            </div>

            <div class="form-group">
                <label for="ward">Phường/Xã:</label>
                <select class="form-control" id="ward" name="ward" required></select>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ chi tiết:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <button type="submit" class="btn btn-primary btn-adduser" id="btn-adduser">Thêm Người dùng</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $(document).ready(function() {
                $("#btn-adduser").click(function(e) {
                    e.preventDefault();

                    if (!validateForm()) {
                        return;
                    }

                    let userData = {
                        full_name: $("#fullname").val(),
                        email: $("#email").val(),
                        phone_number: $("#phone").val(),
                        password: $("#password").val(),
                        province: $("#province").val(),
                        district: $("#district").val(),
                        ward: $("#ward").val(),
                        address: $("#address").val()
                    };

                    $.ajax({
                        url: `${BASE_API_URL}/api/users`,
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(userData),
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                console.log("response", response.data);
                                let userId = response.data.id; // API cần trả về user_id
                                console.log("userId", userId);
                                // Gọi API tạo role cho user
                                let roleData = {
                                    user_id: userId,
                                    role_id: $("#role").val() // ID của role được chọn từ form
                                };

                                $.ajax({
                                    url: `${BASE_API_URL}/api/users/user_roles`,
                                    type: "POST",
                                    contentType: "application/json",
                                    data: JSON.stringify(roleData),
                                    dataType: "json",
                                    success: function(roleResponse) {
                                        if (roleResponse.success) {
                                            toast("Thêm người dùng và phân quyền thành công!", "success");
                                            $("#user-form")[0].reset();
                                        } else {
                                            alert("Thêm user thành công nhưng lỗi khi phân quyền: " + roleResponse.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Lỗi phân quyền:", xhr.responseText);
                                        alert("Thêm user thành công nhưng lỗi khi phân quyền: " + xhr.responseText);
                                    }
                                });

                            } else {
                                alert("Lỗi khi thêm user: " + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Lỗi:", xhr.responseText);
                            alert("Có lỗi xảy ra: " + xhr.responseText);
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

    <script src="assets/js/Customer/validationCustomer.js"></script>
    <script src="assets/js/Customer/customerActions.js"></script>

</body>

</html>