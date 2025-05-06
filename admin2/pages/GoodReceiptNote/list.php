<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- In Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Import file pagination -->
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
        <div class="card-search d-flex align-items-center gap-4 p-4">
            <!-- Ô tìm kiếm -->
            <div class="position-relative w-75 search">
                <input type="text" class="form-control" id="keyword" name="keyword" formcontrolname="keyword" placeholder="Search...">
            </div>
            <!-- Nút filter -->
            <button id="toggleFilter" class="btn d-flex align-items-center justify-content-center rounded"
                style="width: 40px; height: 40px; background-color: #d5e2d2; border: none;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.249995 1.61C2.56999 4.59 5.99999 9 5.99999 9V14C5.99999 15.1 6.89999 16 7.99999 16C9.09999 16 10 15.1 10 14V9C10 9 13.43 4.59 15.75 1.61C16.26 0.95 15.79 0 14.95 0H1.03999C0.209995 0 -0.260005 0.95 0.249995 1.61Z" fill="#00A64F"></path>
                </svg>
            </button>
            <!-- Nút thêm mới -->
            <a href="index.php?page=pages/GoodReceiptNote/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add new product
            </a>
        </div>
        <!-- Bộ lọc -->
        <div id="filterContainer" class="card p-3 my-4 mx-4 shadow-sm" style="display: none;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="m-0 text-primary">
                    <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                </h5>
                <span id="closeIcon" class="btn-close" aria-label="Close" style="cursor:pointer"></span>
            </div>

            <form id="filterForm" method="GET">
                <div class="row g-3">
                    <!-- Row 1 -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Receipt ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                            <input type="text" name="id" class="form-control form-control-sm" placeholder="Enter ID" value="<?= $_GET['id'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Provider</label>
                        <select name="provider_id" class="form-select form-select-sm select2">
                            <option value="">All Providers</option>
                            <!-- Danh sách sẽ được thêm bằng JavaScript -->
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Staff</label>
                        <select name="staff_id" class="form-select form-select-sm select2">
                            <option value="">All Staff</option>
                            <!-- Danh sách sẽ được thêm bằng JavaScript -->
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date Range</label>
                        <div class="input-group input-daterange">
                            <input type="date" name="from_date" class="form-control form-control-sm" value="<?= $_GET['from_date'] ?? '' ?>">
                            <span class="input-group-text">to</span>
                            <input type="date" name="to_date" class="form-control form-control-sm" value="<?= $_GET['to_date'] ?? '' ?>">
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Amount Range (VND)</label>
                        <div class="input-group">
                            <input type="number" name="min_amount" class="form-control form-control-sm" placeholder="Min" value="<?= $_GET['min_amount'] ?? '' ?>">
                            <span class="input-group-text">-</span>
                            <input type="number" name="max_amount" class="form-control form-control-sm" placeholder="Max" value="<?= $_GET['max_amount'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="button" id="resetFilter" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-success btn-sm btn-filter">
                        <i class="fas fa-filter me-1"></i> Apply Filters
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
                        <th>Name</th>
                        <th>Provider</th>
                        <th>Staff</th>
                        <th>Total Price</th>
                        <th>Quantity</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="good_receipt_note-table">
                    <!-- VIẾT MÃ JS CALL API VÀ RENDER DỮ LIỆU TẠI ĐÂY -->
                </tbody>
            </table>
            <div class="d-flex flex-wrap justify-content-between align-items-end px-2 pt-1 pb-0 bg-white">
                <div id="record-info" class="small text-muted mb-1 mb-sm-0 me-2">
                    <!-- Hiển thị thông tin số lượng bản ghi ở đây -->
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

    <!-- Modal View Detail -->
    <div class="modal fade" id="modalReceiptDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalReceiptDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalReceiptDetailLabel">Goods Receipt Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="fw-bold">Receipt ID:</label>
                                    <p id="receipt_id" class="text-muted">-</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold">Receipt Date:</label>
                                    <p id="created_at" class="text-muted">-</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold">Staff:</label>
                                    <p id="staff_name" class="text-muted">-</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="fw-bold">Supplier:</label>
                                    <p id="provider_name" class="text-muted">-</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Total Amount:</label>
                                    <p id="total_price" class="text-danger fw-bold">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Product List</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th>SKU</th>
                                            <th>Product Name</th>
                                            <th>Variant (ID)</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="receipt_items">
                                        <!-- Product data will be added by JavaScript -->
                                        <tr>
                                            <td colspan="7" class="text-center">No products found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportExcelBtn">
                        <i class="fas fa-file-excel btn-print"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/GoodReceiptNote/validationGoodReceiptNote.js"></script>
    <script src="assets/js/GoodReceiptNote/goodReceiptNoteActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>