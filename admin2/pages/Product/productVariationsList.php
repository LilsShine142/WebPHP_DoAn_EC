<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- jQuery -->
    <!-- Thay thế đường dẫn cũ bằng phiên bản mới -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@4.1.4/imagesloaded.pkgd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="assets/Components/Pagination.js"></script>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Product Variations list</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card-search bg-white rounded-3 shadow-sm mb-4">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="toggleFilterForm" title="Toggle Filters">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div class="card-body p-4" id="filterFormContainer">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0 text-primary">
                        <i class="fas fa-filter me-2"></i>Advanced Filters
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Render ra nút thêm variarion tại đây trong ProductVariationActions.js -->
                    </div>
                </div>

                <form id="filterForm" method="GET">
                    <!-- Các trường filter giữ nguyên như bạn đã thiết kế -->
                    <div class="row g-3">
                        <!-- ID Filter -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small fw-bold text-muted">Variation ID</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                <input type="number" name="id" class="form-control form-control-sm" placeholder="Variation ID"
                                    value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                            </div>
                        </div>
                        <!-- OS Select -->
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label small fw-bold text-muted">Operating System</label>
                            <select name="os_id" class="form-select form-select-sm select2" id="osSelect">
                                <option value="">All OS</option>
                                <?php if (isset($_GET['os_id'])): ?>
                                    <option value="<?= (int)$_GET['os_id'] ?>" selected>
                                        Loading selected OS...
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted">Price Range</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-dollar-sign text-muted"></i></span>
                                <input type="number" name="price_cents_min" class="form-control"
                                    placeholder="Min" value="<?= htmlspecialchars($_GET['price_cents_min'] ?? '') ?>">
                                <span class="input-group-text bg-light">to</span>
                                <input type="number" name="price_cents_max" class="form-control"
                                    placeholder="Max" value="<?= htmlspecialchars($_GET['price_cents_max'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Stop Selling -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small fw-bold text-muted">Status</label>
                            <select name="stop_selling" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="1" <?= isset($_GET['stop_selling']) && $_GET['stop_selling'] == '1' ? 'selected' : '' ?>>Sold</option>
                                <option value="0" <?= isset($_GET['stop_selling']) && $_GET['stop_selling'] == '0' ? 'selected' : '' ?>>Not yet sold</option>
                            </select>
                        </div>

                        <!-- Apply Button -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" id="resetFilter" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-success btn-sm btn-filter">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <h2>Danh sách Phiên Bản Sản Phẩm</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product ID</th>
                        <th>Product Variation ID</th>
                        <th>Image</th>
                        <th>SKU</th>
                        <th>Watch Size (mm)</th>
                        <th>Display Size (mm)</th>
                        <th>Price</th>
                        <th>Stock Quantity</th>
                        <th>Stop Selling</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="product-variations-table">
                    <!-- Dữ liệu sẽ được thêm vào đây bằng JavaScript -->
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
                    <h5 class="modal-title" id="modalViewLabel">Product Variation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <table id="viewProductTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-start bg-dark text-white p-2" colspan="2">Apple Watch Series 7</th>
                                </tr>
                                <!-- Nhóm tiêu đề theo tên sản phẩm -->
                            </thead>
                            <tbody id="data-viewproduct-table">
                                <!-- Thông tin hệ điều hành -->
                                <tr>
                                    <th colspan="2" class="text-center bg-primary text-white">System Information</th>
                                </tr>
                                <tr>
                                    <td class="fw-bold col-4">Operating System</td>
                                    <td id="os_name" class="col-8"></td>
                                </tr>

                                <!-- Thông số kỹ thuật -->
                                <tr>
                                    <th colspan="2" class="text-center bg-success text-white">Specifications</th>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Watch Size (mm)</td>
                                    <td id="watch_size_mm"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Watch Color</td>
                                    <td id="watch_color"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Display Type</td>
                                    <td id="display_type"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Display Size (mm)</td>
                                    <td id="display_size_mm"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Resolution (W × H px)</td>
                                    <td id="resolution_w_px"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">RAM (Bytes)</td>
                                    <td id="ram_bytes"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ROM (Bytes)</td>
                                    <td id="rom_bytes"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Connectivity</td>
                                    <td id="connectivity"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sensors</td>
                                    <td id="sensors"></td>
                                </tr>

                                <!-- Vật liệu & Kết cấu -->
                                <tr>
                                    <th colspan="2" class="text-center bg-warning text-dark">Materials & Construction</th>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Case Material</td>
                                    <td id="case_material"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Band Material</td>
                                    <td id="band_material"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Band Size (mm)</td>
                                    <td id="band_size_mm"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Band Color</td>
                                    <td id="band_color"></td>
                                </tr>

                                <!-- Kích thước & Khả năng chống nước -->
                                <tr>
                                    <th colspan="2" class="text-center bg-info text-white">Dimensions & Durability</th>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Battery Life (mAh)</td>
                                    <td id="battery_life_mah"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Water Resistance</td>
                                    <td id="water_resistance_value"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Water Resistance Unit</td>
                                    <td id="water_resistance_unit"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Weight (mg)</td>
                                    <td id="weight_miligam"></td>
                                </tr>

                                <!-- Giá & Thời gian ra mắt -->
                                <tr>
                                    <th colspan="2" class="text-center bg-danger text-white">Pricing & Availability</th>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Stock Quantity</td>
                                    <td id="stock_quantity"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Base Price (Cents)</td>
                                    <td id="base_price_cents"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Selling Price (Cents)</td>
                                    <td id="price_cents"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Release Date</td>
                                    <td id="release_date"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Stop Selling</td>
                                    <td id="stop_selling"></td>
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
                            <!-- Product Info -->
                            <div class="mb-3 text-center bg-dark text-white p-2">
                                <h5>Apple Watch Series 7</h5>
                                <input type="hidden" id="productVariationId" value="">
                            </div>

                            <!-- Image Upload Section -->
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="fw-bold" for="image_name">Image Name</label>
                                    <input type="text" id="image_name" class="form-control" readonly value="avatar.png">
                                </div>
                                <div class="col">
                                    <label class="fw-bold" for="image_upload">Upload Image</label>
                                    <input type="file" id="image_upload" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <!-- Watch Specifications Section -->
                            <div class="mb-3">
                                <h5 class="text-center bg-success text-white p-2">Watch Specifications</h5>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="watch_size_mm">Watch Size (mm)</label>
                                        <input type="text" id="watch_size_mm" class="form-control" placeholder="Enter size (mm)">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="watch_color">Watch Color</label>
                                        <input type="text" id="watch_color" class="form-control" placeholder="Enter color">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="display_type">Display Type</label>
                                        <input type="text" id="display_type" class="form-control" placeholder="Enter display type">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="display_size_mm">Display Size (mm)</label>
                                        <input type="text" id="display_size_mm" class="form-control" placeholder="Enter display size (mm)">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <!-- Cột 1: Label Resolution (Width px) và Resolution (Height px) -->
                                    <label class="fw-bold" for="resolution_w_px">Resolution (Width x Height (px))</label>
                                    <!-- Cột 2: Input fields cho Width và Height -->
                                    <div class="col">
                                        <input type="text" id="resolution_w_px" class="form-control" placeholder="Enter width in px">

                                    </div>
                                    <div class="col">
                                        <input type="text" id="resolution_h_px" class="form-control" placeholder="Enter height in px">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="ram_bytes">RAM (Bytes)</label>
                                        <input type="text" id="ram_bytes" class="form-control" placeholder="Enter RAM size in bytes">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="rom_bytes">ROM (Bytes)</label>
                                        <input type="text" id="rom_bytes" class="form-control" placeholder="Enter ROM size in bytes">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="connectivity">Connectivity</label>
                                        <input type="text" id="connectivity" class="form-control" placeholder="Enter connectivity">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="sensors">Sensors</label>
                                        <input type="text" id="sensors" class="form-control" placeholder="Enter sensors">
                                    </div>
                                </div>
                            </div>

                            <!-- Materials & Construction Section -->
                            <div class="mb-3">
                                <h5 class="text-center bg-warning text-dark p-2">Materials & Construction</h5>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="case_material">Case Material</label>
                                        <input type="text" id="case_material" class="form-control" placeholder="Enter case material">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="band_material">Band Material</label>
                                        <input type="text" id="band_material" class="form-control" placeholder="Enter band material">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="band_size_mm">Band Size (mm)</label>
                                        <input type="text" id="band_size_mm" class="form-control" placeholder="Enter band size (mm)">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="band_color">Band Color</label>
                                        <input type="text" id="band_color" class="form-control" placeholder="Enter band color">
                                    </div>
                                </div>
                            </div>

                            <!-- Dimensions & Durability Section -->
                            <div class="mb-3">
                                <h5 class="text-center bg-info text-white p-2">Dimensions & Durability</h5>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="battery_life_mah">Battery Life (mAh)</label>
                                        <input type="text" id="battery_life_mah" class="form-control" placeholder="Enter battery life in mAh">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="water_resistance_value">Water Resistance</label>
                                        <input type="text" id="water_resistance_value" class="form-control" placeholder="Enter water resistance">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="water_resistance_unit">Water Resistance Unit</label>
                                        <input type="text" id="water_resistance_unit" class="form-control" placeholder="Enter unit of measurement">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="weight_miligam">Weight (mg)</label>
                                        <input type="text" id="weight_miligam" class="form-control" placeholder="Enter weight in mg">
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Availability Section -->
                            <div class="mb-3">
                                <h5 class="text-center bg-danger text-white p-2">Pricing & Availability</h5>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="fw-bold" for="base_price_cents">Base Price (Cents)</label>
                                        <input type="text" id="base_price_cents" class="form-control" placeholder="Enter base price in cents">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="price_cents">Selling Price (Cents)</label>
                                        <input type="text" id="price_cents" class="form-control" placeholder="Enter selling price in cents">
                                    </div>
                                    <div class="col">
                                        <label class="fw-bold" for="stop_selling">Stop Selling</label>
                                        <select id="stop_selling" class="form-select">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Product/Validation.js"></script>
    <script src="assets/js/Product/ProductVariationActions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>