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
        <div class="card-search d-flex align-items-center gap-4 p-4">
            <!-- Ô tìm kiếm -->
            <div class="position-relative w-75">
                <input type="text" id="keyword" name="keyword" formcontrolname="keyword"
                    placeholder="Enter the user name you want to search......"
                    class="form-control ps-4">
                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="cursor:pointer"></i>
            </div>

            <!-- Nút filter -->
            <button id="toggleFilter" class="btn d-flex align-items-center justify-content-center rounded"
                style="width: 40px; height: 40px; background-color: #d5e2d2; border: none;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.249995 1.61C2.56999 4.59 5.99999 9 5.99999 9V14C5.99999 15.1 6.89999 16 7.99999 16C9.09999 16 10 15.1 10 14V9C10 9 13.43 4.59 15.75 1.61C16.26 0.95 15.79 0 14.95 0H1.03999C0.209995 0 -0.260005 0.95 0.249995 1.61Z" fill="#00A64F"></path>
                </svg>
            </button>

            <!-- Nút Add User -->
            <a href="index.php?page=pages/User/create.php" class="btn btn-primary btn-sm btn-add btn-lg px-4 py-2">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
        <!-- Bộ lọc -->
        <div id="filterContainer" class="card p-3 my-4 mx-4" style="display: none;"> <!-- my-3: margin trên/dưới, mx-4: margin trái/phải -->
            <div _ngcontent-obg-c192="" class="d-flex align-items-center justify-content-between" bis_skin_checked="1">
                <h6 _ngcontent-obg-c192="">Filter</h6>
                <span id="closeIcon" class="btn-close" disabled aria-label="Close" style="cursor:pointer"></span>
            </div>
            <form id="filterForm" method="GET">
                <input type="hidden" name="type" value="<?= $type ?>">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">User ID</label>
                        <input type="text" name="id" class="form-control" placeholder="Enter User ID"  value="<?= $_GET['id'] ?? '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Contact information</label>
                        <input type="text" name="contact" class="form-control" placeholder="Enter phone number or email" value="<?= $_GET['contact'] ?? '' ?>">
                    </div>
                    <?php
                    // if ($type == 'employee') {
                    //     echo '
                    //     <div class="col-md-3">
                    //         <label for="role">Select role:</label>
                    //         <select class="form-control" id="role" name="role" required></select>
                    //     </div>
                    //     <!-- =============================== Call API để lấy danh sách các role =============================== -->
                    //     <script>
                    //         $(document).ready(function() {
                    //             $.ajax({
                    //                 url: `${BASE_API_URL}/api/users/roles`,
                    //                 type: "GET",
                    //                 contentType: "application/json",
                    //                 dataType: "json",
                    //                 success: function(response) {
                    //                     if (response.success) {
                    //                         let roles = response.data;
                    //                         let roleSelect = $("#role");
                    //                         roles.forEach(role => {
                    //                             roleSelect.append(`<option value="${role.id}">${role.name}</option>`);
                    //                         });
                    //                     } else {
                    //                         alert("Lỗi khi lấy danh sách role: " + response.message);
                    //                     }
                    //                 },
                    //                 error: function(xhr, status, error) {
                    //                     console.error("Lỗi:", xhr.responseText);
                    //                     alert("Có lỗi xảy ra: " + xhr.responseText);
                    //                 }
                    //             });
                    //         });
                    //     </script>
                    //     ';
                    // }
                    ?>

                    <div class="col-md-3">
                        <label class="form-label">From date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To day</label>
                        <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-filter">Apply</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary resetFilter">Cancel</button>
                </div>
            </form>
        </div>
        <!-- JS ẩn hiện bộ lọc khi bấm nút lọc -->
        <script>
            document.getElementById("toggleFilter").addEventListener("click", function() {
                toggleFilter();
            });

            document.getElementById("closeIcon").addEventListener("click", function() {
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
        </script>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Cập nhật thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="userId" name="userId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fullname">Full name:</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone number:</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="form-group">
                                    <label for="role">Select role:</label>
                                    <select class="form-control" id="role" name="role" required></select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city_province">City province:</label>
                                    <select class="form-control" id="city_province" name="city_province" required></select>
                                </div>

                                <div class="form-group">
                                    <label for="district">District:</label>
                                    <select class="form-control" id="district" name="district" required></select>
                                </div>

                                <div class="form-group">
                                    <label for="ward">Ward:</label>
                                    <select class="form-control" id="ward" name="ward" required></select>
                                </div>

                                <div class="form-group">
                                    <label for="street">Street:</label>
                                    <input type="text" class="form-control" id="street" name="street" required>
                                </div>

                                <div class="form-group">
                                    <label for="apartment_number">Apartment number:</label>
                                    <input type="text" class="form-control" id="apartment_number" name="apartment_number" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-button">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveUser">Lưu</button>
                </div>
            </div>
        </div>
    </div>
    <!-- =============================== CALL API LẤY DANH SÁCH EMPLOYEE HOẶC CUSTOMER ================================================= -->
    <script>
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

        // Gọi hàm fetchUsersByType khi trang load
        $(document).ready(function() {
            let urlParams = new URLSearchParams(window.location.search);
            let type = urlParams.get('type');
            fetchUsersByType(type);
        });
    </script>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/User/validationUser.js"></script>
    <script src="assets/js/User/userActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>