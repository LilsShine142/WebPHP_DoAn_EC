<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5.3 Modal Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<?php
$cssStack = [];
$jsStack = [];
array_push($cssStack, '<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">');


array_push($jsStack, '<script src="plugins/datatables/jquery.dataTables.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>');
array_push($jsStack, '<script src="plugins/jszip/jszip.min.js"></script>');
array_push($jsStack, '<script src="plugins/pdfmake/pdfmake.min.js"></script>');
array_push($jsStack, '<script src="plugins/pdfmake/vfs_fonts.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>');
array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>');

?>

<body>
    <div class="card">
        <div class="card-header">
            <?php
            $type = isset($_GET['type']) ? $_GET['type'] : '';
            if ($type == 'employee') {
                echo "<h3 class=`card-title`>Employee list</h3>";
            } elseif ($type == 'customer') {
                echo "<h3 class=`card-title`>Customer list</h3>";
            }
            ?>
        </div>
        <div class="card-search" style="display: flex; margin: 15px;">
            <div class="search" style="width: 80%; margin-right: 30px;">
                <input type="text" class="form-control" placeholder="Search...">
            </div>
            <a href="index.php?page=pages/User/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
        <div class="card-body">
            <table id="customerTable" class="table table-bordered table-striped table-hover">
                <thead id="table-head">
                    <!-- Dữ liệu sẽ được load bằng AJAX -->
                </thead>
                <tbody id="data-table">
                    <!-- Dữ liệu sẽ được load bằng AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- ModalView -->
    <div class="modal fade" id="modalView" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Thêm danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <table id="viewUserTable" class="table table-bordered table-striped table-hover">
                            <thead id="">
                                <tr>
                                    <th scope="col">#ID</th>
                                    <th scope="col">FullName</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Street</th>
                                    <th scope="col">Apartment number</th>
                                    <th scope="col">Ward</th>
                                    <th scope="col">District</th>
                                    <th scope="col">City province</th>
                                </tr>
                            </thead>
                            <tbody id="data-viewuser-table">
                                <!-- Dữ liệu sẽ được load bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal cập nhật thông tin khách hàng -->
    <div class="modal fade" id="updateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Cập nhật thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="userId" name="userId">

                        <div class="form-group">
                            <label for="fullname">Họ và tên:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Số điện thoại:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Select role:</label>
                            <select class="form-control" id="role" name="role" required></select>
                        </div>
                        <!-- =============================== Call API để lấy danh sách các role =============================== -->
                        <script>
                            $(document).ready(function() {
                                // Gọi API lấy danh sách roles ngay khi trang load
                                loadRoles(null);
                            });
                        </script>

                        <div class="form-group">
                            <label for="province">Tỉnh/Thành phố:</label>
                            <select class="form-control" id="province" name="province" required></select>
                        </div>

                        <div class="form-group">
                            <label for="district">Quận/Huyện:</label>
                            <select class="form-control" id="district" name="district" required></select>
                        </div>

                        <div class="form-group">
                            <label for="ward">Phường/Xã:</label>
                            <select class="form-control" id="ward" name="ward" required></select>
                        </div>

                        <div class="form-group">
                            <label for="address">Địa chỉ chi tiết:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveCustomer">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =============================== CALL API LẤY DANH SÁCH EMPLOYEE HOẶC CUSTOMER ================================================= -->
    <script>
        // function fetchUsersByType(type) {
        //     let apiUserRoles = `${BASE_API_URL}/api/users/user_roles`; // API user theo role
        //     console.log("API user roles:", apiUserRoles);
        //     let apiRoles = `${BASE_API_URL}/api/users/roles`; // API roles
        //     //let apiUserAddress = `${BASE_API_URL}/api/users/addresses?user_id=${userid}`; // API user theo address
        //     let isEmployeeList = type !== 'customer'; // Xác định là nhân viên hay khách hàng
        //     //Call API user_roles và roles trước để lấy thông tin user và role
        //     //Gọi cả 2 API cùng lúc
        //     Promise.all([
        //         $.ajax({
        //             url: apiUserRoles,
        //             type: 'GET',
        //             dataType: "json"
        //         }).catch(err => console.error("Lỗi API user_roles", err)),
        //         $.ajax({
        //             url: apiRoles,
        //             type: 'GET',
        //             dataType: "json"
        //         }).catch(err => console.error("Lỗi API roles", err))
        //     ]).then(([userRolesRes, rolesRes]) => {
        //         if (!userRolesRes || !userRolesRes.success || !rolesRes || !rolesRes.success) {
        //             console.error("Lỗi khi lấy thông tin user_roles hoặc roles");
        //             return;
        //         }

        //         let userRoles = userRolesRes.data; //user_id - role_id
        //         let roles = rolesRes.data; //role_id - name
        //         console.log("Roles:", roles);
        //         //Tạo roleMap để lấy tên role dựa vào role_id
        //         let roleMap = {};
        //         roles.forEach(role => {
        //             roleMap[role.id] = role.name;
        //         });
        //         console.log("rolmap", roleMap);
        //         //Lọc danh sách user theo type
        //         let filteredUsers = isEmployeeList ?
        //             userRoles.filter(user => user.role_id != 3) // Nhân viên (không phải role_id=3)
        //             :
        //             userRoles.filter(user => user.role_id == 3); // Khách hàng (role_id=3)
        //         console.log("Filtered users:", filteredUsers);
        //         // Call API lấy thông tin chi tiết của từng user theo user_id
        //         let userPromises = filteredUsers.map(user => {
        //             return $.ajax({
        //                 url: `${BASE_API_URL}/api/users/${user.user_id}`, // Gọi API user theo user_id
        //                 type: 'GET',
        //                 dataType: "json"
        //             });
        //         });
        //         console.log("User promises:", userPromises);
        //         return Promise.all(userPromises).then(userDetails => {
        //             // Cập nhật thông tin user với role_name
        //             let userWithDetails = userDetails.map((detail) => {
        //                 console.log("roleMap[detail.data.role_id]", roleMap[detail.data.id]);
        //                 if (detail && detail.success && detail.data) {
        //                     console.log("Detail data:", detail.data);
        //                     return {
        //                         ...detail.data,
        //                         role_name: roleMap[detail.data.id] || "Không xác định" // Lấy role_name từ role_id đúng cách
        //                     };
        //                     console.log("role name", roleMap[detail.data.id]);
        //                 }
        //                 return null;
        //             }).filter(user => user !== null);

        //             console.log("User with details:", userWithDetails);
        //             renderTable(userWithDetails, isEmployeeList);
        //         });


        //     }).catch(error => {
        //         console.error("Lỗi khi lấy thông tin chi tiết người dùng", error);
        //     });
        // }

        // Hàm render bảng
        // function renderTable(users, isEmployeeList) {
        //     let tableBody = document.getElementById('data-table');
        //     let tableHead = document.getElementById('table-head');

        //     let tableHeadHtml = `
        //         <tr>
        //             <th>ID</th>
        //             <th>Fullname</th>
        //             <th>Email</th>
        //             <th>Status</th>
        //             <th>Phone</th>
        //             ${isEmployeeList ? '<th>Role</th>' : ''} 
        //             <th>Address</th>
        //             <th>Create At</th>
        //             <th>Actions</th>
        //         </tr>
        //     `;
        //     tableHead.innerHTML = tableHeadHtml;

        //     let html = '';
        //     users.forEach(user => {
        //         html += `
        //             <tr id="user-${user.id}">
        //                 <td>${user.id}</td>
        //                 <td class="user-name">${user.full_name}</td>
        //                 <td class="user-email">${user.email}</td>
        //                 <td class="user-status">${user.status}</td>
        //                 <td class="user-phone">${user.phone_number}</td>
        //                 ${isEmployeeList ? `<td class="user-role">${user.role_name}</td>` : ''} <!-- Hiển thị role nếu là nhân viên -->
        //                 <td class="user-address">${user.address}</td>
        //                 <td>${user.created_at}</td>
        //                 <td>
        //                     <button class="btn btn-info btn-view" data-id="${user.id}">Xem</button>
        //                     <button class="btn btn-warning btn-update" data-id="${user.id}">Sửa</button>
        //                     <button class="btn btn-danger btn-delete" data-id="${user.id}">Xóa</button>
        //                 </td>
        //             </tr>
        //         `;
        //     });

        //     tableBody.innerHTML = html;
        // }

        // // Gọi hàm loadUsers() ngay khi trang load
        // $(document).ready(function() {
        //     let urlParams = new URLSearchParams(window.location.search);
        //     let type = urlParams.get('type'); // Lấy type từ URL (employee hoặc customer)
        //     console.log("Type:", type);
        //     fetchUsersByType(type);
        // });

        function fetchUsersByType(type) {
            fetchUserData().then(({
                userRoles,
                roles,
                addresses
            }) => {
                let isEmployeeList = type !== 'customer';
                let filteredUsers = filterUsersByType(userRoles, isEmployeeList);
                console.log("Filtered users:", filteredUsers);
                console.log("addresses2", addresses);
                fetchUserDetails(filteredUsers).then(userDetails => {
                    console.log("User details:", userDetails);
                    console.log("addresses3", addresses);
                    let userWithDetails = mergeUserData(userDetails, roles, addresses);
                    console.log("User with details:", userWithDetails);
                    renderTable(userWithDetails, isEmployeeList);
                });
            }).catch(error => {
                console.error("Lỗi khi lấy thông tin người dùng", error);
            });
        }

        // Gọi API lấy danh sách user_roles, roles và addresses
        function fetchUserData() {
            return Promise.all([
                $.ajax({
                    url: `${BASE_API_URL}/api/users/user_roles`,
                    type: 'GET',
                    dataType: "json"
                }),
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
            ]).then(([userRolesRes, rolesRes, addressRes]) => {
                if (!userRolesRes.success || !rolesRes.success || !addressRes.success) {
                    throw new Error("Lỗi khi lấy dữ liệu user_roles, roles hoặc addresses");
                }
                console.log("addresses", addressRes.data);
                return {
                    userRoles: userRolesRes.data,
                    roles: rolesRes.data,
                    addresses: addressRes.data
                };
            });
        }

        // Lọc danh sách user theo loại (nhân viên/khách hàng)
        function filterUsersByType(userRoles, isEmployeeList) {
            return isEmployeeList ?
                userRoles.filter(user => user.role_id != 3) // Nhân viên
                :
                userRoles.filter(user => user.role_id == 3); // Khách hàng
        }

        // Gọi API lấy chi tiết từng user
        // function fetchUserDetails(users) {
        //     console.log("users", users);
        //     let userPromises = users.map(user => {
        //         return $.ajax({
        //             url: `${BASE_API_URL}/api/users/${user.user_id}`,
        //             type: 'GET',
        //             dataType: "json"
        //         }).then(response => {
        //             if (response.success) {
        //                 return {
        //                     ...response.data,
        //                     role_id: user.role_id
        //                 }; // Thêm role_id vào kết quả
        //             }
        //             return null;
        //         }).catch(error => {
        //             console.error("Error fetching user:", error);
        //             return null;
        //         });
        //     });

        //     return Promise.all(userPromises).then(userDetails => userDetails.filter(user => user !== null));
        // }

        //Thay vì gọi API từng user, lất tất cả user từ API rồi lọc ra user cần thiết
        function fetchUserDetails(user_roles) {
            console.log("user_roles", user_roles);

            return $.ajax({
                url: `${BASE_API_URL}/api/users`,
                type: 'GET',
                dataType: "json"
            }).then(response => {
                if (!response.success) return [];

                let allUsers = response.data; // Danh sách tất cả users từ API

                // Lọc chỉ những user có trong danh sách users truyền vào
                return user_roles.map(user => {
                    let userDetail = allUsers.find(u => u.id === user.user_id);
                    return userDetail ? {
                        ...userDetail,
                        role_id: user.role_id
                    } : null;
                }).filter(user => user !== null);
            }).catch(error => {
                console.error("Error fetching users:", error);
                return [];
            });
        }



        // Ghép role & địa chỉ vào user
        function mergeUserData(users, roles, addresses) {
            let roleMap = {};
            roles.forEach(role => {
                roleMap[role.id] = role.name;
            });
            console.log("rolempa", roleMap);
            return users.map(user => {
                console.log("user", user);
                console.log("userAddress4", addresses);
                let userAddress = addresses.find(addr => addr.user_id === user.id);
                console.log("userAddress5", userAddress);
                return {
                    ...user,
                    role_name: roleMap[user.role_id] || "Không xác định",
                    street: userAddress ? userAddress.street : "Chưa có tên đường",
                    apartment_number: userAddress ? userAddress.apartment_number : "Chưa có số nhà",
                    ward: userAddress ? userAddress.ward : "Chưa có phường/xã",
                    district: userAddress ? userAddress.district : "Chưa có quận/huyện",
                    city_province: userAddress ? userAddress.city_province : "Chưa có tỉnh/thành phố"
                };
            });
        }

        // Render bảng
        function renderTable(users, isEmployeeList) {
            console.log("users", users);
            let tableBody = document.getElementById('data-table');
            let tableHead = document.getElementById('table-head');

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
                <button class="btn btn-info btn-view" data-id="${user.id}">View</button>
                <button class="btn btn-warning btn-update" data-id="${user.id}">Update</button>
                <button class="btn btn-danger btn-delete" data-id="${user.id}">Delete</button>
            </td>
        </tr>
    `).join('');
        }

        // Gọi hàm fetchUsersByType khi trang load
        $(document).ready(function() {
            let urlParams = new URLSearchParams(window.location.search);
            let type = urlParams.get('type');
            fetchUsersByType(type);
        });
    </script>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Customer/validationCustomer.js"></script>
    <script src="assets/js/Customer/customerActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>