// FILE NÀY DÀNH CHO CÁC XỬ LÝ KHI BẤM VÀO VARIANTS CỦA SẢN PHẨM
document.addEventListener("DOMContentLoaded", function () {

    // ======================== GLOBAL CONFIG ========================
    const PRODUCT_VARIATION_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 10,
        DEFAULT_EXTRA_PARAMS: ""
    };

    const pagination = new Pagination(PRODUCT_VARIATION_CONFIG.DEFAULT_ITEMS_PER_PAGE);
    let productVariationListData = [];
    let currentProductId = null;

    // ======================== INITIALIZATION ========================
    $(document).ready(function () {
        initializePage();
    });

    function initializePage() {
        currentProductId = getParameterByName('product_id');
        console.log("Current Product ID:", currentProductId);
        if (!currentProductId) {
            console.error("Product ID not found in URL");
            showError("Product not found");
            return;
        }
        // Tạo nút thêm biến thể sản phẩm
        createAddProductVariationButton(currentProductId);
        // Load OS options
        loadOSOptions();

        // Initialize pagination
        pagination.init(function (searchValue, extraParams, page, perPage) {
            loadProductVariationData(searchValue, extraParams, page, perPage);
        });

        // Bind all events
        bindFilterEvents();
        bindTableEvents();

        // Load initial data
        pagination.loadData();
    }

    // ================ TẠO NÚT THÊM BIẾN THỂ SẢN PHẨM ===================
    function createAddProductVariationButton(currentProductId) {
        // Tạo thẻ <a> mới
        const link = document.createElement('a');
        link.href = `index.php?page=pages/Product/createVariation.php&product_Id=${currentProductId}`;
        link.classList.add('btn', 'btn-primary', 'btn-sm', 'd-flex', 'align-items-center');

        // Thêm icon vào thẻ <a>
        const icon = document.createElement('i');
        icon.classList.add('fas', 'fa-plus', 'me-1');
        link.appendChild(icon);

        // Thêm văn bản vào thẻ <a>
        link.appendChild(document.createTextNode(' Add Product'));

        // Chèn thẻ <a> vào trong phần tử có id 'filterFormContainer'
        const container = document.getElementById('filterFormContainer');
        const filterTitleDiv = container.querySelector('.d-flex.justify-content-between.align-items-center.mb-3');
        const buttonContainer = filterTitleDiv.querySelector('.d-flex.gap-2');
        buttonContainer.appendChild(link);
    }

    // ======================== DATA LOADING ========================
    async function loadProductVariationData(searchValue = "", extraParams = "", page = 1, perPage = 10) {
        try {
            showLoading();

            // Build query params
            const params = new URLSearchParams({
                product_id: currentProductId,
                limit: perPage,
                offset: (page - 1) * perPage
            });

            // Add search if exists
            if (searchValue) {
                params.append('search', searchValue);
            }

            // Add extra filters
            if (extraParams) {
                new URLSearchParams(extraParams).forEach((value, key) => {
                    params.append(key, value);
                });
            }

            // Call API
            const response = await $.ajax({
                url: `${BASE_API_URL}/api/products/variations/instances?${params.toString()}`,
                type: 'GET',
                dataType: 'json'
            });

            if (response.success) {
                productVariationListData = response.data;
                console.log("Product Variations:", productVariationListData);
                renderProductVariationTable(productVariationListData);

                // Update pagination info
                updatePaginationInfo(
                    response.meta.totalElements || 0,
                    (page - 1) * perPage,
                    perPage
                );

                // Update URL
                updateBrowserURL(params);
            }
        } catch (error) {
            console.error("Error loading variations:", error);
            showError("Could not load product variations");
        } finally {
            hideLoading();
        }
    }

    // ======================== FILTER FUNCTIONS ========================
    function bindFilterEvents() {
        // Toggle filter form
        $('#toggleFilterForm').click(function () {
            $('#filterFormContainer').slideToggle();
            $(this).find('i').toggleClass('fa-minus fa-plus');
        });

        // Quick search with debounce
        // $("#keyword").on("input", debounce(function () {
        //     const searchValue = $(this).val().trim();
        //     applyFilters({ search: searchValue });
        // }, 300));

        // Apply advanced filters
        $(".btn-filter").click(function () {
            console.log("Filter button clicked");
            applyFilters(getFormFilters());
        });

        // Reset all filters
        $("#resetFilter").click(function () {
            resetAllFilters();
        });
    }

    function applyFilters(filters = {}) {
        try {
            // Validate filters
            if (filters.price_cents_min && filters.price_cents_max &&
                parseFloat(filters.price_cents_min) > parseFloat(filters.price_cents_max)) {
                throw new Error("Minimum price must be less than maximum price");
            }

            // Reset to first page
            pagination.currentPage = 1;

            // Handle search separately
            if (filters.search) {
                pagination.setSearchValue(filters.search);
                delete filters.search;
            } else {
                pagination.setSearchValue('');
            }

            // Update extra params
            pagination.extraParams = new URLSearchParams(filters).toString();

            // Reload data
            pagination.loadData();

        } catch (error) {
            console.error("Filter error:", error);
            showError(error.message);
        }
    }

    function getFormFilters() {
        const formData = $("#filterForm").serializeArray();
        console.log("Form Data:", formData);
        const filters = {};

        formData.forEach(item => {
            if (item.value) {
                filters[item.name] = item.value;
            }
        });

        // Add keyword search if exists
        // const keyword = $("#keyword").val().trim();
        // if (keyword) {
        //     filters.search = keyword;
        // }

        return filters;
    }

    function resetAllFilters() {
        // Reset form
        $("#filterForm")[0].reset();
        // $("#keyword").val('');
        $('.select2').val(null).trigger('change');

        // Reset pagination
        pagination.currentPage = 1;
        pagination.setSearchValue('');
        pagination.extraParams = '';

        // Clear URL except product_id
        const cleanURL = window.location.pathname + '?product_id=' + currentProductId;
        window.history.pushState({}, '', cleanURL);

        // Reload data
        pagination.loadData();
    }

    // ======================== OS SELECT FUNCTIONS ========================
    function loadOSOptions() {
        $.ajax({
            url: `${BASE_API_URL}/api/products/os`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const select = $('#osSelect');
                    select.empty().append('<option value="">All OS</option>');

                    // Add OS options
                    response.data.forEach(os => {
                        select.append(new Option(os.name, os.id));
                    });

                    // Initialize Select2
                    if ($.fn.select2) {
                        select.select2({
                            placeholder: "Select OS",
                            allowClear: true
                        });
                    }

                    // Set selected value from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('os_id')) {
                        select.val(urlParams.get('os_id')).trigger('change');
                    }
                }
            },
            error: function (xhr) {
                console.error('Error loading OS:', xhr.responseText);
                showError('Error loading operating systems');
            }
        });
    }

    // ======================== TABLE RENDERING ========================
    function renderProductVariationTable(variations) {
        const table = $("#product-variations-table");
        table.empty();

        if (!variations || variations.length === 0) {
            table.html(`
            <tr>
                <td colspan="11" class="text-center py-3">No data available</td>
            </tr>
        `);
            return;
        }

        variations.forEach((variation, index) => {
            console.log("Rendering variation:", variation);
            const imageUrl = variation.image_url || "default-image.jpg";
            const row = `
        <tr class="align-middle">
            <td class="text-center">${index + 1}</td>
            <td class="text-center">${variation.product_id}</td>
            <td class="text-center">${variation.id}</td>
            <td class="text-center">
                <img src="${imageUrl}" 
                     width="60" 
                     class="img-thumbnail"
                     onerror="this.onerror=null; this.src='default-image.jpg';"
                     alt="Variation ${variation.id}">
            </td>
            <td class="text-center">${variation.sku}</td>
            <td class="text-center">${variation.watch_size_mm || '-'}</td>
            <td class="text-center">${variation.display_size_mm || '-'}</td>
            <td class="text-center">${formatCurrency(variation.price_cents || 0)}</td>
            <td class="text-center">${variation.stock_quantity || '0'}</td>
            <td class="text-center">${variation.stop_selling ? 'Yes' : 'No'}</td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-info btn-sm py-1 px-2 btn-view" data-id="${variation.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm py-1 px-2 btn-update" data-id="${variation.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm py-1 px-2 btn-delete" 
                            data-id="${variation.id}" 
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
            table.append(row);
        });
    }

    function bindTableEvents() {
        // View button
        $(document).on('click', '.btn-view', function () {
            const variationId = $(this).data('id');
            const variation = productVariationListData.find(v => v.variation_id == variationId);
            showVariationDetail(variation);
        });

        // Edit button
        $(document).on('click', '.btn-update', function () {
            const variationId = $(this).data('id');
            // Implement edit functionality
            console.log("Edit variation:", variationId);
        });

        // Delete button
        $(document).on('click', '.btn-delete', function () {
            const variationId = $(this).data('id');
            // Implement delete functionality
            console.log("Delete variation:", variationId);
        });
    }

    // ======================== DETAIL VIEW ========================
    function showVariationDetail(variation) {
        if (!variation) {
            console.error("Invalid variation data");
            return;
        }

        // Display basic info
        $("#variationId").text(variation.variation_id);
        $("#variationSku").text(variation.sku);

        // Display specifications
        $("#os_name").text(variation.os_name || "N/A");
        $("#watch_size_mm").text(variation.watch_size_mm ? `${variation.watch_size_mm}mm` : "N/A");
        $("#display_size_mm").text(variation.display_size_mm ? `${variation.display_size_mm}mm` : "N/A");
        $("#ram_bytes").text(variation.ram_bytes ? `${(variation.ram_bytes / 1024).toFixed(1)} GB` : "N/A");
        $("#rom_bytes").text(variation.rom_bytes ? `${(variation.rom_bytes / 1024).toFixed(1)} GB` : "N/A");

        // Display pricing and stock
        $("#price_cents").text(formatCurrency(variation.price_cents || 0));
        $("#stock_quantity").text(variation.stock_quantity || "0");

        // Show the modal
        $("#variationDetailModal").modal('show');
    }

    // ======================== UTILITY FUNCTIONS ========================
    function getParameterByName(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    function updatePaginationInfo(totalItems, startIndex, perPage) {
        const displayStart = totalItems > 0 ? startIndex + 1 : 0;
        const displayEnd = Math.min(startIndex + perPage, totalItems);

        pagination.updateRecordInfo(displayStart, displayEnd, totalItems);
        pagination.totalItems = totalItems;
        pagination.render(totalItems);
    }

    function updateBrowserURL(params) {
        const url = new URL(window.location);

        // Keep only product_id
        url.search = `?product_id=${currentProductId}`;

        // Add current filters
        params.forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        window.history.pushState({}, '', url);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function showLoading() {
        $("#loading-indicator").show();
    }

    function hideLoading() {
        $("#loading-indicator").hide();
    }

    function showError(message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    }

    function showSuccess(message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
        }).showToast();
    }

    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }


    // ========================== HIỆN MODAL UPDATE SẢN PHẨM ==========================
    $(document).on('click', '.btn-update', async function () {
        let productVariationId = document.getElementById("data-id");
        console.log("productVariationId ID:", productVariationId);
        // Gán giá trị ID vào input hidden trong modal
        $("#productVariationId").val(productVariationId);
        let productToUpdateModal = await fetchAPIProductOSAndVariations(productVariationId);
        console.log("Product Variation to Update:", productToUpdateModal);
        fillUpdateModal(productToUpdateModal); // Đổ dữ liệu lên modal

    });

    // Hiện dữ liệu lên modal update
    function fillUpdateModal(productData) {
        let modal = document.querySelector("#modalUpdate");
        console.log("Product to Update:", productData);

        // Gán giá trị vào các input
        if (!modal) {
            console.error("Modal không tồn tại!");
            return;
        }

        modal.querySelector("#image_name").value = productData.image_name || "";
        //modal.querySelector("#os_name").textContent = productData.os_name || "";
        modal.querySelector("#watch_size_mm").value = productData.watch_size_mm || "";
        modal.querySelector("#watch_color").value = productData.watch_color || "";
        modal.querySelector("#display_type").value = productData.display_type || "";
        modal.querySelector("#display_size_mm").value = productData.display_size_mm || "";
        modal.querySelector("#resolution_w_px").value = productData.resolution_w_px || "";
        modal.querySelector("#resolution_h_px").value = productData.resolution_h_px || "";
        modal.querySelector("#ram_bytes").value = productData.ram_bytes || "";
        modal.querySelector("#rom_bytes").value = productData.rom_bytes || "";
        modal.querySelector("#connectivity").value = productData.connectivity || "";
        modal.querySelector("#sensors").value = productData.sensor || "";
        modal.querySelector("#case_material").value = productData.case_material || "";
        modal.querySelector("#band_material").value = productData.band_material || "";
        modal.querySelector("#band_size_mm").value = productData.band_size_mm || "";
        modal.querySelector("#band_color").value = productData.band_color || "";
        modal.querySelector("#battery_life_mah").value = productData.battery_life_mah || "";
        modal.querySelector("#water_resistance_value").value = productData.water_resistance_value || "";
        modal.querySelector("#water_resistance_unit").value = productData.water_resistance_unit || "";
        modal.querySelector("#weight_miligam").value = productData.weight_milligrams || "";
        //modal.querySelector("#stock_quantity").value = productData.stock_quantity || "";
        modal.querySelector("#base_price_cents").value = productData.base_price_cents || "";
        modal.querySelector("#price_cents").value = productData.price_cents || "";
        modal.querySelector("#stop_selling").value = productData.stop_selling || "";
        // Gán ngày tháng (convert thành định dạng yyyy-MM-dd nếu chưa đúng)
        //console.log("Release Date Before Format:", productData.release_date);

        // if (productData.release_date) {
        //     let formattedDate = formatDate(productData.release_date);
        //     console.log("Formatted Release Date:", formattedDate);
        //     modal.querySelector("#release_date").value = formattedDate;
        // }


        // Mở modal bằng Bootstrap 5
        let modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
    // Hàm định dạng ngày thành yyyy-MM-dd
    function formatDate(dateString) {
        let date = new Date(dateString);
        if (isNaN(date.getTime())) {
            console.error("Ngày không hợp lệ:", dateString);
            return ""; // Trả về chuỗi rỗng nếu ngày không hợp lệ
        }
        return date.toISOString().split("T")[0]; // Chuyển thành yyyy-MM-dd
    }


    // Lấy dữ liệu từ modal khi người dùng nhấn "Save Changes"
    function getUpdatedProductVariationInfo() {
        let modal = document.querySelector("#modalUpdate");

        let productVariationInfo = {
            id: modal.querySelector("#productVariationId").value.trim(),
            watch_size_mm: parseInt(modal.querySelector("#watch_size_mm").value) || 0,
            watch_color: modal.querySelector("#watch_color").value.trim(),
            //stock_quantity: parseInt(modal.querySelector("#stock_quantity").value) || 0,
            price_cents: parseInt(modal.querySelector("#price_cents").value) || 0,
            base_price_cents: parseInt(modal.querySelector("#base_price_cents").value) || 0,
            image_name: modal.querySelector("#image_name").value.trim(),
            display_size_mm: parseInt(modal.querySelector("#display_size_mm").value) || 0,
            display_type: modal.querySelector("#display_type").value.trim(),
            resolution_h_px: parseInt(modal.querySelector("#resolution_h_px").value) || 0,
            resolution_w_px: parseInt(modal.querySelector("#resolution_w_px").value) || 0,
            ram_bytes: parseInt(modal.querySelector("#ram_bytes").value) || 0,
            rom_bytes: parseInt(modal.querySelector("#rom_bytes").value) || 0,
            connectivity: modal.querySelector("#connectivity").value.trim(),
            battery_life_mah: parseInt(modal.querySelector("#battery_life_mah").value) || 0,
            water_resistance_value: parseInt(modal.querySelector("#water_resistance_value").value) || 0,
            water_resistance_unit: modal.querySelector("#water_resistance_unit").value.trim(),
            sensor: modal.querySelector("#sensors").value.trim(), // Sửa sensors thành sensor
            case_material: modal.querySelector("#case_material").value.trim(),
            band_material: modal.querySelector("#band_material").value.trim(),
            band_size_mm: parseInt(modal.querySelector("#band_size_mm").value) || 0,
            band_color: modal.querySelector("#band_color").value.trim(),
            weight_milligrams: parseInt(modal.querySelector("#weight_miligam").value) || 0, // Sửa weight_miligam thành weight_milligrams
            //release_date: modal.querySelector("#release_date").value.trim(), // Không cần cập nhật ngày ra mắt
            stop_selling: modal.querySelector("#stop_selling").checked // Checkbox phải lấy .checked
        };

        console.log("Dữ liệu thu thập được:", productVariationInfo);
        return productVariationInfo;
    }

    // function getUpdatedProductVariationInfo() {
    //     return {
    //         band_color: "Black",
    //         band_material: "Silicone",
    //         band_size_mm: 22,
    //         base_price_cents: 1000,
    //         battery_life_mah: 500,
    //         case_material: "Aluminum",
    //         connectivity: "Bluetooth, Wi-Fi",
    //         display_size_mm: 45,
    //         display_type: "AMOLED",
    //         id: "1",
    //         image_name: "avatar.png",
    //         price_cents: 49900,
    //         ram_bytes: 2048,
    //         release_date: "2025-03-23 12:00:00",
    //         resolution_h_px: 400,
    //         resolution_w_px: 400,
    //         rom_bytes: 16384,
    //         sensor: "Heart Rate, GPS",
    //         stock_quantity: 50,
    //         stop_selling: false,
    //         watch_color: "Black",
    //         watch_size_mm: 45,
    //         water_resistance_unit: "ATM",
    //         water_resistance_value: 5,
    //         weight_milligrams: 50000
    //     };
    // }


    // ========================== CẬP NHẬT THÔNG TIN SẢN PHẨM ==========================
    $("#saveChanges").click(function () {
        let productVariationId = document.getElementById("productVariationId").value;
        console.log("productVariationId:", productVariationId);
        console.log("Lưu thay đổi");
        let updatedProductVariation = getUpdatedProductVariationInfo();
        // Kiểm tra nếu có lỗi trong dữ liệu
        if (!updatedProductVariation || !validateProductVariation(updatedProductVariation)) {
            console.error("Lỗi: Dữ liệu không hợp lệ");
            return;
        }
        // Kiểm tra dữ liệu trước khi gửi
        console.log("Dữ liệu gửi lên:", updatedProductVariation);
        let modal = document.querySelector("#modalUpdate");
        // Kiểm tra và thêm ảnh mới nếu có
        let imageFile = modal.querySelector("#image_upload").files[0];
        console.log("Image File:", imageFile);
        if (imageFile) {
            const reader = new FileReader();
            reader.onloadend = function () {
                let base64Image = reader.result.split(',')[1]; // Lấy phần base64 của ảnh
                updatedProductVariation.image_base64 = base64Image; // Thêm ảnh base64 vào dữ liệu JSON
                console.log("Base64 Image:", base64Image);
                console.log("Product Info:", updatedProductVariation);
                updateProductVariation(updatedProductVariation); // Gọi hàm cập nhật
            };
            reader.readAsDataURL(imageFile);
        } else {
            // Nếu không có ảnh mới, gửi yêu cầu PUT để cập nhật sản phẩm
            updateProductVariation(updatedProductVariation);
        }


        // Hàm cập nhật sản phẩm
        function updateProductVariation(productData) {
            // Log đối tượng JSON để kiểm tra
            console.log("Dữ liệu JSON gửi lên:", productData);

            // Gửi updatedProductVariation lên server bằng AJAX
            $.ajax({
                url: `${BASE_API_URL}/api/products/variations/instances/${updatedProductVariation.id}`, // URL API backend
                type: "PUT",
                contentType: "application/json",
                data: JSON.stringify(updatedProductVariation), // Sửa lỗi: Dùng đúng biến
                success: function (response) {
                    if (response.success) {
                        console.log("Cập nhật thành công:", response);
                        toast("Cập nhật thành công!", "success", true);
                        fetchAPIProductsVariations(); // Load lại dữ liệu sau khi cập nhật
                        $("#modalUpdate").modal("hide"); // Ẩn modal sau khi cập nhật
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi cập nhật:", error);
                    console.log("Response Text:", xhr.responseText);
                    console.log("Status:", xhr.status);
                    console.log("Status Text:", xhr.statusText);
                    toast("Lỗi khi gửi dữ liệu lên server!", "error", true);
                },
            });
        }

    });







});