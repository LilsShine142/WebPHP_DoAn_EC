document.addEventListener("DOMContentLoaded", function () {
    // Lấy danh sách tất cả các nút có class "btn-view"
    document.querySelectorAll(".btn-view").forEach(function (btn) {
        btn.addEventListener("click", function () {
            // Lấy modal theo ID
            var myModal = new bootstrap.Modal(document.getElementById("modalView"));
            myModal.show();
        });
    });

    //==================================== CALL API LẤY DANH SÁCH TỈNH/THÀNH PHỐ,.... ==================================== 
    // Lấy danh sách tỉnh/thành phố khi trang tải
    fetch("https://provinces.open-api.vn/api/?depth=1")
        .then(response => response.json())
        .then(data => {
            let provinceSelect = document.getElementById("province");
            data.forEach(province => {
                let option = document.createElement("option");
                option.value = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Lỗi khi tải danh sách tỉnh:", error));

    // Khi chọn tỉnh/thành phố, lấy danh sách quận/huyện
    document.getElementById("province").addEventListener("change", function () {
        let provinceCode = this.value;
        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                let districtSelect = document.getElementById("district");
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                data.districts.forEach(district => {
                    let option = document.createElement("option");
                    option.value = district.code;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });

                // Xóa danh sách phường/xã khi thay đổi tỉnh
                document.getElementById("ward").innerHTML = '<option value="">Chọn phường/xã</option>';
            })
            .catch(error => console.error("Lỗi khi tải danh sách quận/huyện:", error));
    });

    // Khi chọn quận/huyện, lấy danh sách phường/xã
    document.getElementById("district").addEventListener("change", function () {
        let districtCode = this.value;
        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                let wardSelect = document.getElementById("ward");
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                data.wards.forEach(ward => {
                    let option = document.createElement("option");
                    option.value = ward.code;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Lỗi khi tải danh sách phường/xã:", error));
    });

    // Lấy danh sách tất cả các nút có class "btn-update"
    document.querySelectorAll(".btn-update").forEach(function (btn) {
        btn.addEventListener("click", function () {
            // Lấy ID khách hàng
            let customerId = this.getAttribute("data-id");
            console.log("ID khách hàng cần cập nhật:", customerId);

            // Gửi AJAX request để lấy thông tin khách hàng theo ID
            $.ajax({
                url: "api/controllers/user/UserController.php", // API lấy dữ liệu
                type: "GET",
                data: { id: customerId },
                dataType: "json", 
                success: function (response) {
                    if (response.id) {
                        // Điền dữ liệu vào form
                        $("#customerId").val(response.id);
                        $("#fullname").val(response.fullname);
                        $("#email").val(response.email);
                        $("#phone").val(response.phone);
                        $("#address").val(response.address);
                        $("#status").val(response.status);

                        // Hiển thị modal
                        $("#updateModal").modal("show");
                    } else {
                        alert("Không tìm thấy khách hàng!");
                    }
                },
                error: function () {
                    alert("Lỗi khi lấy dữ liệu khách hàng! có id: " + customerId);
                    //Tạm thời vẫn hiện modal
                    var myModal = new bootstrap.Modal(document.getElementById("formModal"));
                    myModal.show();
                }
            });

            // Xử lý khi bấm submit form
            $("#updateForm").off("submit").on("submit", function (e) {
                e.preventDefault();
                alert("Cập nhật thành công! (Chưa xử lý lưu vào DB)");
                $("#updateModal").modal("hide");
            });
        });
    });


    //==================================== CẬP NHẬT VÀ THÊM MỚI THÔNG TIN KHÁCH HÀNG ====================================    
    //Cập nhật và thêm cutomer
    const customerForm = document.getElementById("customerForm");
    const saveButton = document.getElementById("saveCustomer");

    saveButton.addEventListener("click", function () {
        let isValid = true;

        // Xóa thông báo lỗi cũ trước khi kiểm tra
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        // Kiểm tra từng trường dữ liệu
        const fields = [
            { id: "fullname", message: "Vui lòng nhập họ và tên" },
            { id: "email", message: "Vui lòng nhập email hợp lệ", type: "email" },
            { id: "phone", message: "Vui lòng nhập số điện thoại hợp lệ", type: "phone" },
            { id: "province", message: "Vui lòng chọn tỉnh/thành phố" },
            { id: "district", message: "Vui lòng chọn quận/huyện" },
            { id: "ward", message: "Vui lòng chọn phường/xã" },
            { id: "address", message: "Vui lòng nhập địa chỉ cụ thể" }
        ];

        fields.forEach(field => {
            let input = document.getElementById(field.id);
            let value = input.value.trim();
            let errorMessage = "";

            // Kiểm tra theo từng loại dữ liệu
            if (!value) {
                errorMessage = field.message;
            } else if (field.type === "email" && !validateEmail(value)) {
                errorMessage = "Email không hợp lệ";
            } else if (field.type === "phone" && !validatePhone(value)) {
                errorMessage = "Số điện thoại không hợp lệ";
            }

            // Nếu có lỗi, hiển thị thông báo dưới ô nhập
            if (errorMessage) {
                showError(input, errorMessage);
                isValid = false;
            }
        });

        // Nếu tất cả dữ liệu hợp lệ, cập nhật thông tin

        if (isValid) {
            let customerId = document.getElementById("customerId").value.trim();
            let isUpdating = customerId !== ""; // Nếu có ID thì là cập nhật

            let formData = {
                fullname: document.getElementById("fullname").value.trim(),
                email: document.getElementById("email").value.trim(),
                phone: document.getElementById("phone").value.trim(),
                province: document.getElementById("province").value.trim(),
                district: document.getElementById("district").value.trim(),
                ward: document.getElementById("ward").value.trim(),
                address: document.getElementById("address").value.trim()
            };

            let apiUrl = "api/controllers/user/UserController.php";
            let successMessage = isUpdating ? "Cập nhật thông tin thành công!" : "Thêm khách hàng mới thành công!";
            let httpMethod = isUpdating ? "PUT" : "POST";

            if (isUpdating) {
                // Nếu cập nhật, thêm ID vào formData
                formData.id = customerId;
            }

            $.ajax({
                url: apiUrl + (isUpdating ? `?id=${customerId}` : ""), // Gửi ID qua URL nếu cập nhật
                type: httpMethod,
                data: JSON.stringify(formData),
                contentType: "application/json",
                success: function (response) {
                    try {
                        let res = JSON.parse(response); // Đảm bảo dữ liệu phản hồi là JSON hợp lệ
                        if (res.success) {
                            showSuccessMessage(successMessage);
                            $("#updateModal").modal("hide"); // Đóng modal
                            location.reload(); // Tải lại trang để cập nhật danh sách khách hàng
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

//========================XÓA NGƯỜI DÙNG ========================
    // Lấy danh sách tất cả các nút có class "btn-delete
    document.querySelectorAll(".btn-delete").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let customerId = this.getAttribute("data-id");
            if (confirm("Bạn có chắc chắn muốn xóa khách hàng này?")) {
                $.ajax({
                    //Gửi api xóa người dùng
                    url: "api/controllers/user/UserController.php?id=" + customerId,
                    type: "DELETE",
                    success: function (response) {
                        try {
                            let res = JSON.parse(response);
                            if (res.success) {
                                alert("Xóa khách hàng thành công!");
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
