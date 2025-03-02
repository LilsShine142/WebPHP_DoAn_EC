<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5.3 Modal Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

// =================== TẠO CODE ĐỂ LẤY DỮ LIỆU TẠM THỜI TỪ MẢNG CUSTOMERS (ĐẾN LÚC DÙNG DATABASE SẼ XÓA PHẦN NÀY)===================
$customers = [
    [
        'id' => 1,
        'fullname' => 'Nguyen Van A',
        'email' => 'nguyenvana@example.com',
        'status' => 'Active',
        'phone' => '0123456789',
        'address' => '123 ABC Street',
        'created_at' => '2023-01-01',
    ],
    [
        'id' => 2,
        'fullname' => 'Tran Thi B',
        'email' => 'tranthib@example.com',
        'status' => 'Inactive',
        'phone' => '0987654321',
        'address' => '456 DEF Street',
        'created_at' => '2023-02-01',
    ],
];

?>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Customer list</h3>
        </div>
        <div class="card-search" style="display: flex; margin: 15px;">
            <div class="search" style="width: 80%; margin-right: 30px;">
                <input type="text" class="form-control" placeholder="Search...">
            </div>
            <a href="index.php?page=pages/Customer/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm người dùng 
            </a>
        </div>
        <div class="card-body">
            <table id="customerTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Create At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo $customer['fullname']; ?></td>
                            <td><?php echo $customer['email']; ?></td>
                            <td><?php echo $customer['status']; ?></td>
                            <td><?php echo $customer['phone']; ?></td>
                            <td><?php echo $customer['address']; ?></td>
                            <td><?php echo $customer['created_at']; ?></td>
                            <td>
                                <button class="btn btn-info btn-view" data-id="<?php echo $customer['id']; ?>">View</button>
                                <button class="btn btn-warning btn-update" data-id="<?php echo $customer['id']; ?>">Update</button>
                                <button class="btn btn-danger btn-delete" data-id="<?php echo $customer['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Customer/validationCustomer.js"></script>
    <script src="assets/js/Customer/customerActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>