<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup Form</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="form-box login">
            <form id="login-form" action="login?form=" method="POST" class="validate-form">
                <h1>Login</h1>
                <div class="input-box validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <input type="text" name="email" placeholder="Email" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box validate-input" data-validate="Password is required">
                    <input name="pass" type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <div class="form-box forgot-password" style="display: none;">
            <div class="change-password">
                <h1 style="padding-bottom: 40px;">Change Password</h1>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" placeholder="Enter your email">
                    <!-- hiện thông báo -->
                    <div class="message" id="email" style="color: red; display: none;">Email is incorrect</div>
                </div>

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
        </div>

        <div class="form-box register">
            <form id="register-form" action="register?form=" method="POST" class="validate-form">
                <h1>Registration</h1>
                <div class="input-box validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <input type="text" name="newEmail" placeholder="Email" required>
                    <i class='bx bxs-envelope' ></i>
                </div>
                <div class="input-box validate-input" data-validate="Password is required">
                    <input type="password" name="newPassword" placeholder="Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn" id="show-login">Login</button>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script src="../vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/bootstrap/js/popper.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/tilt/titl.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <!--===============================================================================================-->
    <script src="../js/mainlogin.js"></script>
    <script src="../js/auth.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bcryptjs/2.4.3/bcrypt.min.js"></script>
</body>
</html>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        text-decoration: none;
        list-style: none;
    }

    body{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(90deg, #e2e2e2, #c9d6ff);
    }

    .container{
        position: relative;
        width: 850px;
        height: 550px;
        background: #fff;
        margin: 20px;
        border-radius: 30px;
        box-shadow: 0 0 30px rgba(0, 0, 0, .2);
        overflow: hidden;
    }

        .container h1{
            font-size: 36px;
            margin: -10px 0;
        }

        .container p{
            font-size: 14.5px;
            margin: 15px 0;
        }

    form{ width: 100%; }

    .form-box{
        position: absolute;
        right: 0;
        width: 50%;
        height: 100%;
        background: #fff;
        display: flex;
        align-items: center;
        color: #333;
        text-align: center;
        padding: 40px;
        z-index: 1;
        transition: .6s ease-in-out 1.2s, visibility 0s 1s;
    }

        .container.active .form-box{ right: 50%; }

        .form-box.register{ visibility: hidden; }
            .container.active .form-box.register{ visibility: visible; }

    .input-box{
        position: relative;
        margin: 30px 0;
    }

        .input-box input{
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

            .input-box input::placeholder{
                color: #888;
                font-weight: 400;
            }
        
        .input-box i{
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

    .forgot-link{ margin: -15px 0 15px; }
        .forgot-link a{
            font-size: 14.5px;
            color: #333;
        }

    .btn{
        width: 100%;
        height: 48px;
        background: #7494ec;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        font-weight: 600;
    }

    .social-icons{
        display: flex;
        justify-content: center;
    }

        .social-icons a{
            display: inline-flex;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 24px;
            color: #333;
            margin: 0 8px;
        }

    .toggle-box{
        position: absolute;
        width: 100%;
        height: 100%;
    }

        .toggle-box::before{
            content: '';
            position: absolute;
            left: -250%;
            width: 300%;
            height: 100%;
            background: #7494ec;
            /* border: 2px solid red; */
            border-radius: 150px;
            z-index: 2;
            transition: 1.8s ease-in-out;
        }

            .container.active .toggle-box::before{ left: 50%; }

    .toggle-panel{
        position: absolute;
        width: 50%;
        height: 100%;
        /* background: seagreen; */
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 2;
        transition: .6s ease-in-out;
    }

        .toggle-panel.toggle-left{ 
            left: 0;
            transition-delay: 1.2s; 
        }
            .container.active .toggle-panel.toggle-left{
                left: -50%;
                transition-delay: .6s;
            }

        .toggle-panel.toggle-right{ 
            right: -50%;
            transition-delay: .6s;
        }
            .container.active .toggle-panel.toggle-right{
                right: 0;
                transition-delay: 1.2s;
            }

        .toggle-panel p{ margin-bottom: 20px; }

        .toggle-panel .btn{
            width: 160px;
            height: 46px;
            background: transparent;
            border: 2px solid #fff;
            box-shadow: none;
        }

    @media screen and (max-width: 650px){
        .container{ height: calc(100vh - 40px); }

        .form-box{
            bottom: 0;
            width: 100%;
            height: 70%;
        }

            .container.active .form-box{
                right: 0;
                bottom: 30%;
            }

        .toggle-box::before{
            left: 0;
            top: -270%;
            width: 100%;
            height: 300%;
            border-radius: 20vw;
        }

            .container.active .toggle-box::before{
                left: 0;
                top: 70%;
            }

            .container.active .toggle-panel.toggle-left{
                left: 0;
                top: -30%;
            }

        .toggle-panel{ 
            width: 100%;
            height: 30%;
        }
            .toggle-panel.toggle-left{ top: 0; }
            .toggle-panel.toggle-right{
                right: 0;
                bottom: -30%;
            }

                .container.active .toggle-panel.toggle-right{ bottom: 0; }
    }

    @media screen and (max-width: 400px){
        .form-box { padding: 20px; }

        .toggle-panel h1{font-size: 30px; }
    }

    .input-group {
        flex: 1; /* Chia đều không gian */
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group input{
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .input-group select {
        width: 100%;
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const container = document.querySelector('.container');
    const registerBtn = document.querySelector('.register-btn');
    const loginBtn = document.querySelector('.login-btn');

    registerBtn.addEventListener('click', () => {
        container.classList.add('active');
    })

    loginBtn.addEventListener('click', () => {
        container.classList.remove('active');
    });

    $('.forgot-link a').click(function(e) {
        e.preventDefault();
        $('.form-box.login').hide();
        $('.form-box.forgot-password').show();
    });

    $('#show-login').click(function(e) {
        e.preventDefault();
        $('.form-box.forgot-password').hide();
        $('.form-box.login').show();
    });

    $("#change-password").on("click", function (event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của nút submit
        let email = $("#email").val();
        const currentPassword = $("#current-password").val();
        const newPassword = $("#new-password").val();
        const confirmPassword = $("#confirm-password").val();

        // Kiểm tra thông tin đầu vào
        if (!email || !currentPassword || !newPassword || !confirmPassword) {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }
        
        if (newPassword !== confirmPassword) {
            $("#confirm-password-message").show();
            $("#new-password-message").hide();
            return;
        } else if (newPassword.length < 8 || !/\d/.test(newPassword) || !/[a-zA-Z]/.test(newPassword)) {
            $("#new-password-message").show();
            $("#confirm-password-message").hide();
            return;
        } else {
            $("#new-password-message").hide();
            $("#confirm-password-message").hide();
        }

        // Kiểm tra mật khẩu hiện tại
        $.ajax({
            url: `${BASE_API_URL}/api/users?email=${encodeURIComponent(email)}&password=${encodeURIComponent(currentPassword)}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Nếu mật khẩu hiện tại đúng, tiến hành cập nhật mật khẩu mới
                    $.ajax({
                        url: `${BASE_API_URL}/api/users/${response.data.id}`, // Sử dụng id từ phản hồi
                        method: "PUT",
                        contentType: "application/json",
                        data: JSON.stringify({ password: newPassword }),
                        success: function (response) {
                            if (response.success) {
                                alert("Đổi mật khẩu thành công!");
                                location.reload(); // Tải lại trang để cập nhật thông tin hiển thị
                            } else {
                                alert("Có lỗi xảy ra khi đổi mật khẩu.");
                            }
                        },
                        error: function () {
                            alert("Không thể kết nối đến server.");
                        }
                    });
                } else {
                    alert("Mật khẩu hiện tại không đúng!");
                }
            },
            error: function (xhr) {
                console.error("Error fetching user data:", xhr.responseText);
                // alert("Không thể kết nối đến server.");
                $("#current-password-message").show();
            }
        });
    });
</script>