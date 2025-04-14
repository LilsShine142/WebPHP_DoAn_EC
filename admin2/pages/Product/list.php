<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Thêm vào phần head -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/simplePagination.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.min.js"></script>
    <script src="assets/Components/Pagination.js"></script>

</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Customer list</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-search d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm mb-4">
            <!-- Search Input -->
            <div class="search flex-grow-1 position-relative">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="keyword" name="keyword" class="form-control ps-5" placeholder="Search products...">
            </div>

            <!-- Nút filter -->
            <button id="toggleFilter" class="btn d-flex align-items-center justify-content-center rounded"
                style="width: 40px; height: 40px; background-color: #d5e2d2; border: none;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.249995 1.61C2.56999 4.59 5.99999 9 5.99999 9V14C5.99999 15.1 6.89999 16 7.99999 16C9.09999 16 10 15.1 10 14V9C10 9 13.43 4.59 15.75 1.61C16.26 0.95 15.79 0 14.95 0H1.03999C0.209995 0 -0.260005 0.95 0.249995 1.61Z" fill="#00A64F"></path>
                </svg>
            </button>

            <!-- Add Product Button -->
            <a href="index.php?page=pages/Product/create.php" class="btn btn-primary d-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
        </div>

        <!-- Filter Panel -->
        <div id="filterContainer" class="card p-3 my-4 mx-4 shadow-sm" style="display: none;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="m-0 text-primary">
                    <i class="fas fa-sliders-h me-2"></i>Product Filters
                </h5>
                <span id="closeIcon" class="btn-close" aria-label="Close" style="cursor:pointer"></span>
            </div>

            <form id="filterForm" method="GET">
                <div class="row g-3">
                    <!-- Row 1 -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Product ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="text" name="id" class="form-control form-control-sm" placeholder="Enter product ID" value="<?= $_GET['id'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Brand</label>
                        <select name="brand_id" class="form-select form-select-sm select2" id="brandSelect">
                            <option value="">All Brands</option>
                            <!-- Brands will be loaded via API -->
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Category</label>
                        <select name="category_id" class="form-select form-select-sm select2" id="categorySelect">
                            <option value="">All Categories</option>
                            <!-- Categories will be loaded via API -->
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Stop Selling</label>
                        <select name="stop_selling" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="1" <?= isset($_GET['stop_selling']) && $_GET['stop_selling'] == '1' ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= isset($_GET['stop_selling']) && $_GET['stop_selling'] == '0' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Stock Quantity</label>
                        <div class="input-group">
                            <input type="number" name="min_stock" class="form-control form-control-sm" placeholder="Min" value="<?= $_GET['min_stock'] ?? '' ?>">
                            <span class="input-group-text">-</span>
                            <input type="number" name="max_stock" class="form-control form-control-sm" placeholder="Max" value="<?= $_GET['max_stock'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="button" id="resetFilter" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary btn-sm btn-filter">
                        <i class="fas fa-check me-1"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Stock quantity</th>
                        <th>Stop selling</th>
                        <th>View product variations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="product-table">

                </tbody>
            </table>
            <div class="d-flex flex-wrap justify-content-between align-items-end px-2 pt-1 pb-0 bg-white">
                <div id="record-info" class="small text-muted mb-1 mb-sm-0 me-2">
                    Showing <span class="fw-medium">0-0</span> of <span class="fw-medium">0</span>
                </div>
                <div id="pagination-container" class="mt-4">
                    <nav aria-label="Page navigation" class="pb-0">
                        <ul class="pagination pagination-sm mb-0 pb-0">
                            <!-- pagination items -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- ModalView -->
    <div class="modal fade" id="modalView" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <table id="viewProductTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-start bg-dark text-white p-2" colspan="2">Product Information</th>
                                </tr>
                            </thead>
                            <tbody id="data-viewproduct-table">
                                <tr>
                                    <td class="fw-bold">Product ID</td>
                                    <td id="product_id"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Product Name</td>
                                    <td id="product_name"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Model</td>
                                    <td id="model"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Description</td>
                                    <td id="description"></td>
                                </tr>
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

    <!-- Modal Update -->
    <div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateLabel">Update Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form id="updateProductForm" enctype="multipart/form-data">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-start bg-dark text-white p-2" colspan="2">Product Information</th>
                                        <input type="hidden" id="productId" value="">
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold col-4">Product Name</td>
                                        <td><input type="text" id="product_name" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Brand ID</td>
                                        <td><input type="number" id="brand_id" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Model</td>
                                        <td><input type="text" id="model" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Category ID</td>
                                        <td><input type="number" id="category_id" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Description</td>
                                        <td><textarea id="description" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Image Name</td>
                                        <td>
                                            <input type="text" id="image_name" class="form-control" readonly>
                                            <input type="file" id="image_upload" class="form-control mt-2" accept="image/*">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Stop Selling</td>
                                        <td>
                                            <select id="stop_selling" class="form-control">
                                                <option value="false">No</option>
                                                <option value="true">Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </div>
            </div>
            <div id="loading-indicator" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <script src="assets/js/Product/Validation.js"></script>
    <script src="assets/js/Product/ProductAction.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>