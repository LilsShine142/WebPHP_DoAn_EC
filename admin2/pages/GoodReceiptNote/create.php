<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Phiếu Nhập Mới</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Thêm Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Thêm Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body class="bg-light">
    <?php
    // Fake data nhân viên - giả lập từ session hoặc database
    $current_staff = [
        'id' => 3
    ];
    ?>
    <div class="container-fluid p-4 bg-white">
        <div class="text-center border-bottom border-primary pb-2 mb-4">
            <h2 class="text-dark"><i class="bi bi-clipboard2-plus"></i> Add New Receipt</h2>
        </div>

        <form id="goodsReceiptForm">
            <!-- Thông tin chung và nhà cung cấp -->
            <div class="mb-4 p-0 d-flex flex-wrap gap-3">
                <!-- Thông tin cơ bản -->
                <div class="flex-grow-1 p-4 bg-light rounded border-start border-primary border-4" style="flex-basis: 48%; min-width: 300px;">
                    <h4 class="text-primary mb-3 fw-semibold"><i class="bi bi-info-circle"></i> General Information</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">Receipt Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="created_at" class="form-label fw-bold">Created At</label>
                            <input type="datetime-local" class="form-control" id="created_at" name="created_at" required>
                        </div>
                    </div>
                </div>

                <!-- Nhà cung cấp -->
                <div class="flex-grow-1 p-4 bg-light rounded border-start" style="flex-basis: 48%; min-width: 300px;">
                    <h4 class="text-primary mb-3 fw-semibold"><i class="bi bi-truck"></i> Supplier</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="provider_id" class="form-label fw-bold">Select Supplier</label>
                            <select class="form-select" id="provider_id" name="provider_id" required>
                                <option value="" selected disabled>-- Select Supplier --</option>
                                <option value="1">Company A</option>
                                <option value="2">Company B</option>
                                <option value="3">Company C</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="staff_id" class="form-label fw-bold">Staff</label>
                            <input type="text" class="form-control" id="staff_id_display" readonly>
                            <input type="hidden" name="staff_id" id="staff_id" value="<?php echo $current_staff['id']; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm sản phẩm -->
            <div class="mb-4 p-4 bg-light rounded border-start border-primary border-4">
                <h4 class="text-primary mb-3 fw-semibold"><i class="bi bi-plus-circle"></i> Add Product</h4>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Product</label>
                            <select class="form-select" id="product_select">
                                <option value="" selected disabled>-- Select Product --</option>
                            </select>
                        </div>
                        <div class="col-md-5" id="product_variations_col" hidden>
                            <label class="form-label fw-bold">Variation</label>
                            <select class="form-select" id="product_variation_select">
                                <option value="" selected disabled>-- Select Product Variation --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Quantity</label>
                        <input type="number" class="form-control" id="product_quantity" min="1" value="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Unit Price (cents)</label>
                        <input type="number" class="form-control" id="product_unit_price" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="btnAddProduct">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                            <button type="button" class="btn btn-success" id="btnUpdateProduct" style="display: none;">
                                <i class="bi bi-check-lg"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm đã thêm -->
            <div class="mb-4 p-4 bg-light rounded border-start border-primary border-4">
                <h4 class="text-primary mb-3 fw-semibold"><i class="bi bi-list-check"></i> Product List</h4>
                <div class="overflow-auto" style="max-height: 400px;" id="productsList">
                    <div class="alert alert-info">No products added yet</div>
                </div>
            </div>

            <!-- Tổng kết -->
            <div class="mb-4 p-4 bg-info bg-opacity-10 rounded">
                <h4 class="text-primary mb-3 fw-semibold"><i class="bi bi-calculator"></i> Summary</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #055160;">Total Quantity</label>
                            <input type="number" class="form-control" id="total_quantity" readonly value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #055160;">Total Value (cents)</label>
                            <input type="number" class="form-control" id="total_price_cents" readonly value="0">
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #055160;">Total Value (VND)</label>
                            <input type="text" class="form-control" id="total_price_vnd" readonly value="0 ₫">
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Receipt
                </button>
            </div>
        </form>
    </div>

    <script>
        const currentStaff = {
            id: <?php echo $current_staff['id']; ?>
        };
    </script>

    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/GoodReceiptNote/validationGoodReceiptNote.js"></script>
    <script src="assets/js/GoodReceiptNote/create.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>