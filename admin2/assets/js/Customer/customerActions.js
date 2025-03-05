document.addEventListener("DOMContentLoaded", function () {
    // =============================== HÀM LẤY THÔNG TIN USER ===============================
    function getUserAddressInfo(userId) {
        $.ajax({
            url: `${BASE_API_URL}/api/users/addresses?user_id=${userId}`,
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                console.log("API Response:", response);
                console.log("API Response Data:", response.data);
                if (response.success) {
                    loadUserDataToModal(response.data);
                } else {
                    console.error("Lỗi khi tải dữ liệu");
                }
            },
            error: function (error) {
                console.error("Lỗi API:", error);
            }
        });
    }
    //================================= HÀM HIỂN THỊ VIEW USER VÀO BẢNG MODAL ==================================
    function loadUserDataToModal(users) {
        let tableBody = document.getElementById("data-viewuser-table");

        console.log("Dữ liệu user trước khi kiểm tra:", users);
        console.log("Kiểu dữ liệu users:", typeof users);

        // Xóa dữ liệu cũ trước khi thêm dữ liệu mới
        tableBody.innerHTML = "";

        if (!users || Object.keys(users).length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No data available</td></tr>';
            return;
        }

        let row = `
        <tr>
            <td>${users.user_id || "N/A"}</td>
            <td>${users.name || "N/A"}</td>
            <td>${users.phone_number || "N/A"}</td>
            <td>${users.street || "N/A"}</td>
            <td>${users.apartment_number || "N/A"}</td>
            <td>${users.ward || "N/A"}</td>
            <td>${users.district || "N/A"}</td>
            <td>${users.city_province || "N/A"}</td>
        </tr>
    `;

        tableBody.innerHTML = row;

        // Hiển thị modal
        $("#modalView").modal("show");
    }

    //================================================== XEM CHI TIẾT KHÁCH HÀNG hoặc NHÂN VIÊN ====================================
    // Lấy danh sách tất cả các nút có class "btn-view"
    $(document).on('click', '.btn-view', function () {
        let userId = $(this).data('id'); // Lấy ID của user từ button
        console.log("User ID:", userId); // Debug xem có lấy đúng ID không
        getUserAddressInfo(userId); // Gọi hàm lấy thông tin user
        // Hiển thị modal
        $('#modalView').modal('show');
    });
    // ===================================== LẤY DANH SÁCH ROLE ====================================
    // Hàm load danh sách role vào dropdown
    function loadRoles(userRoleId) {
        $.ajax({
            url: `${BASE_API_URL}/api/users/roles`,
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let roles = response.data;
                    let roleSelect = $("#role");

                    roleSelect.empty(); // Xóa danh sách cũ tránh bị lặp
                    roles.forEach(role => {
                        roleSelect.append(`<option value="${role.id}">${role.name}</option>`);
                    });

                    // Nếu đang sửa user, chọn role tương ứng
                    if (userRoleId) {
                        roleSelect.val(userRoleId);
                    }
                } else {
                    alert("Lỗi khi lấy danh sách role: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Lỗi:", xhr.responseText);
                alert("Có lỗi xảy ra: " + xhr.responseText);
            }
        });
    }

    //=========================== Hàm hiển thị thông báo toast =====================================
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


    //====================================== Hiện modal và data khi bấm update khách hàng hoặc nhân viên ====================================
    // Lấy danh sách tất cả các nút có class "btn-update"
    $(document).on("click", ".btn-update", function () {
        let userId = this.getAttribute("data-id");
        console.log("ID khách hàng cần cập nhật:", userId);

        // Tạo danh sách promise cho các API call
        let userInfo = fetch(`${BASE_API_URL}/api/users/${userId}`).then(res => res.json());
        let userRole = fetch(`${BASE_API_URL}/api/users/user_roles?user_id=${userId}`).then(res => res.json());
        //Tạm thời chưa có API lấy địa chỉ của khách hàng
        let userAddress = fetch(`${BASE_API_URL}/api/users/addresses`).then(res => res.json());

        // Chạy tất cả API song song
        Promise.all([userInfo, userRole, userAddress])
            .then(([userData, roleData, addressData]) => {
                console.log("Dữ liệu khách hàng:", userData);
                console.log("Dữ liệu vai trò:", roleData);
                console.log("Dữ liệu địa chỉ:", addressData);

                if (userData.data.id) {
                    // Điền dữ liệu vào form
                    $("#userId").val(userData.data.id);
                    $("#fullname").val(userData.data.full_name);
                    $("#email").val(userData.data.email);
                    $("#phone").val(userData.data.phone_number);
                    $("#password").val("");
                    console.log("roleData:", roleData);
                    // Lấy role_id của user và load danh sách role
                    let userRoleId = roleData.data[0] ? roleData.data[0].role_id : null;
                    console.log("Role roleData.data:", roleData.data[0]);
                    console.log("Role role_id:", roleData.data[0].role_id);
                    console.log("Role ID:", userRoleId);
                    loadRoles(userRoleId);

                    // Điền dữ liệu địa chỉ
                    $("#province").val(addressData.data.province);
                    $("#district").val(addressData.data.district);
                    $("#ward").val(addressData.data.ward);
                    $("#address").val(addressData.data.address);

                    // Hiển thị modal
                    $("#updateModal").modal("show");
                } else {
                    alert("Không tìm thấy khách hàng!");
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy dữ liệu:", error);
                alert("Lỗi khi lấy dữ liệu khách hàng!");
            });
    });


    //==================================== CẬP NHẬT VÀ THÊM MỚI THÔNG TIN KHÁCH HÀNG HOẶC NHÂN VIÊN ====================================    
    //Cập nhật và thêm cutomer
    const customerForm = document.getElementById("customerForm");
    const saveButton = document.getElementById("saveCustomer");

    saveButton.addEventListener("click", function () {
        let userIdUpdate = document.getElementById("userId").value;
        console.log("ID khách hàng cần cập nhật:", userIdUpdate);
        let isValid = true;
        console.log("Click save button");
        // Xóa thông báo lỗi cũ trước khi kiểm tra
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        // if (!validateForm()){
        //     return;
        // }

        if (isValid) {
            // Kiểm tra nếu mật khẩu không rỗng mới thêm vào dữ liệu gửi đi
            const password = document.getElementById("password").value.trim();
            console.log("password", password);
            let formData = {
                full_name: document.getElementById("fullname").value.trim(),
                email: document.getElementById("email").value.trim(),
                phone_number: document.getElementById("phone").value.trim(),
                role_id: document.getElementById("role").value,
                // province: document.getElementById("province").value.trim(),
                // district: document.getElementById("district").value.trim(),
                // ward: document.getElementById("ward").value.trim(),
                // address: document.getElementById("address").value.trim()
            };
            if (password.length > 0) {
                formData.password = password;
            }
            console.log("formData:", formData);

            let apiUrl = `${BASE_API_URL}/api/users/${userIdUpdate}`;

            $.ajax({
                // url: apiUrl + (isUpdating ? `?id=${customerId}` : ""), // Gửi ID qua URL nếu cập nhật
                url: apiUrl,
                type: 'PUT',
                data: JSON.stringify(formData),
                contentType: "application/json",
                success: function (response) {
                    console.log("response", response);
                    if (response.success) {
                        toast("Cập nhật thông tin thành công!", "success");
                        $("#updateModal").modal("hide"); // Đóng modal
                        //location.reload(); // Tải lại trang để cập nhật danh sách khách hàng
                        //Cập nhật lại danh sách user mà không load lại trang
                        let userRow = document.querySelector(`#user-${userIdUpdate}`);
                        if (userRow) {
                            userRow.querySelector(".user-name").textContent = formData.full_name;
                            userRow.querySelector(".user-email").textContent = formData.email;
                            userRow.querySelector(".user-phone").textContent = formData.phone_number;
                            userRow.querySelector(".user-role").textContent = document.getElementById("role").selectedOptions[0].text;
                        } else {
                            console.warn("Không tìm thấy hàng user trong bảng!");
                        }
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
    // ============================== THÊM NGƯỜI DÙNG ==============================
    // Lấy danh sách tất cả các nút có class "btn-add"

});
