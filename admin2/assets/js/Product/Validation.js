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

function validateProductVariation(data) {
    // Kiểm tra ID
    if (!data.id) {
        toast("ID sản phẩm không hợp lệ!", "error", true);
        return false;
    }

    // Kiểm tra kích thước đồng hồ (watch_size_mm)
    if (data.watch_size_mm <= 0) {
        toast("Kích thước đồng hồ phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra màu đồng hồ (watch_color)
    if (!data.watch_color) {
        toast("Vui lòng chọn màu đồng hồ!", "error", true);
        return false;
    }

    // Kiểm tra số lượng tồn kho (stock_quantity)
    if (data.stock_quantity < 0) {
        toast("Số lượng tồn kho không được âm!", "error", true);
        return false;
    }

    // Kiểm tra giá bán (price_cents)
    if (data.price_cents <= 0) {
        toast("Giá bán phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra giá gốc (base_price_cents)
    if (data.base_price_cents < 0) {
        toast("Giá gốc không được âm!", "error", true);
        return false;
    }

    // Kiểm tra tên ảnh (image_name)
    if (!data.image_name) {
        toast("Vui lòng chọn hình ảnh sản phẩm!", "error", true);
        return false;
    }

    // Kiểm tra kích thước màn hình (display_size_mm)
    if (data.display_size_mm <= 0) {
        toast("Kích thước màn hình phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra loại màn hình (display_type)
    if (!data.display_type) {
        toast("Vui lòng nhập loại màn hình!", "error", true);
        return false;
    }

    // Kiểm tra độ phân giải màn hình
    if (data.resolution_h_px <= 0 || data.resolution_w_px <= 0) {
        toast("Độ phân giải phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra dung lượng RAM
    if (data.ram_bytes < 0) {
        toast("Dung lượng RAM không được âm!", "error", true);
        return false;
    }

    // Kiểm tra dung lượng ROM
    if (data.rom_bytes < 0) {
        toast("Dung lượng ROM không được âm!", "error", true);
        return false;
    }

    // Kiểm tra kết nối (connectivity)
    if (!data.connectivity) {
        toast("Vui lòng nhập thông tin kết nối!", "error", true);
        return false;
    }

    // Kiểm tra dung lượng pin (battery_life_mah)
    if (data.battery_life_mah <= 0) {
        toast("Dung lượng pin phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra chỉ số chống nước
    if (data.water_resistance_value < 0) {
        toast("Chỉ số chống nước không hợp lệ!", "error", true);
        return false;
    }

    // Kiểm tra đơn vị chống nước (water_resistance_unit)
    if (!data.water_resistance_unit) {
        toast("Vui lòng nhập đơn vị chống nước!", "error", true);
        return false;
    }

    // Kiểm tra cảm biến (sensor)
    if (!data.sensor) {
        toast("Vui lòng nhập thông tin cảm biến!", "error", true);
        return false;
    }

    // Kiểm tra chất liệu vỏ (case_material)
    if (!data.case_material) {
        toast("Vui lòng nhập chất liệu vỏ!", "error", true);
        return false;
    }

    // Kiểm tra chất liệu dây đeo (band_material)
    if (!data.band_material) {
        toast("Vui lòng nhập chất liệu dây đeo!", "error", true);
        return false;
    }

    // Kiểm tra kích thước dây đeo (band_size_mm)
    if (data.band_size_mm <= 0) {
        toast("Kích thước dây đeo phải lớn hơn 0!", "error", true);
        return false;
    }

    // Kiểm tra màu dây đeo (band_color)
    if (!data.band_color) {
        toast("Vui lòng chọn màu dây đeo!", "error", true);
        return false;
    }

    // Kiểm tra trọng lượng (weight_milligrams)
    if (data.weight_milligrams <= 0) {
        toast("Trọng lượng phải lớn hơn 0!", "error", true);
        return false;
    }

    return true; // Nếu tất cả dữ liệu hợp lệ
}

function validateProduct(product) {
    let isValid = true;
    let modal = document.querySelector("#modalUpdate");

    // Kiểm tra tên sản phẩm
    if (!product.name || typeof product.name !== "string" || product.name.trim() === "") {
        showError(modal.querySelector("#product_name"), "Tên sản phẩm không được để trống!");
        isValid = false;
    }

    // Kiểm tra brand_id
    if (!product.brand_id || isNaN(product.brand_id)) {
        showError(modal.querySelector("#brand_id"), "Brand ID phải là một số hợp lệ!");
        isValid = false;
    }

    // Kiểm tra model
    if (!product.model || typeof product.model !== "string" || product.model.trim() === "") {
        showError(modal.querySelector("#model"), "Model không được để trống!");
        isValid = false;
    }

    // Kiểm tra category_id
    if (!product.category_id || isNaN(product.category_id)) {
        showError(modal.querySelector("#category_id"), "Category ID phải là một số hợp lệ!");
        isValid = false;
    }

    // Kiểm tra description
    if (!product.description || typeof product.description !== "string" || product.description.trim() === "") {
        showError(modal.querySelector("#description"), "Mô tả sản phẩm không được để trống!");
        isValid = false;
    }

    // Kiểm tra image_name
    if (!product.image_name || typeof product.image_name !== "string" || product.image_name.trim() === "") {
        showError(modal.querySelector("#image_name"), "Tên hình ảnh không được để trống!");
        isValid = false;
    }

    // Kiểm tra stop_selling
    if (typeof product.stop_selling !== "boolean") {
        showError(modal.querySelector("#stop_selling"), "Trường Stop Selling phải là true hoặc false!");
        isValid = false;
    }

    return isValid;
}
