$(document).ready(function () {
    // GẮN THÊM BASE_API_URL VÀO ĐÂY VÌ Ở INDEX KHÔNG TRUYỀN ĐƯỢC VÀO FILE JS NÀY
    const BASE_API_URL = "http://localhost:3000/WebPHP_DoAn_EC";
    // Chuyển đổi giữa form đăng nhập và đăng ký
    $("#show-register").click(function (e) {
        e.preventDefault();
        $(".login100-form").hide();
        $("#register-form").fadeIn();
        document.title = "Register"; // Đổi tiêu đề thành "Register"
    });

    $("#show-login").click(function (e) {
        e.preventDefault();
        $("#register-form").hide();
        $("#login-form").fadeIn();
        document.title = "Login"; // Đổi tiêu đề thành "Login"
    });

    // hàm validateEmail
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    // Xử lý đăng ký bằng AJAX
    $("#register-form").submit(function (e) {
        e.preventDefault();
        console.log("register-form");
        let email = $("input[name='newEmail']").val().trim();
        let password = $("input[name='newPassword']").val().trim();
        if (!email || !password) {
            alert("Email and password are required.");
            return;
        }

        // Kiểm tra định dạng email
        if (!validateEmail(email)) {
            alert("Invalid email format.");
            return;
        }

        $.ajax({
            url: `${BASE_API_URL}/api/users`,
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: email, password: password }),
            dataType: "json",
            success: function (response) {
                alert("Registration successful! You can now log in.");
                // Chuyển về trang đăng nhập href="./pages/login.php"
                window.location.href = `${BASE_API_URL}/client/pages/login.php`;

            },
            error: function (xhr) {
                try {
                    let error = xhr.responseText ? JSON.parse(xhr.responseText) : { message: "Unknown error" };
                    alert("Error: " + error.message);
                } catch (e) {
                    alert("Error: Invalid JSON response from server.");
                    console.error("Parsing error:", e, "Response:", xhr.responseText);
                }
            }
        });
    });

    // Xử lý đăng nhập bằng AJAX
    $("#login-form").submit(function (e) {
        console.log("login-form");
        e.preventDefault();
        let email = $("input[name='email']").val().trim();
        let password = $("input[name='pass']").val().trim();
        console.log(email);
        console.log(password);
        // Kiểm tra nếu email hoặc password bị trống
        if (!email || !password) {
            alert("Vui lòng nhập đầy đủ email và mật khẩu.");
            return; // Không gửi request
        }

        // Kiểm tra định dạng email
        if (!validateEmail(email)) {
            alert("Địa chỉ email không hợp lệ.");
            return; // Không gửi request
        }

        $.ajax({
            url: `${BASE_API_URL}/api/users?email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                // Lấy thông tin user
                const userData = response.data;
                console.log("userData", userData);
                console.log("userData.id", userData.id);
                console.log("path", `${BASE_API_URL}/api/users/user_roles?user_id=${userData.id}`);
                // Kiểm tra role cua người dùng
                $.ajax({
                    url: `${BASE_API_URL}/api/users/user_roles?user_id=${userData.id}`,
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            console.log("response", response.data[0].role_id);
                            console.log("response.data.role_id", response.data[0].role_id);
                            // Kết hợp thông tin user và role
                            const userWithRole = {
                                role_id: response.data[0].role_id,
                                ...userData
                                
                            };
                            console.log("userWithRole", userWithRole);
                            // Lưu thông tin người dùng vào localStorage
                            localStorage.setItem("user", JSON.stringify(userWithRole));

                            // Chuyển hướng theo role
                            switch (response.data[0].role_id) {
                                case 1: // Admin
                                    alert("Đăng nhập thành công với quyền admin!");
                                    window.location.href = `${BASE_API_URL}/admin2/index.php`;
                                    break;
                                case 2: // Customer
                                    alert("Đăng nhập thành công với quyền khách hàng!");
                                    window.location.href = `${BASE_API_URL}/client/index.php`;
                                    break;
                                case 3: // Staff
                                    alert("Đăng nhập thành công với quyền nhân viên!");
                                    window.location.href = `${BASE_API_URL}/staff/index.php`;
                                    break;
                                default:
                                    alert("Tài khoản không có quyền truy cập!");
                                    window.location.href = `${BASE_API_URL}/client/login.php`;
                            }
                            // alert("Đăng nhập thành công với quyền admin!");
                            // // Chuyển hướng đến trang chủ sau khi đăng nhập thành công
                            // window.location.href = `${BASE_API_URL}/admin2/index.php`;
                        }
                    },
                    // error: function (xhr) {
                    //     let error = JSON.parse(xhr.responseText);
                    //     localStorage.setItem("user", JSON.stringify(response.data));
                    //     alert("Đăng nhập thành công với quyền user!");
                    //     window.location.href = `${BASE_API_URL}/client/index.php`;
                    // }
                    error: function (xhr) {
                        let error = JSON.parse(xhr.responseText);
                        alert("Lỗi khi kiểm tra quyền: " + error.message);
                        window.location.href = `${BASE_API_URL}/client/login.php`;
                    }
                });
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                alert("Đăng nhập thất bại: " + error.message);
            }
        });
    });
});