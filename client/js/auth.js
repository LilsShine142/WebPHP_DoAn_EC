$(document).ready(function () {
    // GẮN THÊM BASE_API_URL VÀO ĐÂY VÌ Ở INDEX KHÔNG TRUYỀN ĐƯỢC VÀO FILE JS NÀY
    const BASE_API_URL = "http://localhost:81/WebPHP_DoAn_EC";
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
        
        $.ajax({
            url: `${BASE_API_URL}/api/users`,
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: email, password: password }),
            dataType: "json",
            success: function (response) {
                alert("Registration successful! You can now log in.");
                // Chuyển về trang đăng nhập href="./pages/login.php"
                window.location.href = 'http://localhost:81/WebPHP_DoAn_EC/client/pages/login.php';

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
        
        $.ajax({
            url: `${BASE_API_URL}/api/users?email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                $.ajax({
                    url: `${BASE_API_URL}/api/users/user_roles?user_id=${response.data.id}&role_id=1`,
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        if(response.success) {
                            // Lưu thông tin người dùng vào localStorage
                            localStorage.setItem("user", JSON.stringify(response.data));
                            alert("Đăng nhập thành công với quyền admin!");
                            // Chuyển hướng đến trang chủ sau khi đăng nhập thành công
                            window.location.href = 'http://localhost:81/WebPHP_DoAn_EC/admin2';
                        }
                    },
                    error: function (xhr) {
                        let error = JSON.parse(xhr.responseText);
                        localStorage.setItem("user", JSON.stringify(response.data));
                        alert("Đăng nhập thành công với quyền user!");
                        window.location.href = 'http://localhost:81/WebPHP_DoAn_EC/client';
                    }
                });
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                alert("Error: " + error.message);
            }
        });
    });             
});