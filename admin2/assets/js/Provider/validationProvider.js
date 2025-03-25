// Hiển thị lỗi ngay dưới ô nhập
function showError(input, message) {
    if (!input) {
        console.error("Lỗi: Input không tồn tại", message);
        return;
    }

    clearError(input); // Xóa lỗi cũ nếu có
    input.classList.add("is-invalid");

    let errorDiv = document.createElement("div");
    errorDiv.classList.add("invalid-feedback");
    errorDiv.textContent = message;

    input.parentElement.appendChild(errorDiv);
}


// Xóa thông báo lỗi của một input
function clearError(input) {
    if (!input) return; // Đảm bảo input tồn tại trước khi thao tác
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

function toast(message, type = "success", useBootstrap = false) {
    console.log("Toast function called with:", message, type, useBootstrap);

    if (useBootstrap) {
        showBootstrapToast(message, type);
    } else {
        showToastifyToast(message, type);
    }
}

// Hiển thị thông báo bằng Toastify
function showToastifyToast(message, type) {
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
        backgroundColor: colors[type] || "#343a40", // Mặc định màu xám
    }).showToast();
}

// Hiển thị thông báo bằng Bootstrap Toast
function showBootstrapToast(message, type) {
    let toastContainer = document.getElementById("toastContainer");

    let bgColorClass = {
        success: "bg-success",
        error: "bg-danger",
        warning: "bg-warning text-dark",
        info: "bg-info"
    };

    let toastHTML = `
        <div class="toast align-items-center text-white ${bgColorClass[type] || "bg-secondary"} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.innerHTML = toastHTML;

    let toastEl = toastContainer.querySelector(".toast");
    let bsToast = new bootstrap.Toast(toastEl);
    bsToast.show();
}

function isValidPhoneNumber(phone) {
    const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/; // Kiểm tra số Việt Nam
    return phoneRegex.test(phone);
}
// Kiểm tra trùng dữ liệu nhà cung cấp
async function isDuplicateProvider(provider) {
    try {
        let response = await $.ajax({
            url: `${BASE_API_URL}/api/providers`,
            method: "GET",
            contentType: "application/json",
        });

        console.log("Response:", response);

        if (!response || !response.data || !Array.isArray(response.data)) {
            console.error("Dữ liệu API không hợp lệ:", response);
            return null;
        }

        let errors = {}; // Object chứa lỗi

        response.data.forEach(p => {
            if (p.id != provider.id) { // Loại trừ chính nó khi cập nhật
                console.log("Provider:", provider);
                console.log("P:", p);
                console.log("Provider.full name:", provider.full_name);
                console.log("P.full name:", p.full_name);
                if (p.full_name && provider.full_name && p.full_name.toLowerCase() === provider.full_name.toLowerCase()) {
                    errors.full_name = "Tên nhà cung cấp đã tồn tại!";
                }
                if (p.email && provider.email && p.email.toLowerCase() === provider.email.toLowerCase()) {
                    errors.email = "Email đã tồn tại!";
                }
                if (p.phone_number && provider.phone_number && p.phone_number === provider.phone_number) {
                    errors.phone_number = "Số điện thoại đã tồn tại!";
                }
            }
        });

        return Object.keys(errors).length > 0 ? errors : null;
    } catch (error) {
        console.error("Lỗi kiểm tra trùng dữ liệu:", error);
        return null; // Trả về null nếu có lỗi
    }
}






async function validateProvider(provider, isUpdate = false) {
    let isValid = true;

    // Xác định modal đúng
    let modal = document.querySelector(isUpdate ? "#updateProviderForm" : "#addProviderForm");
    if (!modal) {
        console.error(`Không tìm thấy modal ${isUpdate ? "#updateProviderForm" : "#addProviderForm"}`);
        return false;
    }

    // Xóa thông báo lỗi cũ
    clearAllErrors(modal);

    // Lấy input fields
    let nameInput = modal.querySelector("#provider_name");
    let emailInput = modal.querySelector("#provider_email");
    let phoneInput = modal.querySelector("#provider_phone");

    // Kiểm tra tên nhà cung cấp
    if (!provider.full_name || typeof provider.full_name !== "string" || provider.full_name.trim() === "") {
        if (nameInput) showError(nameInput, "Tên nhà cung cấp không được để trống!");
        isValid = false;
    }

    // Kiểm tra email hợp lệ
    if (!provider.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(provider.email)) {
        if (emailInput) showError(emailInput, "Email không hợp lệ!");
        isValid = false;
    }

    // Kiểm tra số điện thoại hợp lệ
    if (!provider.phone_number || !isValidPhoneNumber(provider.phone_number)) {
        if (phoneInput) showError(phoneInput, "Số điện thoại không hợp lệ!");
        isValid = false;
    }

    // Kiểm tra trùng dữ liệu (chỉ kiểm tra nếu có API)
    let errors = await isDuplicateProvider(provider);
    if (errors) {
        if (errors.full_name && nameInput) showError(nameInput, errors.full_name);
        if (errors.email && emailInput) showError(emailInput, errors.email);
        if (errors.phone_number && phoneInput) showError(phoneInput, errors.phone_number);
        isValid = false;
    }

    return isValid;
}





