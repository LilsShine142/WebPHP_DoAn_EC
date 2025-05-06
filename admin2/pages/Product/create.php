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
    <!-- <script>
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
    </script> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchOSList();
            // Khi danh mục thay đổi
            document.getElementById('category_id').addEventListener('change', function() {
                const selectedCategory = this.value;
                toggleFieldsByCategory(selectedCategory);

                // Nếu là smartwatch thì load OS list
                if (selectedCategory === 1) {
                    fetchOSList();
                }
            });

            // Hiển thị các trường phù hợp khi trang được tải (nếu đã chọn danh mục)
            const initialCategory = document.getElementById('category_id').value;
            if (initialCategory) {
                toggleFieldsByCategory(initialCategory);
            }
        });

        // =============== ẨN/HIỆN TRƯỜNG THEO DANH MỤC ===============
        function toggleFieldsByCategory(categoryId) {
            // Ẩn tất cả các section theo danh mục
            document.querySelectorAll('.category-specific-section').forEach(section => {
                section.style.display = 'none';
            });

            // Hiển thị section tương ứng với danh mục được chọn
            if (categoryId) {
                console.log("categoryId", categoryId);
                const sectionId = getSectionIdByCategory(categoryId);
                console.log("sectionId", sectionId);
                if (sectionId) {
                    const section = document.getElementById(sectionId);
                    if (section) {
                        section.style.display = 'block';
                    }
                }
            }
        }

        // Hàm ánh xạ category_id sang section ID tương ứng
        function getSectionIdByCategory(categoryId) {
            const categoryMap = {
                1: 'smartwatch-section',
                4: 'band-section',
                2: 'cable-section',
                3: 'charger-section'
                // Thêm các ánh xạ khác nếu cần
            };
            return categoryMap[categoryId];
        }
        // =============== LẤY DANH SÁCH OS (CHỈ CHO SMARTWATCH) ===============
        function fetchOSList() {
            $.ajax({
                url: `${BASE_API_URL}/api/products/os`,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    const select = $("#os_select");
                    select.empty().append('<option value="">Select OS</option>');

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

        // =============== CHUYỂN BƯỚC FORM ===============
        function showStep(step) {
            $('#step1').toggle(step === 1);
            $('#step2').toggle(step === 2);

            // Cập nhật chỉ số bước
            $('.step').removeClass('active');
            $(`.step:nth-child(${step})`).addClass('active');

            // Nếu chuyển sang step 2, kiểm tra lại danh mục
            if (step === 2) {
                const selectedCategory = $('#category_id').val();
                console.log("selectedCategory", selectedCategory);
                toggleFieldsByCategory(selectedCategory);
            }
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
                            <td class="fw-bold">Brand</td>
                            <td>
                                <select id="brand_id" name="brand_id" class="form-control">
                                    <option value="">Select a brand</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Model</td>
                            <td>
                                <input type="text" id="model" name="model" class="form-control" placeholder="Enter product model">
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Category</td>
                            <td>
                                <select id="category_id" name="category_id" class="form-control">
                                    <option value="">Select a category</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
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

                <!-- ===== CÁC TRƯỜNG CHUNG CHO TẤT CẢ DANH MỤC ===== -->
                <!-- Common Fields -->
                <div class="mb-3 common-fields">
                    <h5 class="text-center bg-danger text-white p-2">Pricing & Availability</h5>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="base_price_cents">Base Price (cents)</label>
                            <input type="text" id="base_price_cents" name="base_price_cents" class="form-control" placeholder="Base price in cents">
                        </div>
                        <div class="col">
                            <label class="fw-bold" for="price_cents">Selling Price (cents)</label>
                            <input type="text" id="price_cents" name="price_cents" class="form-control" placeholder="Selling price in cents">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold" for="stop_selling">Stop Selling</label>
                            <select id="stop_selling" name="stop_selling" class="form-control">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ===== SMARTWATCH ===== -->
                <div id="smartwatch-section" class="category-specific-section" style="display:none;">
                    <!-- Operating System -->
                    <div class="mb-3">
                        <h5 class="text-center bg-gray text-white p-2">Operating System</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="os_select">OS</label>
                                <select id="os_select" name="os" class="form-control">
                                    <option value="">Select OS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Watch Specifications -->
                    <div class="mb-3">
                        <h5 class="text-center bg-success text-white p-2">Watch Specifications</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="watch_size_mm">Watch Size (mm)</label>
                                <input type="text" id="watch_size_mm" name="watch_size_mm" class="form-control" placeholder="Enter size in mm">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="watch_color">Watch Color</label>
                                <input type="text" id="watch_color" name="watch_color" class="form-control" placeholder="Enter color">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="display_type">Display Type</label>
                                <input type="text" id="display_type" name="display_type" class="form-control" placeholder="AMOLED, LCD, etc.">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="display_size_mm">Display Size (mm)</label>
                                <input type="text" id="display_size_mm" name="display_size_mm" class="form-control" placeholder="Enter size in mm">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="resolution_w_px">Resolution Width (px)</label>
                                <input type="text" id="resolution_w_px" name="resolution_w_px" class="form-control" placeholder="Width in pixels">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="resolution_h_px">Resolution Height (px)</label>
                                <input type="text" id="resolution_h_px" name="resolution_h_px" class="form-control" placeholder="Height in pixels">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="ram_bytes">RAM (Bytes)</label>
                                <input type="text" id="ram_bytes" name="ram_bytes" class="form-control" placeholder="RAM size in bytes">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="rom_bytes">ROM (Bytes)</label>
                                <input type="text" id="rom_bytes" name="rom_bytes" class="form-control" placeholder="ROM size in bytes">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="connectivity">Connectivity</label>
                                <input type="text" id="connectivity" name="connectivity" class="form-control" placeholder="Bluetooth, WiFi, etc.">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="sensors">Sensors</label>
                                <input type="text" id="sensors" name="sensors" class="form-control" placeholder="Heart rate, GPS, etc.">
                            </div>
                        </div>
                    </div>

                    <!-- Materials & Construction -->
                    <div class="mb-3">
                        <h5 class="text-center bg-warning text-dark p-2">Materials & Construction</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="case_material">Case Material</label>
                                <input type="text" id="case_material" name="case_material" class="form-control" placeholder="Metal, Plastic, etc.">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="band_material">Band Material</label>
                                <input type="text" id="band_material" name="band_material" class="form-control" placeholder="Silicone, Leather, etc.">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="band_size_mm">Band Size (mm)</label>
                                <input type="text" id="band_size_mm" name="band_size_mm" class="form-control" placeholder="Band size in mm">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="band_color">Band Color</label>
                                <input type="text" id="band_color" name="band_color" class="form-control" placeholder="Band color">
                            </div>
                        </div>
                    </div>

                    <!-- Dimensions & Durability -->
                    <div class="mb-3">
                        <h5 class="text-center bg-info text-white p-2">Dimensions & Durability</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="battery_life_mah">Battery Life (mAh)</label>
                                <input type="text" id="battery_life_mah" name="battery_life_mah" class="form-control" placeholder="Battery capacity">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="water_resistance_value">Water Resistance</label>
                                <input type="text" id="water_resistance_value" name="water_resistance_value" class="form-control" placeholder="Water resistance level">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="fw-bold" for="water_resistance_unit">Water Resistance Unit</label>
                                <input type="text" id="water_resistance_unit" name="water_resistance_unit" class="form-control" placeholder="ATM, meters, etc.">
                            </div>
                            <div class="col">
                                <label class="fw-bold" for="weight_miligam">Weight (mg)</label>
                                <input type="text" id="weight_miligam" name="weight_miligam" class="form-control" placeholder="Weight in milligrams">
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Availability -->

                </div>

                <!-- ===== DÂY ĐỒNG HỒ (BAND) ===== -->
                <div id="band-section" class="category-specific-section" style="display:none;">
                    <!-- Band Specifications -->
                    <div class="mb-3">
                        <h5 class="text-center bg-success text-white p-2">Band Specifications</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold" for="band_size_mm">Size (mm)</label>
                                <input type="number" id="band_size_mm" name="band_size_mm" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Materials & Colors -->
                    <div class="mb-3">
                        <h5 class="text-center bg-warning text-dark p-2">Materials & Colors</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold" for="band_material">Material</label>
                                <select id="band_material" name="band_material" class="form-control">
                                    <option value="silicone">Silicone</option>
                                    <option value="leather">Leather</option>
                                    <option value="metal">Metal</option>
                                    <option value="nylon">Nylon</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold" for="band_color">Color</label>
                                <input type="text" id="band_color" name="band_color" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ===== CÁP SẠC (CABLE) ===== -->
                <div id="cable-section" class="category-specific-section" style="display:none;">
                    <!-- Cable Specifications -->
                    <!-- <div class="mb-3">
                        <h5 class="text-center bg-success text-white p-2">Cable Specifications</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold" for="cable_length">Length (m)</label>
                                <input type="number" step="0.1" id="cable_length" name="cable_length" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold" for="current">Current (A)</label>
                                <input type="number" step="0.1" id="current" name="current" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold" for="charging_speed">Charging Speed</label>
                                <input type="text" id="charging_speed" name="charging_speed" class="form-control" placeholder="e.g., PD 20W">
                            </div>
                        </div> -->
                    <div class="mb-3">
                        <h5 class="text-center bg-success text-white p-2">Band Specifications</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold" for="band_size_mm">Size (mm)</label>
                                <input type="number" id="band_size_mm" name="band_size_mm" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold" for="band_color">Color</label>
                        <input type="text" id="band_color" name="band_color" class="form-control">
                    </div>

                </div>

                <!-- Connectors -->
                <!-- <div class="mb-3">
                    <h5 class="text-center bg-warning text-dark p-2">Connectors</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold" for="connector_a">Connector A</label>
                            <select id="connector_a" name="connector_a" class="form-control">
                                <option value="usb-c">USB-C</option>
                                <option value="lightning">Lightning</option>
                                <option value="micro-usb">Micro USB</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold" for="connector_b">Connector B</label>
                            <select id="connector_b" name="connector_b" class="form-control">
                                <option value="usb-c">USB-C</option>
                                <option value="usb-a">USB-A</option>
                            </select>
                        </div>
                    </div> -->
            </div>
    </div>

    <!-- ===== CỦ SẠC (CHARGER) ===== -->
    <div id="charger-section" class="category-specific-section" style="display:none;">
        <!-- Charger Specifications -->
        <!-- <div class="mb-3">
                    <h5 class="text-center bg-success text-white p-2">Charger Specifications</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold" for="wattage">Wattage (W)</label>
                            <input type="number" id="wattage" name="wattage" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold" for="port_count">Port Count</label>
                            <input type="number" id="port_count" name="port_count" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold" for="input_voltage">Input Voltage</label>
                            <input type="text" id="input_voltage" name="input_voltage" class="form-control" placeholder="100-240V">
                        </div>
                    </div>
                </div> -->
        <div class="mb-3">
            <h5 class="text-center bg-success text-white p-2">Band Specifications</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="fw-bold" for="band_size_mm">Size (mm)</label>
                    <input type="number" id="band_size_mm" name="band_size_mm" class="form-control">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <label class="fw-bold" for="band_color">Color</label>
            <input type="text" id="band_color" name="band_color" class="form-control">
        </div>


    </div>

    <!-- Nút điều hướng -->
    <div class="text-end mt-4">
        <button type="button" class="btn btn-secondary" onclick="showStep(1)">
            <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="button" class="btn btn-primary" id="btn-create-save">
            Save <i class="bi bi-save"></i>
        </button>

    </div>
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