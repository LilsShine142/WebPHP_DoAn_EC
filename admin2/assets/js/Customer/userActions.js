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
    function loadRoles(userRole) {
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
                    if (userRole.role_id) {
                        console.log("userRoleId:", userRole.role_id);
                        roleSelect.val(userRole.role_id);
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

    async function getProvinces() {
        let response = await fetch("https://provinces.open-api.vn/api/?depth=1");
        return await response.json();
    }
    async function getDistricts(provinceCode) {
        let response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        let data = await response.json();
        return data.districts;
    }

    async function getWards(districtCode) {
        let response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        let data = await response.json();
        return data.wards;
    }

    async function loadProvinces(selectedProvinceName) {
        let provinces = await getProvinces();
        let provinceSelect = document.getElementById("city_province");

        provinceSelect.innerHTML = '<option value="">Select province</option>';
        let selectedProvinceCode = "";

        provinces.forEach(province => {
            let option = document.createElement("option");
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);

            if (province.name === selectedProvinceName) {
                option.selected = true;
                selectedProvinceCode = province.code;
            }
        });

        return selectedProvinceCode;
    }
    async function loadDistricts(provinceCode, selectedDistrictName) {
        let districts = await getDistricts(provinceCode);
        let districtSelect = document.getElementById("district");

        districtSelect.innerHTML = '<option value="">Select district</option>';
        let selectedDistrictCode = "";

        districts.forEach(district => {
            let option = document.createElement("option");
            option.value = district.code;
            option.textContent = district.name;
            districtSelect.appendChild(option);

            if (district.name === selectedDistrictName) {
                option.selected = true;
                selectedDistrictCode = district.code;
            }
        });

        return selectedDistrictCode;
    }
    async function loadWards(districtCode, selectedWardName) {
        let wards = await getWards(districtCode);
        let wardSelect = document.getElementById("ward");

        wardSelect.innerHTML = '<option value="">Select ward</option>';

        wards.forEach(ward => {
            let option = document.createElement("option");
            option.value = ward.code;
            option.textContent = ward.name;
            wardSelect.appendChild(option);

            if (ward.name === selectedWardName) {
                option.selected = true;
            }
        });
    }

    //================================================== LẤY THÔNG TIN NGƯỜI DÙNG ====================================
    // Gọi API lấy danh sách user_roles, roles và addresses
    function fetchUserData(userId) {
        return Promise.all([
            $.ajax({
                url: `${BASE_API_URL}/api/users/${userId}`,
                type: 'GET',
                dataType: "json"
            }).catch(error => {
                console.error("Lỗi API user:", error);
                return { success: false, data: {} };
            }),

            $.ajax({
                url: `${BASE_API_URL}/api/users/user_roles?user_id=${userId}`,
                type: 'GET',
                dataType: "json"
            }).catch(error => {
                console.error("Lỗi API user_roles:", error);
                return { success: false, data: [] };
            }),

            $.ajax({
                url: `${BASE_API_URL}/api/users/addresses?user_id=${userId}`,
                type: 'GET',
                dataType: "json"
            }).catch(error => {
                console.error("Lỗi API addresses:", error);
                return { success: false, data: [] };
            })
        ]).then(([userInfor, userRolesRes, addressRes]) => {
            console.log("API userInfor:", userInfor);
            console.log("API user_roles:", userRolesRes);
            console.log("API addresses:", addressRes);

            if (!userInfor.success || !userRolesRes.success) {
                throw new Error("Lỗi khi lấy dữ liệu userInfor hoặc user_roles"); //Không có address cũng không sao
            }

            let roleId = userRolesRes.data[0]?.role_id || 0;
            console.log("Role ID:", roleId);

            // Nếu roleId hợp lệ (>0) thì gọi API lấy roles, nếu không thì trả về dữ liệu ngay
            if (roleId > 0) {
                return $.ajax({
                    url: `${BASE_API_URL}/api/users/roles/${roleId}`,
                    type: 'GET',
                    dataType: "json"
                }).then(rolesRes => {
                    console.log("API roles:", rolesRes);

                    if (!rolesRes.success) {
                        throw new Error("Lỗi khi lấy dữ liệu roles");
                    }

                    return {
                        userInfor: userInfor.data,
                        userRoles: userRolesRes.data,
                        roles: rolesRes.data,
                        addresses: addressRes.data
                    };
                });
            }
            // Nếu không có address
            else if (!addressRes.success) {
                return {
                    userInfor: userInfor.data,
                    userRoles: userRolesRes.data,
                    roles: rolesRes.data,
                    addresses: []
                };
            } else {
                // Nếu không có roleId, trả về dữ liệu ngay
                return {
                    userInfor: userInfor.data,
                    userRoles: userRolesRes.data,
                    roles: [],
                    addresses: addressRes.data
                };
            }
        }).catch(error => {
            console.error("Lỗi trong fetchUserData:", error);
            return { userInfor: {}, userRoles: [], roles: [], addresses: [] };
        });
    }

    // Ghép userInfor, usetRoles, roles, addresses vào 1 object userDataUpdate
    function mergeUserData(userInfor, roles, addresses) {

        return {
            id: userInfor.id || null,
            full_name: userInfor.full_name || "N/A",
            email: userInfor.email || "N/A",
            phone_number: userInfor.phone_number || "N/A",
            role_id: roles.id || null,
            role_name: roles.name || "N/A",
            city_province: addresses ? addresses.city_province : "N/A",
            district: addresses ? addresses.district : "N/A",
            ward: addresses ? addresses.ward : "N/A",
            street: addresses ? addresses.street : "N/A",
            apartment_number: addresses ? addresses.apartment_number : "N/A"
        };
    }

    // Gọi API để lấy thông tin người dùng
    function getUserDataInfor(userId) {
        return fetchUserData(userId).then(userData => {
            if (!userData) return null;
            console.log("userData:", userData);

            let userWithDetails = mergeUserData(userData.userInfor, userData.roles, userData.addresses);
            console.log("User with details:", userWithDetails);

            return userWithDetails;
        }).catch(error => {
            console.error("Lỗi khi lấy thông tin người dùng", error);
            return null;
        });
    }

    //====================================== Hiện modal và data khi bấm update khách hàng hoặc nhân viên ====================================
    // Lấy danh sách tất cả các nút có class "btn-update"
    let lastUserId = null;
    $(document).on("click", ".btn-update", function () {

        let userId = this.getAttribute("data-id");
        console.log("ID khách hàng cần cập nhật:", userId);
// Nếu id click trước đó khác với id vừa click thì xóa thông báo lỗi cũ
        if (lastUserId !== userId) {
            // Xóa thông báo lỗi cũ
            clearAllErrors();
        }

        lastUserId = userId; // Cập nhật ID trước đó
        // Gọi hàm lấy thông tin user
        getUserDataInfor(userId).then(userDataUpdate => {
            if (!userDataUpdate) {
                alert("Không tìm thấy khách hàng!");
                return;
            }

            console.log("userDataUpdate:", userDataUpdate);

            // Điền dữ liệu vào form
            $("#userId").val(userDataUpdate.id);
            $("#fullname").val(userDataUpdate.full_name);
            $("#email").val(userDataUpdate.email);
            $("#phone").val(userDataUpdate.phone_number);
            $("#password").val("");
            $("#street").val(userDataUpdate.street);
            $("#apartment_number").val(userDataUpdate.apartment_number);

            // Lấy role_id của user và load danh sách role
            loadRoles(userDataUpdate);

            // Hiển thị modal
            $("#updateModal").modal("show");

            // Xử lý tỉnh/huyện/xã
            let selectedProvince = userDataUpdate.city_province;
            let selectedDistrict = userDataUpdate.district;
            let selectedWard = userDataUpdate.ward;

            // Gọi API lấy tỉnh trước, sau đó load huyện và xã theo tỉnh/huyện đã chọn
            loadProvinces(selectedProvince).then(provinceCode => {
                if (provinceCode) {
                    loadDistricts(provinceCode, selectedDistrict).then(districtCode => {
                        if (districtCode) {
                            loadWards(districtCode, selectedWard);
                        }
                    });
                }
            });

            // Xử lý sự kiện khi thay đổi tỉnh
            $("#city_province").off("change").on("change", function () {
                let provinceCode = this.value;
                loadDistricts(provinceCode, "").then(() => {
                    $("#ward").html('<option value="">Select ward</option>'); // Reset xã
                });
            });

            // Xử lý sự kiện khi thay đổi huyện
            $("#district").off("change").on("change", function () {
                let districtCode = this.value;
                loadWards(districtCode, "");
            });

        }).catch(error => {
            console.error("Lỗi khi cập nhật dữ liệu người dùng:", error);
            alert("Lỗi khi tải dữ liệu người dùng!");
        });
    });



    //==================================== CẬP NHẬT THÔNG TIN KHÁCH HÀNG HOẶC NHÂN VIÊN ====================================    
    //Cập nhật thông tin khách hàng hoặc nhân viên
    const customerForm = document.getElementById("customerForm");
    const saveButton = document.getElementById("saveUser");

    saveButton.addEventListener("click", function () {
        //isValid = true
        if (!validateForm(true)) {
            return;
        }
        console.log("===> Bắt đầu sự kiện click");
        let userIdUpdate = document.getElementById("userId").value;
        console.log("ID khách hàng cần cập nhật:", userIdUpdate);
        let isValid = true;
        console.log("Click save button");

        // Xóa thông báo lỗi cũ trước khi kiểm tra
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        if (isValid) {
            // Lấy dữ liệu từ form
            const password = document.getElementById("password").value.trim();
            let userData = {
                full_name: document.getElementById("fullname").value.trim(),
                email: document.getElementById("email").value.trim(),
                phone_number: document.getElementById("phone").value.trim()
            };
            if (password.length > 0) {
                userData.password = password; // Chỉ thêm mật khẩu nếu có nhập
            }

            let roleData = {
                role_id: document.getElementById("role").value
            };

            let addressData = {
                city_province: document.getElementById("city_province").selectedOptions[0].text,
                district: document.getElementById("district").selectedOptions[0].text,
                ward: document.getElementById("ward").selectedOptions[0].text,
                street: document.getElementById("street").value.trim(),  // Nếu là input thì giữ nguyên
                apartment_number: document.getElementById("apartment_number").value.trim()
            };
            console.log("addressData:", addressData);

            // API endpoints
            let apiUserInforUpdate = `${BASE_API_URL}/api/users/${userIdUpdate}`;
            let apiUserRoleUpdate = `${BASE_API_URL}/api/users/user_roles?user_id=${userIdUpdate}`;
            let apiUserAddressUpdate = `${BASE_API_URL}/api/users/addresses?user_id=${userIdUpdate}`;

            // Cập nhật user trước
            $.ajax({
                url: apiUserInforUpdate,
                type: 'PUT',
                data: JSON.stringify(userData),
                contentType: "application/json",
                success: function (response) {
                    console.log("Cập nhật user thành công:", response);
                    if (response.success) {
                        // Cập nhật user_role tiếp theo
                        $.ajax({
                            url: apiUserRoleUpdate,
                            type: 'PUT',
                            data: JSON.stringify(roleData),
                            contentType: "application/json",
                            success: function (response) {
                                console.log("Cập nhật vai trò thành công:", response);
                                if (response.success) {
                                    // Cập nhật địa chỉ cuối cùng
                                    $.ajax({
                                        url: apiUserAddressUpdate,
                                        type: 'PUT',
                                        data: JSON.stringify(addressData),
                                        contentType: "application/json",
                                        success: function (response) {
                                            console.log("Cập nhật địa chỉ thành công:", response);
                                            if (response.success) {
                                                toast("Cập nhật thông tin thành công!", "success");
                                                $("#updateModal").modal("hide"); // Đóng modal

                                                // Cập nhật lại danh sách user mà không load lại trang
                                                updateUserRow(userIdUpdate, userData);
                                            }
                                        },
                                        error: function (xhr) {
                                            alert("Có lỗi xảy ra khi cập nhật địa chỉ!");
                                            console.error("AJAX Error:", xhr.responseText);
                                        }
                                    });
                                }
                            },
                            error: function (xhr) {
                                alert("Có lỗi xảy ra khi cập nhật vai trò!");
                                console.error("AJAX Error:", xhr.responseText);
                            }
                        });
                    }
                },
                error: function (xhr) {
                    alert("Có lỗi xảy ra khi cập nhật thông tin người dùng!");
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        }
    });

    // Hàm cập nhật lại dữ liệu trên bảng mà không cần reload trang
    function updateUserRow(userId, userData) {
        let userRow = document.querySelector(`#user-${userId}`);
        if (userRow) {
            userRow.querySelector(".user-name").textContent = userData.full_name;
            userRow.querySelector(".user-email").textContent = userData.email;
            userRow.querySelector(".user-phone").textContent = userData.phone_number;
            userRow.querySelector(".user-role").textContent = document.getElementById("role").selectedOptions[0].text;
        } else {
            console.warn("Không tìm thấy hàng user trong bảng!");
        }
    }

    //==================================== ĐÓNG FORM ====================================
    // const closeButton = document.getElementById("close-button");
    // // Gán sự kiện xóa lỗi khi bấm nút "Đóng"
    // closeButton.addEventListener("click", function () {
    //     clearAllErrors();
    // }
    // );


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
    //====================================================================

});
