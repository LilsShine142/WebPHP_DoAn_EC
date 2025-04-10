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

                if (response.success && response.data) {
                    let addressData = null;

                    if (Array.isArray(response.data)) {
                        // Trả về mảng: tìm địa chỉ mặc định
                        addressData = response.data.find(addr => addr.is_default == 1);
                    } else if (typeof response.data === "object") {
                        // Trả về object đơn: dùng luôn
                        addressData = response.data;
                    }

                    if (addressData) {
                        console.log("Dữ liệu user:", addressData);
                        loadUserDataToModal(addressData);
                    } else {
                        console.warn("Không tìm thấy địa chỉ phù hợp");
                    }
                } else {
                    console.error("Lỗi khi tải dữ liệu hoặc không có dữ liệu");
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
    $("#modalView").on("hidden.bs.modal", function () {
        const tableBody = document.getElementById("data-viewuser-table");
        tableBody.innerHTML = ""; // Reset lại nội dung bảng khi đóng modal
    });
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
    // let apiUrl = `${BASE_API_URL}/api/users?full_name=${encodeURIComponent(keyValue)}`;
    // console.log("API URL:", apiUrl);
    // if (extraParams) {
    //     apiUrl += `&${extraParams}`;
    // }
    // console.log("API URL with extra params:", apiUrl);

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
    // let urlParams = new URLSearchParams(window.location.search);
    // let type = urlParams.get('type');
    // let isEmployeeList = type === 'employee';
    // console.log("isEmployeeList:", isEmployeeList);
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

    // Gọi API để lấy thông tin người dùng
    async function getUserDataByID(userId) {
        try {
            const [userInforRes, userRolesRes, addressRes] = await Promise.all([
                $.ajax({
                    url: `${BASE_API_URL}/api/users/${userId}`,
                    type: 'GET',
                    dataType: "json"
                }).catch(error => {
                    console.error("Lỗi API userInfor:", error);
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
            ]);

            if (!userInforRes.success || !userRolesRes.success) {
                throw new Error("Không thể lấy userInfor hoặc user_roles");
            }

            const roleId = userRolesRes.data[0]?.role_id || 0;

            let rolesRes = { success: true, data: [] };
            if (roleId > 0) {
                rolesRes = await $.ajax({
                    url: `${BASE_API_URL}/api/users/roles/${roleId}`,
                    type: 'GET',
                    dataType: "json"
                }).catch(error => {
                    console.error("Lỗi API roles:", error);
                    return { success: false, data: [] };
                });

                if (!rolesRes.success) {
                    throw new Error("Không thể lấy roles");
                }
            }

            // Lọc địa chỉ mặc định
            let defaultAddress = null;
            const addresses = addressRes.success ? addressRes.data : [];

            if (Array.isArray(addresses)) {
                defaultAddress = addresses.length === 1
                    ? addresses[0]
                    : addresses.find(addr => addr.is_default == 1);
            }

            return {
                userInfor: userInforRes.data,
                userRoles: userRolesRes.data,
                roles: rolesRes.data,
                addresses: defaultAddress ? [defaultAddress] : []
            };
        } catch (error) {
            console.error("Lỗi khi getUserDataByID:", error);
            return {
                userInfor: {},
                userRoles: [],
                roles: [],
                addresses: []
            };
        }
    }


    // ================== MỞ FORM LỌC ==================
    document.getElementById("toggleFilter").addEventListener("click", function () {
        toggleFilter();
    });

    document.getElementById("closeIcon").addEventListener("click", function () {
        toggleFilter();
    });

    function toggleFilter() {
        let filterContainer = document.getElementById("filterContainer");
        let filterIcon = document.getElementById("filterIcon");
        let closeIcon = document.getElementById("closeIcon");

        if (filterContainer.style.display === "none") {
            filterContainer.style.display = "block";
            filterIcon.style.display = "none";
            closeIcon.style.display = "inline";
        } else {
            filterContainer.style.display = "none";
            filterIcon.style.display = "inline";
            closeIcon.style.display = "none";
        }
    }



    // ================== CẤU HÌNH HỆ THỐNG ==================
    const USER_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 2,
        DEFAULT_EXTRA_PARAMS: ""
    };

    // ================== KHỞI TẠO PHÂN TRANG ==================
    const pagination = new Pagination(USER_CONFIG.DEFAULT_ITEMS_PER_PAGE);

    // ================== HÀM CHÍNH XỬ LÝ DỮ LIỆU ==================
    async function fetchUsersByType(type) {
        try {
            const extraParams = `type=${type}`;

            pagination.init(async (searchValue, extraParams, page, perPage) => {
                try {
                    const limit = perPage || USER_CONFIG.DEFAULT_ITEMS_PER_PAGE;
                    const offset = (page - 1) * limit;

                    // Thêm tham số phân trang vào query params
                    const params = new URLSearchParams(extraParams);
                    params.append('limit', limit);
                    params.append('offset', offset);
                    params.append('full_name', searchValue);
                    // 1. Lấy danh sách user đã phân trang từ server
                    const { users, totalCount } = await fetchPaginatedUsers(params.toString());

                    // 2. Lấy thông tin roles và addresses cho các user này
                    const { roles, addresses } = await fetchRolesAndAddresses();

                    // 3. Merge dữ liệu
                    const userWithDetails = mergeUserData(users, roles, addresses);
                    console.log("userWithDetails", userWithDetails);
                    // 4. Áp dụng bộ lọc từ form nếu có
                    const filterParams = new URLSearchParams(extraParams);
                    const filteredUsers = applyFilters(userWithDetails, filterParams);

                    // 5. Áp dụng tìm kiếm nếu có
                    const finalUsers = searchValue
                        ? filterUsersBySearch(filteredUsers, searchValue)
                        : filteredUsers;

                    // Render bảng và cập nhật phân trang
                    const isEmployeeList = type !== 'customer';
                    renderTable(finalUsers, isEmployeeList);
                    pagination.updateRecordInfo(
                        offset + 1,
                        offset + finalUsers.length,
                        totalCount
                    );
                    pagination.render(totalCount);

                } catch (error) {
                    console.error("Lỗi khi xử lý dữ liệu:", error);
                    showErrorToast("Lỗi khi xử lý dữ liệu người dùng");
                }
            }, USER_CONFIG.DEFAULT_ITEMS_PER_PAGE, extraParams);

            pagination.loadData();
        } catch (error) {
            console.error("Lỗi chính khi lấy dữ liệu:", error);
            showErrorToast("Lỗi hệ thống khi tải dữ liệu");
        }
    }

    // ================== CÁC HÀM HỖ TRỢ XỬ LÝ DỮ LIỆU ==================
    async function fetchPaginatedUsers(params) {
        try {
            const response = await $.ajax({
                url: `${BASE_API_URL}/api/users?${params}`,
                type: 'GET',
                dataType: "json"
            });

            if (!response?.success) {
                throw new Error("Dữ liệu trả về không hợp lệ");
            }

            return {
                users: response.data,
                totalCount: response.totalElements
            };
        } catch (error) {
            console.error("Lỗi khi lấy dữ liệu người dùng:", error);
            throw error;
        }
    }

    async function fetchRolesAndAddresses() {
        try {
            const [rolesRes, addressRes] = await Promise.all([
                $.ajax({
                    url: `${BASE_API_URL}/api/users/roles`,
                    type: 'GET',
                    dataType: "json"
                }),
                $.ajax({
                    url: `${BASE_API_URL}/api/users/addresses`,
                    type: 'GET',
                    dataType: "json"
                })
            ]);

            if (!rolesRes?.success || !addressRes?.success) {
                throw new Error("Dữ liệu trả về không hợp lệ");
            }

            return {
                roles: rolesRes.data,
                addresses: addressRes.data
            };
        } catch (error) {
            console.error("Lỗi khi lấy roles và addresses:", error);
            throw error;
        }
    }

    function mergeUserData(users, addresses) {
        const addressMap = addresses.reduce((map, address) => {
            map[address.user_id] = address;
            return map;
        }, {});

        return users.map(user => {
            const userAddress = addressMap[user.id] || {};
            return {
                ...user,
                role_name: user.role_name || "Không xác định",
                street: userAddress.street || "Chưa có tên đường",
                apartment_number: userAddress.apartment_number || "Chưa có số nhà",
                ward: userAddress.ward || "Chưa có phường/xã",
                district: userAddress.district || "Chưa có quận/huyện",
                city_province: userAddress.city_province || "Chưa có tỉnh/thành phố"
            };
        });
    }


    function filterUsersBySearch(users, searchValue) {
        const searchTerm = searchValue.toLowerCase();
        console.log("searchTerm", searchTerm);
        return users.filter(user =>
            user.full_name.toLowerCase().includes(searchTerm)
        );
    }

    function updatePaginationInfo(totalElements, page, limit, offset) {
        pagination.updateRecordInfo(
            offset + 1,
            Math.min(offset + limit, totalElements),
            totalElements
        );
        pagination.render(totalElements);
    }

    function renderTable(users, isEmployeeList) {
        const tableBody = document.getElementById('data-table');
        const tableHead = document.getElementById('table-head');

        tableHead.innerHTML = `
        <tr>
            <th>ID</th>
            <th>Fullname</th>
            <th>Email</th>
            <th>Phone</th>
            ${isEmployeeList ? '<th>Role</th>' : ''} 
            <th>Status</th>
            <th>Create At</th>
            <th>Actions</th>
        </tr>
    `;

        tableBody.innerHTML = users.map(user => `
        <tr id="user-${user.id}">
            <td>${user.id}</td>
            <td class="user-name">${user.full_name}</td>
            <td class="user-email">${user.email}</td>
            <td class="user-phone">${user.phone_number}</td>
            ${isEmployeeList ? `<td class="user-role">${user.role_name}</td>` : ''} 
            <td class="user-status">${user.status}</td>
            <td>${user.created_at}</td>
            <td>
                <button class="btn btn-info btn-view" data-id="${user.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-update" data-id="${user.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="${user.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
    }

    // ================== CÁC HÀM LỌC DỮ LIỆU ==================
    function applyFilters(users, filterParams) {
        let filteredUsers = [...users];

        // Lọc theo ID nếu có
        if (filterParams.has('id')) {
            const idFilter = filterParams.get('id').trim();
            if (idFilter) {
                filteredUsers = filteredUsers.filter(user =>
                    user.id.toString().includes(idFilter)
                );
            }
        }

        // Lọc theo contact (email hoặc phone)
        if (filterParams.has('contact')) {
            const contactFilter = filterParams.get('contact').trim().toLowerCase();
            if (contactFilter) {
                filteredUsers = filteredUsers.filter(user =>
                    user.email.toLowerCase().includes(contactFilter) ||
                    user.phone_number.includes(contactFilter)
                );
            }
        }

        // Lọc theo ngày tạo
        if (filterParams.has('from_date') || filterParams.has('to_date')) {
            const fromDate = filterParams.get('from_date');
            const toDate = filterParams.get('to_date');

            filteredUsers = filteredUsers.filter(user => {
                const userDate = new Date(user.created_at);
                if (fromDate && userDate < new Date(fromDate)) return false;
                if (toDate && userDate > new Date(toDate + 'T23:59:59')) return false;
                return true;
            });
        }

        return filteredUsers;
    }

    // ================== XỬ LÝ SỰ KIỆN ==================
    let searchTimeout;

    $("#keyword").on("input", function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const keyValue = $(this).val().trim();
            pagination.setSearchValue(keyValue);
            pagination.loadData();
        }, 300);
    });

    // ================== XỬ LÝ SỰ KIỆN LỌC ==================
    $(document).on("click", ".btn-filter", function () {
        // Lấy dữ liệu từ form
        const formData = $("#filterForm").serialize();
        const filterParams = new URLSearchParams(formData);
        const searchValue = $("#keyword").val().trim();

        // Reset về trang đầu tiên khi áp dụng bộ lọc mới
        pagination.currentPage = 1;

        // Cập nhật tham số phân trang và tìm kiếm
        pagination.setSearchValue(searchValue);

        // Kết hợp tham số lọc với tham số hiện có
        const currentParams = new URLSearchParams(pagination.extraParams);
        filterParams.forEach((value, key) => {
            currentParams.set(key, value);
        });

        // Cập nhật extraParams với các tham số lọc mới
        pagination.extraParams = currentParams.toString();

        // Load lại dữ liệu với các tham số mới
        pagination.loadData();
    });

    $("#resetFilter").click(function () {
        $("#filterForm")[0].reset();
        $("#keyword").val('');
        pagination.setSearchValue('');
        pagination.loadData();
    });

    // ================== HÀM KHỞI TẠO TRANG ==================
    function initializePage() {
        const type = new URLSearchParams(window.location.search).get('type');
        if (!type) {
            showErrorToast("Thiếu tham số type trong URL");
            return;
        }
        fetchUsersByType(type);
    }

    function showErrorToast(message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    }

    // ================== KHỞI CHẠY ỨNG DỤNG ==================
    $(document).ready(function () {
        initializePage();
    });

















    //====================================== Hiện modal và data khi bấm update khách hàng hoặc nhân viên ====================================
    // Lấy danh sách tất cả các nút có class "btn-update"
    let lastUserId = null;

    $(document).on("click", ".btn-update", function () {
        let userId = this.getAttribute("data-id");
        console.log("ID khách hàng cần cập nhật:", userId);

        // Nếu id click trước đó khác với id vừa click thì xóa thông báo lỗi cũ
        if (lastUserId !== userId) {
            clearAllErrors();
        }

        lastUserId = userId;

        // Gọi hàm lấy thông tin user
        getUserDataByID(userId).then(userData => {
            if (!userData || !userData.userInfor) {
                alert("Không tìm thấy khách hàng!");
                return;
            }

            console.log("userDataUpdate:", userData);

            const { userInfor, addresses, roles, userRoles } = userData;

            // Điền dữ liệu vào form
            $("#userId").val(userInfor.id);
            $("#fullname").val(userInfor.full_name);
            $("#email").val(userInfor.email);
            $("#phone").val(userInfor.phone_number);
            $("#password").val("");
            $("#addressId").val("");
            // Nếu có địa chỉ mặc định thì điền
            if (addresses.length > 0) {
                const address = addresses[0];

                console.log("address:", address);
                $("#addressId").val(address.id || "");
                $("#street").val(address.street || "");
                $("#apartment_number").val(address.apartment_number || "");

                // Tỉnh/Huyện/Xã
                let selectedProvince = address.city_province;
                let selectedDistrict = address.district;
                let selectedWard = address.ward;

                loadProvinces(selectedProvince).then(provinceCode => {
                    if (provinceCode) {
                        loadDistricts(provinceCode, selectedDistrict).then(districtCode => {
                            if (districtCode) {
                                loadWards(districtCode, selectedWard);
                            }
                        });
                    }
                });
            } else {
                // Nếu không có địa chỉ thì reset các trường
                $("#street").val("");
                $("#apartment_number").val("");
                $("#city_province").val("");
                $("#district").html('<option value="">Select district</option>');
                $("#ward").html('<option value="">Select ward</option>');
            }

            // Load role
            loadRoles(userRoles[0]);

            // Hiển thị modal
            $("#updateModal").modal("show");

            // Gắn lại sự kiện thay đổi tỉnh/huyện
            $("#city_province").off("change").on("change", function () {
                let provinceCode = this.value;
                loadDistricts(provinceCode, "").then(() => {
                    $("#ward").html('<option value="">Select ward</option>'); // Reset xã
                });
            });

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
                id: document.getElementById("addressId").value,
                city_province: document.getElementById("city_province").selectedOptions[0].text,
                district: document.getElementById("district").selectedOptions[0].text,
                ward: document.getElementById("ward").selectedOptions[0].text,
                street: document.getElementById("street").value.trim(),  // Nếu là input thì giữ nguyên
                apartment_number: document.getElementById("apartment_number").value.trim(),
                is_default: true
            };
            console.log("addressData:", addressData);

            // API endpoints
            let apiUserInforUpdate = `${BASE_API_URL}/api/users/${userIdUpdate}`;
            let apiUserRoleUpdate = `${BASE_API_URL}/api/users/user_roles?user_id=${userIdUpdate}`;
            let apiUserAddressUpdate = `${BASE_API_URL}/api/users/addresses/${addressData.id}?user_id=${userIdUpdate}`;
            console.log("apiUserInforUpdate:", apiUserInforUpdate);
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


});


