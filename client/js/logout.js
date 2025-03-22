document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logout-btn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function (e) {
            e.preventDefault();

            // Xóa thông tin đăng nhập khỏi localStorage
            localStorage.removeItem("user");

            // Chuyển hướng về trang đăng nhập
            window.location.href = "./pages/login.php";
        });
    }

    // Kiểm tra trạng thái đăng nhập
    let user = localStorage.getItem("user");
    if (!user) {
        // Nếu chưa đăng nhập, hiển thị "Login" thay vì "Account"
        document.querySelector(".dropdown-account").innerHTML = '<a href="./pages/login.php">Login</a>';
    }
});
