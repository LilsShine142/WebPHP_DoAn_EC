<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product Variation</title>
    <!-- jQuery -->
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        .card-header {
            padding: 1.5rem;
        }

        .section-header {
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            border-radius: 0.375rem;
        }

        .form-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #0d6efd;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-top: 1rem;
            display: none;
        }

        .btn-action {
            min-width: 120px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Product Variation</h3>
            </div>

            <div class="card-body">
                <form id="productVariationForm">
                    <!-- Watch Specific Info Section -->
                    <div class="form-section">
                        <h4 class="section-header bg-secondary text-white">Watch Specific Info</h4>

                        <!-- Operating System -->
                        <div class="mb-4">
                            <h5 class="text-center bg-dark text-white p-2 rounded mb-3">Operating System</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="os_select" class="form-label required-field">OS</label>
                                    <select id="os_select" name="os_id" class="form-select" required>
                                        <option value="" selected disabled>Select Operating System</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Watch Specifications -->
                        <div class="mb-4">
                            <h5 class="text-center bg-success text-white p-2 rounded mb-3">Watch Specifications</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label for="watch_size_mm" class="form-label required-field">Watch Size (mm)</label>
                                    <input type="number" id="watch_size_mm" name="watch_size_mm" class="form-control" placeholder="Enter size in mm" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="watch_color" class="form-label required-field">Watch Color</label>
                                    <input type="text" id="watch_color" name="watch_color" class="form-control" placeholder="Enter color" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="display_type" class="form-label required-field">Display Type</label>
                                    <input type="text" id="display_type" name="display_type" class="form-control" placeholder="Enter display type" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="display_size_mm" class="form-label required-field">Display Size (mm)</label>
                                    <input type="number" id="display_size_mm" name="display_size_mm" class="form-control" placeholder="Enter size in mm" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required-field">Resolution (px)</label>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="number" id="resolution_w_px" name="resolution_w_px" class="form-control" placeholder="Width" required>
                                        </div>
                                        <div class="col">
                                            <input type="number" id="resolution_h_px" name="resolution_h_px" class="form-control" placeholder="Height" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="ram_bytes" class="form-label required-field">RAM (Bytes)</label>
                                    <input type="number" id="ram_bytes" name="ram_bytes" class="form-control" placeholder="Enter RAM size" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="rom_bytes" class="form-label required-field">ROM (Bytes)</label>
                                    <input type="number" id="rom_bytes" name="rom_bytes" class="form-control" placeholder="Enter ROM size" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="connectivity" class="form-label required-field">Connectivity</label>
                                    <input type="text" id="connectivity" name="connectivity" class="form-control" placeholder="Enter connectivity options" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="sensors" class="form-label required-field">Sensors</label>
                                    <input type="text" id="sensors" name="sensors" class="form-control" placeholder="Enter sensor types" required>
                                </div>
                            </div>
                        </div>

                        <!-- Materials & Construction -->
                        <div class="mb-4">
                            <h5 class="text-center bg-warning text-dark p-2 rounded mb-3">Materials & Construction</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="case_material" class="form-label required-field">Case Material</label>
                                    <input type="text" id="case_material" name="case_material" class="form-control" placeholder="Enter case material" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="band_material" class="form-label required-field">Band Material</label>
                                    <input type="text" id="band_material" name="band_material" class="form-control" placeholder="Enter band material" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="band_size_mm" class="form-label required-field">Band Size (mm)</label>
                                    <input type="number" id="band_size_mm" name="band_size_mm" class="form-control" placeholder="Enter size in mm" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="band_color" class="form-label required-field">Band Color</label>
                                    <input type="text" id="band_color" name="band_color" class="form-control" placeholder="Enter color" required>
                                </div>
                            </div>
                        </div>

                        <!-- Dimensions & Durability -->
                        <div class="mb-4">
                            <h5 class="text-center bg-info text-white p-2 rounded mb-3">Dimensions & Durability</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="battery_life_mah" class="form-label required-field">Battery Life (mAh)</label>
                                    <input type="number" id="battery_life_mah" name="battery_life_mah" class="form-control" placeholder="Enter mAh" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="water_resistance_value" class="form-label required-field">Water Resistance</label>
                                    <input type="number" id="water_resistance_value" name="water_resistance_value" class="form-control" placeholder="Enter value" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="water_resistance_unit" class="form-label required-field">Resistance Unit</label>
                                    <input type="text" id="water_resistance_unit" name="water_resistance_unit" class="form-control" placeholder="Enter unit" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="weight_miligam" class="form-label required-field">Weight (mg)</label>
                                    <input type="number" id="weight_miligam" name="weight_miligam" class="form-control" placeholder="Enter mg" required>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Availability -->
                        <div class="mb-4">
                            <h5 class="text-center bg-danger text-white p-2 rounded mb-3">Pricing & Availability</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="base_price_cents" class="form-label required-field">Base Price (cents)</label>
                                    <input type="number" id="base_price_cents" name="base_price_cents" class="form-control" placeholder="Enter base price" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="price_cents" class="form-label required-field">Selling Price (cents)</label>
                                    <input type="number" id="price_cents" name="price_cents" class="form-control" placeholder="Enter selling price" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold" for="stop_selling">Stop Selling</label>
                                    <select id="stop_selling" name="stop_selling" class="form-select">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Images Section -->
                        <div class="mb-4">
                            <h4 class="section-header bg-primary text-white">Images</h4>

                            <!-- Main Image -->
                            <div class="card mb-4 border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Main Image</h5>
                                    <p class="card-text text-muted">Choose a single main product image (required)</p>

                                    <div class="d-flex flex-column align-items-start">
                                        <!-- File input with custom styling -->
                                        <div class="file-input-wrapper mb-3 w-100">
                                            <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*" required
                                                style="display: none;">
                                            <label for="main_image" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-cloud-arrow-up me-2"></i> Choose Main Image
                                            </label>
                                            <div class="file-name mt-1 text-muted small" id="main_image_name">No file chosen</div>
                                        </div>

                                        <!-- Image preview -->
                                        <div class="image-preview-container text-center w-100">
                                            <img id="main_image_preview" src="#" alt="Main image preview"
                                                class="img-thumbnail d-none" style="max-height: 250px; width: auto;">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger d-none" id="remove_main_image">
                                                    <i class="bi bi-trash me-1"></i> Remove Image
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Images Section -->
                            <div class="card border-secondary mb-4">
                                <div class="card-body">
                                    <h5 class="card-title d-flex align-items-center">
                                        <i class="bi bi-images me-2"></i> Detail Images
                                    </h5>
                                    <p class="card-text text-muted small">Add multiple images to showcase product details (optional)</p>

                                    <!-- File input area -->
                                    <div class="file-input-area border rounded p-3 bg-light mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary rounded-pill" id="detail_images_count">0 files selected</span>
                                        </div>

                                        <input type="file" class="d-none" id="detail_images" name="detail_images[]" accept="image/*" multiple>
                                        <label for="detail_images" class="btn btn-outline-primary w-100 py-3">
                                            <i class="bi bi-cloud-arrow-up me-2"></i> Choose Images
                                        </label>
                                    </div>

                                    <!-- Image preview grid -->
                                    <div class="row g-2" id="detail_images_preview"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="reset" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="bi bi-save me-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast notification container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Product/Validation.js"></script>
    <script src="assets/js/Product/variationsManagement.js"></script>
</body>

</html>