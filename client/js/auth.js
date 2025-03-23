$(document).ready(function () {
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
        
        let email = $("input[name='newEmail']").val().trim();
        let password = $("input[name='newPassword']").val().trim();
        if (!email || !password) {
            alert("Email and password are required.");
            return;
        }
    
        $.ajax({
            url: "http://localhost:81/WebPHP_DoAn_EC/api/users",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: email, password: password }),
            dataType: "json",
            success: function (response) {
                alert("Registration successful! You can now log in.");
                $("#register-form").hide();
                $("#login-form").fadeIn();
                document.title = "Login";
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
        e.preventDefault();
        let email = $("input[name='email']").val().trim();
        let password = $("input[name='pass']").val().trim();
    
        // Kiểm tra nếu email hoặc password bị trống
        if (!email || !password) {
            alert("Vui lòng nhập đầy đủ email và mật khẩu.");
            return; // Không gửi request
        }
    
        $.ajax({
            url: `http://localhost:81/WebPHP_DoAn_EC/api/users?email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                alert(response.message); // Hiển thị thông báo đăng nhập thành công
                // Lưu thông tin user vào localStorage
                localStorage.setItem("user", JSON.stringify(response.data));
                // Chuyển hướng về trang chủ
                window.location.href = "../index.php";
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                alert("Error: " + error.message);
            }
        });
    });             
});