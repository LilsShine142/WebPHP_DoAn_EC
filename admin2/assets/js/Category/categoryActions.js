document.addEventListener("DOMContentLoaded", function () {
    // Xử lý hiển thị modal khi bấm nút xem danh mục
    document.querySelectorAll(".btn-view").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var myModal = new bootstrap.Modal(document.getElementById("modalView"));
            myModal.show();
        });
    });

    // Xử lý cập nhật danh mục
    document.querySelectorAll(".btn-update").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let categoryId = this.getAttribute("data-id");
            console.log("ID danh mục cần cập nhật:", categoryId);

            $.ajax({
                url: "api/controllers/category/CategoryController.php",
                type: "GET",
                data: { id: categoryId },
                dataType: "json",
                success: function (response) {
                    if (response.id) {
                        $("#categoryId").val(response.id);
                        $("#categoryName").val(response.name);
                        $("#categoryStatus").val(response.status);
                        $("#updateModal").modal("show");
                    } else {
                        alert("Không tìm thấy danh mục!");
                    }
                },
                error: function () {
                    alert("Lỗi khi lấy dữ liệu danh mục! ID: " + categoryId);
                    var myModal = new bootstrap.Modal(document.getElementById("formModal"));
                    myModal.show();
                }
            });
        });
    });

    // Xử lý lưu danh mục
    const saveButton = document.getElementById("saveCategory");
    saveButton.addEventListener("click", function () {
        let isValid = true;
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        const fields = [
            { id: "categoryName", message: "Vui lòng nhập tên danh mục" }
        ];

        fields.forEach(field => {
            let input = document.getElementById(field.id);
            let value = input.value.trim();
            if (!value) {
                showError(input, field.message);
                isValid = false;
            }
        });

        if (isValid) {
            let categoryId = document.getElementById("categoryId").value.trim();
            let isUpdating = categoryId !== "";

            let formData = {
                name: document.getElementById("categoryName").value.trim(),
                status: document.getElementById("categoryStatus").value.trim()
            };

            let apiUrl = "api/controllers/category/CategoryController.php";
            let successMessage = isUpdating ? "Cập nhật danh mục thành công!" : "Thêm danh mục mới thành công!";
            let httpMethod = isUpdating ? "PUT" : "POST";

            if (isUpdating) {
                formData.id = categoryId;
            }

            $.ajax({
                url: apiUrl + (isUpdating ? `?id=${categoryId}` : ""),
                type: httpMethod,
                data: JSON.stringify(formData),
                contentType: "application/json",
                success: function (response) {
                    try {
                        let res = JSON.parse(response);
                        if (res.success) {
                            alert(successMessage);
                            $("#updateModal").modal("hide");
                            location.reload();
                        } else {
                            alert("Lỗi: " + res.message);
                        }
                    } catch (e) {
                        alert("Lỗi phản hồi từ server!");
                        console.error("JSON Error:", e);
                    }
                },
                error: function (xhr) {
                    alert("Có lỗi xảy ra khi xử lý yêu cầu!");
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        }
    });

    // Xóa danh mục
    document.querySelectorAll(".btn-delete").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let categoryId = this.getAttribute("data-id");
            if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
                $.ajax({
                    url: "api/controllers/category/CategoryController.php?id=" + categoryId,
                    type: "DELETE",
                    success: function (response) {
                        try {
                            let res = JSON.parse(response);
                            if (res.success) {
                                alert("Xóa danh mục thành công!");
                                location.reload();
                            } else {
                                alert("Lỗi: " + res.message);
                            }
                        } catch (e) {
                            alert("Lỗi phản hồi từ server!");
                            console.error("JSON Error:", e);
                        }
                    },
                    error: function (xhr) {
                        alert("Có lỗi xảy ra khi xử lý yêu cầu!");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            }
        });
    });
});
