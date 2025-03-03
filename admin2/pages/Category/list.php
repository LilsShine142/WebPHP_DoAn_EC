<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5.3 Modal Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

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
                <tbody id="data-table">
                    <!-- CHÈN DỮ LIỆU TỪ SCRIPT  -->
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

    <script>
        //Dùng .then xử lý bất đồng bộ
        function getListProduct() {
            let apiProduct = 'http://localhost:3000/WebPHP_DoAn_EC/api/products';

            return fetch(apiProduct)
                .then(response => response.json()) // Chuyển JSON thành Object
                .then(product => {
                    if (product.success && product.data.length > 0) {
                        console.log("response", product);
                        return product.data; // Trả về danh sách sản phẩm
                    } else {
                        throw new Error("No data available");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    return []; // Trả về mảng rỗng nếu có lỗi
                });
        }

        // Gọi hàm với `.then()`
        getListProduct().then(productList => {
            console.log("Product List:", productList);

            function fetchProductCategory() {
                let apiProductCategory = 'http://localhost:3000/WebPHP_DoAn_EC/api/products/categories'; // API lấy tất cả danh mục sản phẩm

                $.ajax({
                    url: apiProductCategory, // Đường dẫn API lấy danh sách danh mục sản phẩm
                    type: 'GET',
                    dataType: "json",
                    success: function(response) {
                        console.log("Category list:", response.data);
                        if (response.success && response.data.length > 0) {
                            let tableBody = document.getElementById('data-table');
                            let html = '';

                            response.data.forEach(category => {
                                let product_count = 0;

                                productList.forEach(product => {
                                    if (product.category_id === category.id) {
                                        product_count++;
                                    }
                                });

                                html += `
                                <tr>
                                    <td>${category.id}</td>
                                    <td>${category.name}</td>
                                    <td>${product_count}</td>
                                    <td>${category.status}</td>
                                    <td>
                                        <button class="btn btn-info btn-view" data-id="${category.id}">Xem</button>
                                        <button class="btn btn-warning btn-update" data-id="${category.id}">Sửa</button>
                                        <button class="btn btn-danger btn-delete" data-id="${category.id}">Xóa</button>
                                    </td>
                                </tr>
                            `;
                            });

                            tableBody.innerHTML = html; // Cập nhật nội dung bảng sau khi vòng lặp kết thúc
                        } else {
                            console.warn("Không có dữ liệu danh mục");
                        }
                    },
                    error: function() {
                        console.error("Lỗi khi tải danh sách danh mục");
                    }
                });
            }

            // Gọi hàm fetchProductCategory() ngay khi trang load
            $(document).ready(function() {
                fetchProductCategory();
            });
        });
    </script>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Category/validationCategory.js"></script>
    <script src="assets/js/Category/categoryActions.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>