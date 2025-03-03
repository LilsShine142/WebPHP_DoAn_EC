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
</head>
<?php
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
                <i class="fas fa-plus"></i> Thêm người dùng
            </a>
        </div>
        <div class="card-body">
            <table id="customerTable" class="table table-bordered table-striped">
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Thêm danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Nhập thông tin danh mục mới tại đây...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal cập nhật thông tin khách hàng -->
    <div class="modal fade" id="formModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Cập nhật thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="customerId" name="customerId">

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
        function fetchUsersByType(type) {
            let apiUserRoles = 'http://localhost:3000/WebPHP_DoAn_EC/api/users/user_roles'; // API user theo role
            let apiRoles = 'http://localhost:3000/WebPHP_DoAn_EC/api/users/roles'; // API roles

            let isEmployeeList = type !== 'customer'; // Xác định là nhân viên hay khách hàng
            /*  Cách 1: lấy từng role  */
            // let apiUrl = type == 'employee' ?
            //     'http://localhost:3000/WebPHP_DoAn_EC/api/users/user_roles?role_id=3' :
            //     'http://localhost:3000/WebPHP_DoAn_EC/api/users/user_roles?role_id=2';
            // //Cách 2: lấy 2 role: nhân viên(employee và admin đều tính là nhân viên) và khách hàng
            // let apiUrl = type == 'customer' ?
            //     'http://localhost:3000/WebPHP_DoAn_EC/api/users/user_roles?role_id=2' :
            //     'http://localhost:3000/WebPHP_DoAn_EC/api/users/user_roles?role_id=2';

            // $.ajax({
            //     url: apiUrl, // Đường dẫn API lấy danh sách khách hàng hoặc nhân viên
            //     type: 'GET',
            //     dataType: "json",
            //     success: function(response) {
            //         console.log("User list:", response);
            //         if (response.success && response.data.length > 0) {
            //             let tableBody = document.getElementById('data-table');
            //             let html = '';

            //             let userPromises = response.data.map(user => {
            //                 return $.ajax({
            //                     url: `http://localhost:3000/WebPHP_DoAn_EC/api/users/${user.user_id}`, // Gọi API user theo user_id
            //                     type: 'GET',
            //                     dataType: "json"
            //                 });
            //             });

            //             Promise.all(userPromises)
            //                 .then(userDetails => {
            //                     userDetails.forEach(detail => {
            //                         if (detail.success) {
            //                             let user = detail.data;
            //                             html += `
            //                                 <tr>
            //                                     <td>${user.id}</td>
            //                                     <td>${user.full_name}</td>
            //                                     <td>${user.email}</td>
            //                                     <td>${user.status}</td>
            //                                     <td>${user.phone_number}</td>
            //                                     <td>${user.address}</td>
            //                                     <td>${user.created_at}</td>
            //                                     <td>
            //                                         <button class="btn btn-info btn-view" data-id="${user.id}">Xem</button>
            //                                         <button class="btn btn-warning btn-update" data-id="${user.id}">Sửa</button>
            //                                         <button class="btn btn-danger btn-delete" data-id="${user.id}">Xóa</button>
            //                                     </td>
            //                                 </tr>
            //                             `;
            //                         }
            //                     });
            //                     tableBody.innerHTML = html; // Cập nhật nội dung bảng sau khi vòng lặp kết thúc
            //                 })
            //                 .catch(error => {
            //                     console.error("Lỗi khi lấy thông tin chi tiết người dùng", error);
            //                 });

            //         } else {
            //             console.warn("Không có dữ liệu người dùng");
            //         }
            //     },
            //     error: function() {
            //         console.error("Lỗi khi tải danh sách người dùng");
            //     }
            // });


            /*  Cách 2: lấy 2 role: nhân viên(employee và admin đều tính là nhân viên) và khách hàng  */
            //Call API user_roles và roles trước để lấy thông tin user và role
            //Gọi cả 2 API cùng lúc
            Promise.all([
                $.ajax({
                    url: apiUserRoles,
                    type: 'GET',
                    dataType: "json"
                }).catch(err => console.error("Lỗi API user_roles", err)),
                $.ajax({
                    url: apiRoles,
                    type: 'GET',
                    dataType: "json"
                }).catch(err => console.error("Lỗi API roles", err))
            ]).then(([userRolesRes, rolesRes]) => {
                if (!userRolesRes || !userRolesRes.success || !rolesRes || !rolesRes.success) {
                    console.error("Lỗi khi lấy thông tin user_roles hoặc roles");
                    return;
                }

                let userRoles = userRolesRes.data; //user_id - role_id
                let roles = rolesRes.data; //role_id - name
                console.log("Roles:", roles);
                //Tạo roleMap để lấy tên role dựa vào role_id
                let roleMap = {};
                roles.forEach(role => {
                    roleMap[role.id] = role.name;
                });
                console.log("rolmap", roleMap);
                //Lọc danh sách user theo type
                let filteredUsers = isEmployeeList ?
                    userRoles.filter(user => user.role_id != 2) // Nhân viên (không phải role_id=2)
                    :
                    userRoles.filter(user => user.role_id == 2); // Khách hàng (role_id=2)
                console.log("Filtered users:", filteredUsers);
                // Call API lấy thông tin chi tiết của từng user theo user_id
                let userPromises = filteredUsers.map(user => {
                    return $.ajax({
                        url: `http://localhost:3000/WebPHP_DoAn_EC/api/users/${user.user_id}`, // Gọi API user theo user_id
                        type: 'GET',
                        dataType: "json"
                    });
                });
                console.log("User promises:", userPromises);
                return Promise.all(userPromises).then(userDetails => {
                    // Cập nhật thông tin user với role_name
                    let userWithDetails = userDetails.map((detail) => {
                        console.log("roleMap[detail.data.role_id]", roleMap[detail.data.id]);
                        if (detail && detail.success && detail.data) {
                            console.log("Detail data:", detail.data);
                            return {
                                ...detail.data,
                                role_name: roleMap[detail.data.id] || "Không xác định" // Lấy role_name từ role_id đúng cách
                            };
                            console.log("role name", roleMap[detail.data.id]);
                        }
                        return null;
                    }).filter(user => user !== null);

                    console.log("User with details:", userWithDetails);
                    renderTable(userWithDetails, isEmployeeList);
                });


            }).catch(error => {
                console.error("Lỗi khi lấy thông tin chi tiết người dùng", error);
            });
        }

        // Hàm render bảng
        function renderTable(users, isEmployeeList) {
            let tableBody = document.getElementById('data-table');
            let tableHead = document.getElementById('table-head');

            let tableHeadHtml = `
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Phone</th>
                    ${isEmployeeList ? '<th>Role</th>' : ''} 
                    <th>Address</th>
                    <th>Create At</th>
                    <th>Actions</th>
                </tr>
            `;
            tableHead.innerHTML = tableHeadHtml;

            let html = '';
            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.full_name}</td>
                        <td>${user.email}</td>
                        <td>${user.status}</td>
                        <td>${user.phone_number}</td>
                        ${isEmployeeList ? `<td>${user.role_name}</td>` : ''} <!-- Hiển thị role nếu là nhân viên -->
                        <td>${user.address}</td>
                        <td>${user.created_at}</td>
                        <td>
                            <button class="btn btn-info btn-view" data-id="${user.id}">Xem</button>
                            <button class="btn btn-warning btn-update" data-id="${user.id}">Sửa</button>
                            <button class="btn btn-danger btn-delete" data-id="${user.id}">Xóa</button>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
        }

        // Gọi hàm loadUsers() ngay khi trang load
        $(document).ready(function() {
            let urlParams = new URLSearchParams(window.location.search);
            let type = urlParams.get('type'); // Lấy type từ URL (employee hoặc customer)
            console.log("Type:", type);
            fetchUsersByType(type);
        });
    </script>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Customer/validationCustomer.js"></script>
    <script src="assets/js/Customer/customerActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>