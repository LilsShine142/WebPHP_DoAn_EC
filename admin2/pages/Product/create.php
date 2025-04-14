<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin: 0 5px;
        }

        .step.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .form-section {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
    <script>
        $(document).ready(function() {
            fetchOSList();
        });

        // ========================== CALL API LẤY DANH SÁCH OS ==========================
        function fetchOSList() {
            $.ajax({
                url: `${BASE_API_URL}/api/products/os`, // Thay API_ENDPOINT bằng đường dẫn API thực tế
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let select = $("#os_select");
                    select.empty(); // Xóa các option cũ
                    if (response.success) {
                        response.data.forEach(os => {
                            select.append(`<option value="${os.id}">${os.name}</option>`);
                        });
                    } else {
                        select.append('<option value="">No OS available</option>');
                    }
                },
                error: function() {
                    $("#os_select").html('<option value="">Failed to load OS</option>');
                }
            });
        }

        function showStep(step) {
            document.getElementById('step1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step2').style.display = step === 2 ? 'block' : 'none';

            // Update step indicator
            document.querySelectorAll('.step').forEach((el, index) => {
                if (index + 1 === step) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add New Product</h2>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" onclick="showStep(1)">Step 1: Product information</div>
            <div class="step" onclick="showStep(2)">Step 2: Variation information</div>
        </div>

        <!-- Form -->
        <form id="productForm" enctype="multipart/form-data">
            <!-- Step 1: Product Information -->
            <div id="step1" class="form-section">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-start bg-dark text-white p-2" colspan="2">Product Information</th>
                            <input type="hidden" id="productId" name="productId" value="">
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold col-4">Product Name</td>
                            <td>
                                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter product name">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Brand ID</td>
                            <td>
                                <input type="number" id="brand_id" name="brand_id" class="form-control" placeholder="Enter brand ID">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Model</td>
                            <td>
                                <input type="text" id="model" name="model" class="form-control" placeholder="Enter product model">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Category ID</td>
                            <td>
                                <input type="number" id="category_id" name="category_id" class="form-control" placeholder="Enter category ID">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Description</td>
                            <td>
                                <textarea id="description" name="description" class="form-control" placeholder="Enter product description"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Image Name</td>
                            <td>
                                <input type="text" id="image_name" name="image_name" class="form-control" readonly placeholder="Image name will appear here">
                                <input type="file" id="image_upload" name="image_upload" class="form-control mt-2" accept="image/*">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Stop Selling</td>
                            <td>
                                <select id="stop_selling" name="stop_selling" class="form-control">
                                    <option value="false">No</option>
                                    <option value="true">Yes</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-end">
                    <button type="button" class="btn btn-primary" onclick="showStep(2)">
                        Next
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Product Variation Information -->
            <div id="step2" class="form-section" style="display: none;">
                <!-- Product Info -->
                <!-- OS -->
                <div class="mb-3">
                    <h5 class="text-center bg-gray text-white p-2">Operating System</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="os_select">OS</label>
                            <select id="os_select" name="os" class="form-control">
                                <option value="">Loading...</option> <!-- Hiển thị khi chưa load API -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Watch Specifications Section -->
                <div class="mb-3">
                    <h5 class="text-center bg-success text-white p-2">Watch Specifications</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="watch_size_mm">Watch Size (mm)</label>
                            <input type="text" id="watch_size_mm" name="watch_size_mm" class="form-control" placeholder="Enter size (mm)">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="watch_color">Watch Color</label>
                            <input type="text" id="watch_color" name="watch_color" class="form-control" placeholder="Enter color">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="display_type">Display Type</label>
                            <input type="text" id="display_type" name="display_type" class="form-control" placeholder="Enter display type">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="display_size_mm">Display Size (mm)</label>
                            <input type="text" id="display_size_mm" name="display_size_mm" class="form-control" placeholder="Enter display size (mm)">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="fw-bold" for="resolution_w_px">Resolution (Width x Height (px))</label>
                        <div class="col">
                            <input type="text" id="resolution_w_px" name="resolution_w_px" class="form-control" placeholder="Enter width in px">
                        </div>
                        <div class="col">
                            <input type="text" id="resolution_h_px" name="resolution_h_px" class="form-control" placeholder="Enter height in px">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="ram_bytes">RAM (Bytes)</label>
                            <input type="text" id="ram_bytes" name="ram_bytes" class="form-control" placeholder="Enter RAM size in bytes">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="rom_bytes">ROM (Bytes)</label>
                            <input type="text" id="rom_bytes" name="rom_bytes" class="form-control" placeholder="Enter ROM size in bytes">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="connectivity">Connectivity</label>
                            <input type="text" id="connectivity" name="connectivity" class="form-control" placeholder="Enter connectivity">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="sensors">Sensors</label>
                            <input type="text" id="sensors" name="sensors" class="form-control" placeholder="Enter sensors">
                        </div>
                    </div>
                </div>

                <!-- Materials & Construction Section -->
                <div class="mb-3">
                    <h5 class="text-center bg-warning text-dark p-2">Materials & Construction</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="case_material">Case Material</label>
                            <input type="text" id="case_material" name="case_material" class="form-control" placeholder="Enter case material">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="band_material">Band Material</label>
                            <input type="text" id="band_material" name="band_material" class="form-control" placeholder="Enter band material">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="band_size_mm">Band Size (mm)</label>
                            <input type="text" id="band_size_mm" name="band_size_mm" class="form-control" placeholder="Enter band size (mm)">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="band_color">Band Color</label>
                            <input type="text" id="band_color" name="band_color" class="form-control" placeholder="Enter band color">
                        </div>
                    </div>
                </div>

                <!-- Dimensions & Durability Section -->
                <div class="mb-3">
                    <h5 class="text-center bg-info text-white p-2">Dimensions & Durability</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="battery_life_mah">Battery Life (mAh)</label>
                            <input type="text" id="battery_life_mah" name="battery_life_mah" class="form-control" placeholder="Enter battery life in mAh">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="water_resistance_value">Water Resistance</label>
                            <input type="text" id="water_resistance_value" name="water_resistance_value" class="form-control" placeholder="Enter water resistance">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="water_resistance_unit">Water Resistance Unit</label>
                            <input type="text" id="water_resistance_unit" name="water_resistance_unit" class="form-control" placeholder="Enter unit of measurement">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="weight_miligam">Weight (mg)</label>
                            <input type="text" id="weight_miligam" name="weight_miligam" class="form-control" placeholder="Enter weight in mg">
                        </div>
                    </div>
                </div>

                <!-- Pricing & Availability Section -->
                <div class="mb-3">
                    <h5 class="text-center bg-danger text-white p-2">Pricing & Availability</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="base_price_cents">Base Price (Cents)</label>
                            <input type="text" id="base_price_cents" name="base_price_cents" class="form-control" placeholder="Enter base price in cents">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="price_cents">Selling Price (Cents)</label>
                            <input type="text" id="price_cents" name="price_cents" class="form-control" placeholder="Enter selling price in cents">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="stop_selling">Stop Selling</label>
                            <select id="stop_selling" name="stop_selling" class="form-select">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="showStep(1)">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-create-save">
                        Save <i class="bi bi-save"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Product/Validation.js"></script>
    <script src="assets/js/Product/addProduct.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>