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
// ========================= Tạo dữ liệu tạm thời (đến lúc kết nối database sẽ xóa phần này) =========================
$categories = [
    [
        'id' => 1,
        'name' => 'Men\'s Smartwatch',
        'product_count' => 15,
        'status' => 'Active',
    ],
    [
        'id' => 2,
        'name' => 'Women\'s Smartwatch',
        'product_count' => 12,
        'status' => 'Active',
    ],
    [
        'id' => 3,
        'name' => 'Kids Smartwatch',
        'product_count' => 8,
        'status' => 'Active',
    ],
    [
        'id' => 4,
        'name' => 'Straps & Accessories',
        'product_count' => 20,
        'status' => 'Active',
    ],
    [
        'id' => 5,
        'name' => 'Sports & Fitness Smartwatches',
        'product_count' => 18,
        'status' => 'Active',
    ],
    [
        'id' => 6,
        'name' => 'Luxury Smartwatches',
        'product_count' => 5,
        'status' => 'Active',
    ],
    [
        'id' => 7,
        'name' => 'Smartwatches for Seniors',
        'product_count' => 7,
        'status' => 'Active',
    ],
    [
        'id' => 8,
        'name' => 'Smartwatch Chargers & Docks',
        'product_count' => 12,
        'status' => 'Active',
    ],
];


?>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Category list</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-search" style="display: flex; margin: 15px;">
            <div class="search" style="width: 80%; margin-right: 30px;">
                <input type="text" class="form-control" placeholder="Search...">
            </div>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="fas fa-plus">Add new category</i>
            </button>

            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Launch static backdrop modal
            </button> -->
            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ...
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>name</th>
                        <th>Product count</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo $category['name']; ?></td>
                            <td><?php echo $category['product_count']; ?></td>
                            <td><?php echo $category['status']; ?></td>
                            <td>
                                <button class="btn btn-info btn-view" data-id="<?php echo $category['id']; ?>">View</button>
                                <button class="btn btn-warning btn-update" data-id="<?php echo $category['id']; ?>">Update</button>
                                <button class="btn btn-danger btn-delete" data-id="<?php echo $category['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- viewModal -->
    <div class="modal fade" id="modalView" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewLabel">Category Details</h5>
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

    <!-- Modal cập nhật thông tin danh mục -->
    <div class="modal fade" id="formModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update category information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="customerId" name="customerId">

                        <div class="form-group">
                            <label for="fullname">Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
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
    <script src="assets/js/Category/validationCategory.js"></script>
    <script src="assets/js/Category/categoryActions.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>