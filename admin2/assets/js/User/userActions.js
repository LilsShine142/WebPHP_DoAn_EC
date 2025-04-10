document.addEventListener("DOMContentLoaded", function () {
    // =============================== HÀM LẤY THÔNG TIN USER (lấy địa chỉ để xem chi tiết)===============================
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
    // // Gọi API lấy danh sách user_roles, roles và addresses
    // function fetchUserData(userId) {
    //     return Promise.all([
    //         $.ajax({
    //             url: `${BASE_API_URL}/api/users/${userId}`,
    //             type: 'GET',
    //             dataType: "json"
    //         }).catch(error => {
    //             console.error("Lỗi API user:", error);
    //             return { success: false, data: {} };
    //         }),

    //         $.ajax({
    //             url: `${BASE_API_URL}/api/users/user_roles?user_id=${userId}`,
    //             type: 'GET',
    //             dataType: "json"
    //         }).catch(error => {
    //             console.error("Lỗi API user_roles:", error);
    //             return { success: false, data: [] };
    //         }),

    //         $.ajax({
    //             url: `${BASE_API_URL}/api/users/addresses?user_id=${userId}`,
    //             type: 'GET',
    //             dataType: "json"
    //         }).catch(error => {
    //             console.error("Lỗi API addresses:", error);
    //             return { success: false, data: [] };
    //         })
    //     ]).then(([userInfor, userRolesRes, addressRes]) => {
    //         console.log("API userInfor:", userInfor);
    //         console.log("API user_roles:", userRolesRes);
    //         console.log("API addresses:", addressRes);

    //         if (!userInfor.success || !userRolesRes.success) {
    //             throw new Error("Lỗi khi lấy dữ liệu userInfor hoặc user_roles"); //Không có address cũng không sao
    //         }

    //         let roleId = userRolesRes.data[0]?.role_id || 0;
    //         console.log("Role ID:", roleId);

    //         // Nếu roleId hợp lệ (>0) thì gọi API lấy roles, nếu không thì trả về dữ liệu ngay
    //         if (roleId > 0) {
    //             return $.ajax({
    //                 url: `${BASE_API_URL}/api/users/roles/${roleId}`,
    //                 type: 'GET',
    //                 dataType: "json"
    //             }).then(rolesRes => {
    //                 console.log("API roles:", rolesRes);

    //                 if (!rolesRes.success) {
    //                     throw new Error("Lỗi khi lấy dữ liệu roles");
    //                 }

    //                 return {
    //                     userInfor: userInfor.data,
    //                     userRoles: userRolesRes.data,
    //                     roles: rolesRes.data,
    //                     addresses: addressRes.data
    //                 };
    //             });
    //         }
    //         // Nếu không có address
    //         else if (!addressRes.success) {
    //             return {
    //                 userInfor: userInfor.data,
    //                 userRoles: userRolesRes.data,
    //                 roles: rolesRes.data,
    //                 addresses: []
    //             };
    //         } else {
    //             // Nếu không có roleId, trả về dữ liệu ngay
    //             return {
    //                 userInfor: userInfor.data,
    //                 userRoles: userRolesRes.data,
    //                 roles: [],
    //                 addresses: addressRes.data
    //             };
    //         }
    //     }).catch(error => {
    //         console.error("Lỗi trong fetchUserData:", error);
    //         return { userInfor: {}, userRoles: [], roles: [], addresses: [] };
    //     });
    // }

    // // Ghép userInfor, usetRoles, roles, addresses vào 1 object userDataUpdate
    // function mergeUserData(userInfor, roles, addresses) {

    //     return {
    //         id: userInfor.id || null,
    //         full_name: userInfor.full_name || "N/A",
    //         email: userInfor.email || "N/A",
    //         phone_number: userInfor.phone_number || "N/A",
    //         role_id: roles.id || null,
    //         role_name: roles.name || "N/A",
    //         city_province: addresses ? addresses.city_province : "N/A",
    //         district: addresses ? addresses.district : "N/A",
    //         ward: addresses ? addresses.ward : "N/A",
    //         street: addresses ? addresses.street : "N/A",
    //         apartment_number: addresses ? addresses.apartment_number : "N/A"
    //     };
    // }

    // // Gọi API để lấy thông tin người dùng
    // function getUserDataInfor(userId) {
    //     return fetchUserData(userId).then(userData => {
    //         if (!userData) return null;
    //         console.log("userData:", userData);

    //         let userWithDetails = mergeUserData(userData.userInfor, userData.roles, userData.addresses);
    //         console.log("User with details:", userWithDetails);

    //         return userWithDetails;
    //     }).catch(error => {
    //         console.error("Lỗi khi lấy thông tin người dùng", error);
    //         return null;
    //     });
    // }




    // function filterData(keyValue = "", extraParams = "") {
    //     let apiUrl = `${BASE_API_URL}/api/users?full_name=${encodeURIComponent(keyValue)}`;
    //     console.log("API URL:", apiUrl);
    //     if (extraParams) {
    //         apiUrl += `&${extraParams}`;
    //     }
    //     console.log("API URL with extra params:", apiUrl);

    //     $.ajax({
    //         url: apiUrl,
    //         type: "GET",
    //         success: function (response) {
    //             console.log("Filtered Data:", response);
    //             renderUserList(response.data); // Hiển thị lại bảng với dữ liệu mới
    //         },
    //         error: function (err) {
    //             console.error("Error fetching data:", err);
    //         },
    //     });
    // }

    // // Hàm cập nhật danh sách người dùng
    // function renderUserList(users) {
    //     let tableBody = document.getElementById('data-table');
    //     console.log("Danh sách người dùng:", users);
    //     console.log("table", tableBody);
    //     // Xóa nội dung cũ
    //     tableBody.innerHTML = "";
    //     let urlParams = new URLSearchParams(window.location.search);
    //     let type = urlParams.get('type');
    //     let isEmployeeList = type === 'employee';
    //     console.log("isEmployeeList:", isEmployeeList);
    //     if (users.length === 0) {
    //         tableBody.innerHTML = `<tr><td colspan='8' class='text-center'>No data available</td></tr>`;
    //     } else {
    //         tableBody.innerHTML = users.map(user => `
    //     <tr id="user-${user.id}">
    //         <td>${user.id}</td>
    //         <td class="user-name">${user.full_name}</td>
    //         <td class="user-email">${user.email}</td>
    //         <td class="user-phone">${user.phone_number}</td>
    //         ${isEmployeeList ? `<td class="user-role">${user.role_name}</td>` : ''}
    //         <td class="user-status">${user.status}</td>
    //         <td>${user.created_at}</td>
    //         <td>
    //             <button class="btn btn-info btn-view" data-id="${user.id}">
    //                 <i class="fas fa-eye"></i>
    //             </button>
    //             <button class="btn btn-warning btn-update" data-id="${user.id}">
    //                 <i class="fas fa-edit"></i>
    //             </button>
    //             <button class="btn btn-danger btn-delete" data-id="${user.id}">
    //                 <i class="fas fa-trash"></i>
    //             </button>
    //         </td>
    //     </tr>
    // `).join('');
    //     }
    // }


// ====================================== RENDER RA BẢNG DỮ LIỆU =========================================
    


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






    // Cấu hình
    const USER_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 10,
        DEFAULT_EXTRA_PARAMS: "" // Sẽ được thêm type từ URL
    };




    // Khởi tạo pagination với số item mỗi trang
    const userPagination = new Pagination(USER_CONFIG.DEFAULT_ITEMS_PER_PAGE);

    // Hàm xây dựng URL với phân trang và filter
    function buildUserApiUrl(searchValue, page, limit) {
        const type = getUserTypeFromURL();
        let url = `${BASE_API_URL}/api/users?limit=${limit}&offset=${(page - 1) * limit}&type=${type}`;

        if (searchValue) {
            url += `&full_name=${encodeURIComponent(searchValue)}`;
        }

        return url;
    }

    // Hàm filterData với phân trang
    function filterData(searchValue = "", extraParams = "", page = 1, limit = USER_CONFIG.DEFAULT_ITEMS_PER_PAGE) {
        let apiUrl = buildUserApiUrl(searchValue, page, limit);

        if (extraParams) {
            apiUrl += `&${extraParams}`;
        }

        console.log("API URL:", apiUrl);

        $.ajax({
            url: apiUrl,
            type: "GET",
            success: function (response) {
                console.log("Filtered Data:", response);
                if (response.success) {
                    handleUserResponse(response, page, limit);
                } else {
                    showError(response.message || "Failed to load users");
                    renderUserList([]);
                }
            },
            error: function (xhr) {
                console.error("Error fetching data:", xhr);
                const errorMsg = xhr.responseJSON?.message || "Không thể tải dữ liệu";
                showError(errorMsg);
                renderUserList([]);
            }
        });
    }

    // Hàm xử lý response từ server
    function handleUserResponse(response, page, limit) {
        const users = response.data || [];
        const totalUsers = response.totalElements || 0;

        // Cập nhật pagination
        userPagination.currentPage = page;
        userPagination.totalItems = totalUsers;

        renderUserList(users);
        updatePaginationDisplay(totalUsers, page, limit);
    }

    // Hàm cập nhật hiển thị phân trang
    function updatePaginationDisplay(totalItems, page, limit) {
        const start = (page - 1) * limit + 1;
        const end = Math.min(page * limit, totalItems);

        // Cập nhật thông tin bản ghi
        userPagination.updateRecordInfo(start, end, totalItems);

        // Render phân trang
        userPagination.render(totalItems);
    }

    // Hàm cập nhật danh sách người dùng
    function renderUserList(users) {
        const tableBody = $('#data-table');
        if (!tableBody.length) return;

        if (!users || users.length === 0) {
            tableBody.html('<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>');
            return;
        }

        const isEmployeeView = getUserTypeFromURL() === 'employee';

        const rows = users.map((user, index) => `
        <tr id="user-${user.id}">
            <td>${(userPagination.currentPage - 1) * userPagination.itemsPerPage + index + 1}</td>
            <td>${user.full_name || 'N/A'}</td>
            <td>${user.email || 'N/A'}</td>
            <td>${user.phone_number || 'N/A'}</td>
            ${isEmployeeView ? `<td>${user.role_name || 'N/A'}</td>` : ''}
            <td>${user.status || 'N/A'}</td>
            <td>${user.created_at ? new Date(user.created_at).toLocaleDateString('vi-VN') : 'N/A'}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-info btn-sm btn-view" data-id="${user.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm btn-edit" data-id="${user.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${user.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

        tableBody.html(rows);
        attachUserEvents();
    }

    // Hàm xử lý tìm kiếm
    function handleUserSearch() {
        const searchValue = $('#user-search-input').val().trim();
        userPagination.setSearchValue(searchValue);
        userPagination.currentPage = 1; // Reset về trang đầu
        loadUsers();
    }

    // Khởi tạo
    $(document).ready(function () {
        // Khởi tạo pagination với callback là filterData
        userPagination.init(filterData);

        // Gắn sự kiện tìm kiếm với debounce 300ms
        $('#user-search-input').on('input', debounce(handleUserSearch, 300));

        // Load dữ liệu ban đầu
        loadUsers();
    });

    // Hàm debounce
    function debounce(func, timeout = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
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
    $(document).on("click", ".btn-delete", function () {
        let userId = this.getAttribute("data-id");
        if (confirm("Bạn có chắc chắn muốn xóa mục này?")) {
            $.ajax({
                //Gửi api xóa người dùng
                url: `${BASE_API_URL}/api/users/${userId}`,
                type: "DELETE",
                success: function (response) {
                    try {
                        if (response.success) {
                            console.log("Xóa thành công:", response);
                            toast("Xóa người dùng thành công!", "success");
                            // Xóa dòng có id="cate-{id}"
                            let rowToDelete = document.getElementById(`user-${userId}`);
                            if (rowToDelete) rowToDelete.remove();

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

    //======================== LỌC NGƯỜI DÙNG ========================
    let searchTimeout;
    //Tìm kiếm theo tên khi nhập vào ô input
    $("#keyword").on("input", function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            let keyValue = $(this).val().trim();
            console.log("Key value:", keyValue);
            let urlParams = new URLSearchParams(window.location.search);
            let type = urlParams.get('type');
            let isEmployeeList = type === 'employee';
            let paramsType = isEmployeeList ? 'type=employee' : 'type=customer';
            console.log("isEmployeeList:", isEmployeeList);
            filterData(keyValue, paramsType); // Gọi hàm lọc dữ liệu khi nhập tên
        }, 100); // Đợi 100ms sau khi người dùng dừng nhập mới gọi API
    });
    // Lấy danh sách tất cả các nút có class "btn-delete
    $(document).on("click", ".btn-filter", function () {
        console.log("Click filter button");
        let nameSearch = document.getElementById("keyword").value.trim();
        console.log("Key value:", nameSearch);
        //e.preventDefault();
        let formData = $("#filterForm").serialize(); // Chuyển form thành chuỗi query string
        console.log("Form data:", formData);
        filterData(nameSearch, formData); // Gọi hàm lọc dữ liệu
    });

    // Xóa bộ lọc
    $("#resetFilter").click(function () {
        console.log("Click reset filter button");
        $("#filterForm")[0].reset();    
        console.log("keyword", keyValue);
        filterData(keyValue); // Gọi hàm lọc dữ liệu
    });

    
});


