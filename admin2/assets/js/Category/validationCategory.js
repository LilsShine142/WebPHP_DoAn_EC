// Hiển thị lỗi ngay dưới ô nhập
function showError(input, message) {
    input.classList.add("is-invalid");
    let errorDiv = document.createElement("div");
    errorDiv.classList.add("invalid-feedback");
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
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
