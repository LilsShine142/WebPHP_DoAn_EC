<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5.3 Modal Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Thêm Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Thêm Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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
        <div class="card-search d-flex align-items-center gap-4 p-4">
            <!-- Ô tìm kiếm -->
            <div class="position-relative w-75">
                <input type="text" id="name" name="name" formcontrolname="keyword"
                    placeholder="Enter the category name you want to search..."
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

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="addCate">
                <i class="fas fa-plus">Add new category</i>
            </button>
        </div>
        <!-- CÓ THỂ BỎ BỘ LỌC THÌ CATEGORY ÍT THAM SỐ ĐỂ LỌC, CHỈ CẦN TÌM KIẾM THEO TÊN Ở Ô SEARCH LÀ ĐỦ -->
        <!-- Bộ lọc -->
        <div id="filter_cateContainer" class="card p-3 my-4 mx-4" style="display: none;"> <!-- my-3: margin trên/dưới, mx-4: margin trái/phải -->
            <div _ngcontent-obg-c192="" class="d-flex align-items-center justify-content-between" bis_skin_checked="1">
                <h6 _ngcontent-obg-c192="">Bộ lọc</h6>
                <span id="closeIcon" class="btn-close" disabled aria-label="Close" style="cursor:pointer"></span>
            </div>
            <form id="filter_cateForm" method="GET">
                <input type="hidden">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Mã User</label>
                        <input type="text" name="id" class="form-control" value="<?= $_GET['id'] ?? '' ?>">
                    </div>

                    <!-- <div class="col-md-3">
                        <label class="form-label">Thông tin liên hệ</label>
                        <input type="text" name="contact" class="form-control" placeholder="Nhập tên danh mục" value="<?= $_GET['name'] ?? '' ?>">
                    </div> -->
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-filter_cate">Áp dụng</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary resetFilter">Hủy</button>
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
                let filterContainer = document.getElementById("filter_cateContainer");
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

        <!-- updateModal -->
        <div class="modal fade" id="updateCateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Update Categories</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm" method="POST">
                            <input type="hidden" id="cateId" name="cateId">
                            <div class="row">
                                <div class="form-group">
                                    <label for="cate_name">Name:</label>
                                    <input type="text" class="form-control" id="cate_name" name="cate_name" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-button">Close</button>
                        <button type="button" class="btn btn-primary" id="saveCate">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>name</th>
                        <th>Product count</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cate-data-table">
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

    <!-- Modal thêm danh mục -->
    <div class="modal fade" id="addCateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update category information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="cateId" name="cateId">

                        <div class="form-group">
                            <label for="catename">Name</label>
                            <input type="text" class="form-control" id="catename" name="catename" required>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCateAdd">Add</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        //Dùng .then xử lý bất đồng bộ
        function getListProduct() {
            let apiProduct = `${BASE_API_URL}/api/products`;

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
                let apiProductCategory = `${BASE_API_URL}/api/products/categories`; // API lấy tất cả danh mục sản phẩm

                $.ajax({
                    url: apiProductCategory, // Đường dẫn API lấy danh sách danh mục sản phẩm
                    type: 'GET',
                    dataType: "json",
                    success: function(response) {
                        console.log("Category list:", response.data);
                        if (response.success && response.data.length > 0) {
                            let tableBody = document.getElementById('cate-data-table');
                            let html = '';

                            response.data.forEach(category => {
                                let product_count = 0;

                                productList.forEach(product => {
                                    if (product.category_id === category.id) {
                                        product_count++;
                                    }
                                });

                                html += `
                                <tr id="cate-${category.id}" class="cate-row">
                                    <td class="cate-id">${category.id}</td>
                                    <td class="cate-name">${category.name}</td>
                                    <td class="cate-product_count">${product_count}</td>
                                    <td class="cate-status">${category.status}</td>
                                    <td>
                                        <button class="btn btn-warning btn-update" data-id="${category.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-delete" data-id="${category.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            });

                            tableBody.innerHTML = html; // Cập nhật nội dung bảng sau khi vòng lặp kết thúc
                            // Thêm sự kiện tìm kiếm
                            let searchInput = document.getElementById("name");
                            console.log("searchInput", searchInput);
                            searchInput.addEventListener("input", function() {
                                let keyword = this.value.toLowerCase();
                                console.log("Keyword:", keyword);
                                let rows = document.querySelectorAll("#cate-data-table .cate-row");

                                rows.forEach(row => {
                                    let nameCell = row.querySelector(".cate-name");
                                    if (nameCell) {
                                        let nameText = nameCell.textContent.toLowerCase();
                                        row.style.display = nameText.includes(keyword) ? "" : "none";
                                    }
                                });
                            });

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