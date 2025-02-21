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
        $.ajax({
            type: "POST",
            url: "../backend/controllers/RegisterController.php",
            data: $(this).serialize(),
            success: function (response) {
                alert(response);
                if (response.trim() === "success") {
                    $("#register-form").hide();
                    $(".login100-form.validate-form").fadeIn();
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi đăng ký!");
            }
        });
    });

    // Xử lý đăng nhập bằng AJAX
    $("#login-form").submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../backend/controllers/LoginController.php",
            data: $(this).serialize(),
            success: function (response) {
                if (response.trim() === "success") {
                    window.location.href = "dashboard.php";
                } else {
                    alert("Sai tài khoản hoặc mật khẩu!");
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi đăng nhập!");
            }
        });
    });
});