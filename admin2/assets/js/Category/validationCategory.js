// Hiển thị lỗi ngay dưới ô nhập
function showError(input, message) {
    clearError(input); // Xóa lỗi cũ nếu có

    input.classList.add("is-invalid"); // Thêm class Bootstrap
    let errorDiv = document.createElement("div");
    errorDiv.classList.add("invalid-feedback"); // Bootstrap class
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
}

// Xóa thông báo lỗi cũ nếu có
function clearError(input) {
    input.classList.remove("is-invalid");
    let errorDiv = input.parentElement.querySelector(".invalid-feedback");
    if (errorDiv) {
        errorDiv.remove();
    }
}

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
}

// Hàm kiểm tra tên danh mục có hợp lệ không
function validateCategory(cateIdUpdate) {
    return new Promise((resolve) => {
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        let categoryName;
        console.log("ID danh mục cần cập nhật:", cateIdUpdate);
        if (cateIdUpdate) {
            categoryName = $("#cate_name");
            console.log("Đây là cập nhật danh mục.");
        } else {
            categoryName = $("#catename");
            console.log("Đây là thêm mới danh mục.");
        }
        let categoryValue = categoryName.val().trim(); // Gán giá trị sau khi xác định categoryName
        console.log("Tên danh mục nhập vào:", categoryValue);

        if (categoryValue === "") {
            showError(categoryName[0], "Tên danh mục không được để trống.");
            return resolve(false);
        }

        let apiCate = `${BASE_API_URL}/api/products/categories?name=${encodeURIComponent(categoryValue)}`;
        console.log("API kiểm tra danh mục:", apiCate);

        $.ajax({
            url: apiCate,
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                console.log("API Response Data:", response);
                console.log("id:", cateIdUpdate);

                // Nếu không có response.data hoặc response.data.name thì có thể thêm danh mục mới
                if (!response.success) {
                    console.log("Danh mục chưa tồn tại, có thể thêm mới.");
                    return resolve(true);
                }

                // Nếu tên danh mục tồn tại và trùng ID cập nhật thì hợp lệ
                if (cateIdUpdate && cateIdUpdate == response.data.id) {
                    console.log("Cập nhật danh mục hợp lệ.");
                    return resolve(true);
                }

                // Nếu tên danh mục đã tồn tại và ID không khớp, không thể thêm
                showError(categoryName[0], "Tên danh mục đã tồn tại.");
                return resolve(false);
            },
            error: function (xhr) {
                console.error("Lỗi khi kiểm tra danh mục:", xhr.responseText);
                alert("Có lỗi xảy ra khi kiểm tra danh mục!");
                resolve(false); // Tránh `reject(false)` gây lỗi khi `await validateCategory()`
            }
        });
    });
}
