// Hàm kiểm tra email hợp lệ
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Hàm kiểm tra số điện thoại hợp lệ (10 số, bắt đầu bằng 0)
function validatePhone(phone) {
    return /^(0[0-9]{9})$/.test(phone);
}

// Hiển thị lỗi ngay dưới ô nhập
function showError(input, message) {
    clearError(input); // Xóa lỗi cũ nếu có

    input.classList.add("is-invalid"); // Thêm class Bootstrap
    let errorDiv = document.createElement("div");
    errorDiv.classList.add("invalid-feedback"); // Bootstrap class
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
}

// Xóa thông báo lỗi của một input
function clearError(input) {
    input.classList.remove("is-invalid");
    let existingError = input.parentElement.querySelector(".invalid-feedback");
    if (existingError) {
        existingError.remove();
    }
}

// Xóa toàn bộ thông báo lỗi và trạng thái input
function clearAllErrors() {
    document.querySelectorAll(".is-invalid").forEach(input => {
        clearError(input);
    });
}


// Hiển thị thông báo thành công bằng Bootstrap Toast
function showSuccessMessage(message) {
    let toastContainer = document.getElementById("toastContainer");

    let toastHTML = `
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.innerHTML = toastHTML;
    let toast = new bootstrap.Toast(toastContainer.querySelector(".toast"));
    toast.show();
}

function validateForm(isUpdate = false) {
    let isValid = true;

    // Xóa thông báo lỗi cũ
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").remove();

    let fullname = $("#fullname");
    let email = $("#email");
    let phone = $("#phone");
    let password = $("#password");
    let role = $("#role");
    let city = $("#city_province");
    let district = $("#district");
    let ward = $("#ward");
    let street = $("#street");
    let apartment = $("#apartment_number");

    // Kiểm tra họ và tên
    if (fullname.val().trim() === "") {
        showError(fullname[0], "Họ và tên không được để trống.");
        isValid = false;
    }

    // Kiểm tra email
    if (!validateEmail(email.val().trim())) {
        showError(email[0], "Email không hợp lệ.");
        isValid = false;
    }

    // Kiểm tra số điện thoại
    if (!validatePhone(phone.val().trim())) {
        showError(phone[0], "Số điện thoại phải có 10 số và bắt đầu bằng 0.");
        isValid = false;
    }

    // Kiểm tra mật khẩu (Chỉ kiểm tra khi thêm mới hoặc khi cập nhật có nhập mật khẩu)
    if (!isUpdate || password.val().trim() !== "") {
        if (password.val().trim().length < 8) {
            showError(password[0], "Mật khẩu phải có ít nhất 8 ký tự.");
            isValid = false;
        }
    }

    // Kiểm tra vai trò (Chỉ kiểm tra nếu là thêm mới)
    if (!isUpdate && role.val() === "") {
        showError(role[0], "Vui lòng chọn vai trò.");
        isValid = false;
    }

    // Kiểm tra địa chỉ
    if (city.val() === "") {
        showError(city[0], "Vui lòng chọn tỉnh/thành phố.");
        isValid = false;
    }
    if (district.val() === "") {
        showError(district[0], "Vui lòng chọn quận/huyện.");
        isValid = false;
    }
    if (ward.val() === "") {
        showError(ward[0], "Vui lòng chọn phường/xã.");
        isValid = false;
    }
    if (street.val().trim() === "") {
        showError(street[0], "Vui lòng nhập tên đường.");
        isValid = false;
    }
    if (apartment.val().trim() === "") {
        showError(apartment[0], "Vui lòng nhập số nhà.");
        isValid = false;
    }

    return isValid;
}
