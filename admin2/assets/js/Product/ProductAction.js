document.addEventListener("DOMContentLoaded", function () {

    //========================================= Toggle =========================================
    const toggleFilterBtn = document.getElementById("toggleFilter");
    const closeFilterBtn = document.getElementById("closeIcon");
    const filterSection = document.getElementById("filterContainer");

    // Kiểm tra sự tồn tại của các phần tử trước khi thêm event listener
    if (toggleFilterBtn && closeFilterBtn && filterSection) {
        // Thêm sự kiện click cho nút toggle
        toggleFilterBtn.addEventListener("click", toggleFilter);

        // Thêm sự kiện click cho nút đóng
        closeFilterBtn.addEventListener("click", toggleFilter);

        // Đảm bảo filterSection có style.display mặc định
        filterSection.style.display = filterSection.style.display || 'none';
    } else {
        console.error("Không tìm thấy một hoặc nhiều phần tử cần thiết cho filter");
        if (!toggleFilterBtn) console.error("Thiếu phần tử #toggleFilter");
        if (!closeFilterBtn) console.error("Thiếu phần tử #closeIcon");
        if (!filterSection) console.error("Thiếu phần tử #filterContainer");
    }

    // Hàm toggleFilter được cải tiến
    function toggleFilter() {
        try {
            // Lấy lại phần tử để đảm bảo nó vẫn tồn tại
            const currentFilterSection = document.getElementById("filterContainer");
            if (!currentFilterSection) {
                throw new Error("Phần tử filterContainer không tồn tại khi thực hiện toggle");
            }

            // Sử dụng classList để toggle thay vì style trực tiếp
            //currentFilterSection.classList.toggle("hidden");

            // Hoặc nếu muốn dùng style.display
            currentFilterSection.style.display =
                currentFilterSection.style.display === 'none' ? 'block' : 'none';

            // Cập nhật trạng thái nút (nếu cần)
            // const isVisible = !currentFilterSection.classList.contains("hidden");
            // console.log(`Filter section is now ${isVisible ? 'visible' : 'hidden'}`);

        } catch (error) {
            console.error("Lỗi khi thực hiện toggle filter:", error);
            // Xử lý lỗi hoặc hiển thị thông báo cho người dùng
            showError("Có lỗi khi thao tác với bộ lọc");
        }
    }

    // ========================================== END Toggle =========================================

    //========================================= CẤU HÌNH CHO FORM FILTER=========================================
    $(document).ready(function () {
        // Gọi API lấy danh sách brands và categories
        loadBrands();
        loadCategories();

        // Set giá trị đã chọn từ URL nếu có
        const selectedBrand = "<?= $_GET['brand_id'] ?? '' ?>";
        const selectedCategory = "<?= $_GET['category_id'] ?? '' ?>";

        if (selectedBrand) {
            $('select[name="brand_id"]').val(selectedBrand).trigger('change');
        }

        if (selectedCategory) {
            $('select[name="category_id"]').val(selectedCategory).trigger('change');
        }
    });

    /**
     * Hàm gọi API lấy danh sách brands
     */
    function loadBrands() {
        $.ajax({
            url: `${BASE_API_URL}/api/products/brands`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const select = $('select[name="brand_id"]');
                    // Xóa options cũ (giữ lại option đầu tiên)
                    select.find('option:not(:first)').remove();

                    // Thêm từng brand vào select
                    response.data.forEach(brand => {
                        select.append(new Option(
                            brand.name,  // Hiển thị tên brand
                            brand.id,   // Giá trị là id của brand
                            false,      // Không selected mặc định
                            false       // Không disabled
                        ));
                    });
                } else {
                    console.error('Failed to load brands:', response.message);
                    showError('Could not load brand list');
                }
            },
            error: function (xhr) {
                console.error('Error loading brands:', xhr.responseText);
                showError('Error loading brand data');
            }
        });
    }

    /**
     * Hàm gọi API lấy danh sách categories
     */
    function loadCategories() {
        $.ajax({
            url: `${BASE_API_URL}/api/products/categories`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const select = $('select[name="category_id"]');
                    // Xóa options cũ (giữ lại option đầu tiên)
                    select.find('option:not(:first)').remove();

                    // Thêm từng category vào select
                    response.data.forEach(category => {
                        select.append(new Option(
                            category.name,  // Hiển thị tên category
                            category.id,   // Giá trị là id của category
                            false,        // Không selected mặc định
                            false         // Không disabled
                        ));
                    });
                } else {
                    console.error('Failed to load categories:', response.message);
                    showError('Could not load category list');
                }
            },
            error: function (xhr) {
                console.error('Error loading categories:', xhr.responseText);
                showError('Error loading category data');
            }
        });
    }

    //========================================= END CẤU HÌNH CHO FORM FILTER=========================================

    // =========================================== CHẠY CHƯƠNG TRÌNH =========================================
    // Thêm cờ kiểm tra
    let productInitialized = false;
    $(document).ready(function () {
        if (!productInitialized) {
            productInitialized = true;

            console.log("Initializing product page...");
            // Khởi tạo pagination
            productPagination.init(filterProducts, null, PRODUCTS_CONFIG.DEFAULT_EXTRA_PARAMS);

            // Load dữ liệu ban đầu
            setTimeout(() => {
                productPagination.loadData();
            }, 100);

            // Load brands và categories cho filter
            bindProductFilterEvents();
        }
    });
    // //=========================================== CẤU HÌNH TOÀN CỤC =========================================

    // // Thêm phần cấu hình đầu file
    // const PRODUCTS_CONFIG = {
    //     DEFAULT_ITEMS_PER_PAGE: 5,
    //     DEFAULT_EXTRA_PARAMS: "stop_selling=false"
    // };

    // // Tạo instance pagination riêng cho product
    // const productPagination = new Pagination(PRODUCTS_CONFIG.DEFAULT_ITEMS_PER_PAGE);

    // // Hàm chính để fetch và xử lý sản phẩm
    // function filterProducts(searchValue = "", extraParams = PRODUCTS_CONFIG.DEFAULT_EXTRA_PARAMS, page = 1, limit = productPagination.itemsPerPage) {
    //     console.log("Filter Products - limit:", limit, "page:", page);
    //     const offset = (page - 1) * limit;
    //     let apiUrl = `${BASE_API_URL}/api/products?limit=${limit}&offset=${offset}`;

    //     // Thêm tham số tìm kiếm nếu có
    //     if (searchValue) {
    //         apiUrl += `&name=${encodeURIComponent(searchValue)}`;
    //     }

    //     // Thêm extra params nếu có
    //     if (extraParams) {
    //         apiUrl += `&${extraParams}`;
    //     }

    //     console.log("API URL:", apiUrl); // Debug URL

    //     const brandsAPI = `${BASE_API_URL}/api/products/brands`;
    //     const categoriesAPI = `${BASE_API_URL}/api/products/categories`;

    //     showLoading();

    //     // Gọi API song song
    //     Promise.all([
    //         $.ajax({ url: apiUrl, type: "GET", dataType: "json" }),
    //         $.ajax({ url: brandsAPI, type: "GET", dataType: "json" }),
    //         $.ajax({ url: categoriesAPI, type: "GET", dataType: "json" })
    //     ])
    //         .then(([productsResponse, brandsResponse, categoriesResponse]) => {
    //             if (!productsResponse.success || !brandsResponse.success || !categoriesResponse.success) {
    //                 throw new Error("API response not successful");
    //             }

    //             // Xử lý dữ liệu
    //             const brandMap = createBrandMap(brandsResponse.data);
    //             const categoryMap = createCategoryMap(categoriesResponse.data);

    //             const mergedProducts = mergeProductData(productsResponse.data, brandMap, categoryMap);

    //             // Hiển thị dữ liệu
    //             //renderProductList(mergedProducts, offset);
    //             prepareProductDataWithStock(mergedProducts).then((productsWithStock) => {
    //                 renderProductList(productsWithStock, offset);
    //             });


    //             // Cập nhật thông tin phân trang
    //             updatePaginationInfo(productsResponse.totalElements, offset, limit);
    //         })
    //         .catch(error => {
    //             console.error("Lỗi API:", error);
    //             showError("Không thể tải dữ liệu sản phẩm");
    //         })
    //         .finally(() => {
    //             hideLoading();
    //         });
    // }

    // // Các hàm helper
    // function createBrandMap(brands) {
    //     return Object.fromEntries(brands.map(b => [b.id, b.name]));
    // }

    // function createCategoryMap(categories) {
    //     return Object.fromEntries(categories.map(c => [c.id, c.name]));
    // }

    // function mergeProductData(products, brandMap, categoryMap) {
    //     return products.map(product => ({
    //         ...product,
    //         brand_name: brandMap[product.brand_id] || "Không xác định",
    //         category_name: categoryMap[product.category_id] || "Không xác định",
    //         stop_selling: product.stop_selling ? 'Yes' : 'No'
    //     }));
    // }
    // function updatePaginationInfo(totalItems, startIndex, perPage) {
    //     const displayStart = totalItems > 0 ? startIndex + 1 : 0;
    //     const displayEnd = Math.min(startIndex + perPage, totalItems);
    //     const currentPage = Math.floor(startIndex / perPage) + 1;
    //     // Cập nhật thông tin hiển thị
    //     productPagination.updateRecordInfo(displayStart, displayEnd, totalItems);
    //     // Cập nhật thông tin phân trang
    //     productPagination.totalItems = totalItems;
    //     productPagination.currentPage = currentPage;
    //     productPagination.itemsPerPage = perPage;

    //     // Render lại phân trang
    //     productPagination.render(totalItems);
    // }

    // // Hàm hiển thị danh sách sản phẩm
    // function renderProductList(products, offset) {
    //     const tableBody = document.getElementById('product-table');
    //     if (!tableBody) {
    //         console.error("Không tìm thấy bảng sản phẩm");
    //         return;
    //     }

    //     if (products.length === 0) {
    //         tableBody.innerHTML = `<tr><td colspan="10" class="text-center">No data available</td></tr>`;
    //         return;
    //     }

    //     tableBody.innerHTML = products.map((product, index) => {

    //         const rowNumber = offset + index + 1;
    //         // const imageUrl = product.image_name
    //         //     ? `${BASE_API_URL}/uploads/products/${product.image_name}`
    //         //     : 'default-image.jpg';
    //         // Chỉ hiện ảnh ở variation, nên bỏ dòng này <td class="text-center"><img src="${imageUrl}" width="50" class="img-thumbnail" alt="${product.name}"></td>
    //         return `
    //     <tr class="align-middle">
    //         <td class="text-center">${rowNumber}</td>
    //         <td class="text-center">${product.id}</td>
            
    //         <td>${product.name}</td>
    //         <td>${product.brand_name}</td>
    //         <td>${product.category_name}</td>
    //         <td class="text-center">${product.stock_quantity || 0}</td>
    //         <td class="text-center">${product.stop_selling}</td>
    //         <td class="text-center">
    //             <a href="index.php?page=pages/Product/productVariationsList.php&product_id=${product.id}" 
    //                class="btn btn-primary btn-sm py-1 px-2">
    //                <i class="bi bi-boxes"></i> Variants
    //             </a>
    //         </td>
    //         <td class="text-center">
    //             <div class="d-flex gap-1 justify-content-center">
    //                 <button class="btn btn-info btn-sm py-1 px-2 btn-view" data-id="${product.id}" title="View">
    //                     <i class="fas fa-eye"></i>
    //                 </button>
    //                 <button class="btn btn-warning btn-sm py-1 px-2 btn-update" data-id="${product.id}" title="Edit">
    //                     <i class="fas fa-edit"></i>
    //                 </button>
    //                 <button class="btn btn-danger btn-sm py-1 px-2 btn-delete" data-id="${product.id}" title="Delete">
    //                     <i class="fas fa-trash"></i>
    //                 </button>
    //             </div>
    //         </td>
    //     </tr>`;
    //     }).join('');

    //     addProductButtonEvents();
    // }

    // // Hàm xử lý tìm kiếm sản phẩm
    // // function handleProductSearch() {
    // //     const searchValue = document.getElementById('keyword').value;
    // //     productPagination.currentPage = 1;
    // //     filterProducts(searchValue, productPagination.extraParams, productPagination.currentPage);
    // // }

    // // Hàm thêm sự kiện cho các nút sản phẩm
    // function addProductButtonEvents() {
    //     $(document).off('click', '.btn-view, .btn-update, .btn-delete');

    //     $(document).on('click', '.btn-view', function () {
    //         const id = $(this).data('id');
    //         console.log("View product:", id);
    //         // Thêm logic xem chi tiết
    //     });

    //     $(document).on('click', '.btn-update', function () {
    //         const id = $(this).data('id');
    //         console.log("Update product:", id);
    //         // Thêm logic cập nhật
    //     });

    //     $(document).on('click', '.btn-delete', function () {
    //         const id = $(this).data('id');
    //         if (confirm("Bạn chắc chắn muốn xóa sản phẩm này?")) {
    //             console.log("Delete product:", id);
    //             // Thêm logic xóa
    //         }
    //     });
    // }


    // =========================================== CẤU HÌNH TOÀN CỤC =========================================
    const PRODUCTS_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 5,
        DEFAULT_EXTRA_PARAMS: "stop_selling=false",
        SEARCH_DELAY: 500 // Thời gian chờ sau khi nhập để tìm kiếm (ms)
    };

    // Tạo instance pagination riêng cho product
    const productPagination = new Pagination(PRODUCTS_CONFIG.DEFAULT_ITEMS_PER_PAGE);

    // =========================================== CÁC HÀM CHÍNH =========================================

    /**
     * Hàm chính để fetch và xử lý sản phẩm
     * @param {string} searchValue - Giá trị tìm kiếm
     * @param {string} extraParams - Tham số bổ sung cho API
     * @param {number} page - Trang hiện tại
     * @param {number} limit - Số lượng sản phẩm mỗi trang
     */
    function filterProducts(
        searchValue = "",
        extraParams = PRODUCTS_CONFIG.DEFAULT_EXTRA_PARAMS,
        page = 1,
        limit = productPagination.itemsPerPage
    ) {
        console.log("Filter Products - limit:", limit, "page:", page);
        const offset = (page - 1) * limit;
        let apiUrl = `${BASE_API_URL}/api/products?limit=${limit}&offset=${offset}`;

        // Thêm tham số tìm kiếm nếu có
        if (searchValue) {
            apiUrl += `&name=${encodeURIComponent(searchValue)}`;
        }

        // Thêm extra params nếu có
        if (extraParams) {
            apiUrl += `&${extraParams}`;
        }

        console.log("API URL:", apiUrl);

        const brandsAPI = `${BASE_API_URL}/api/products/brands`;
        const categoriesAPI = `${BASE_API_URL}/api/products/categories`;

        showLoading();

        // Gọi API song song
        Promise.all([
            $.ajax({ url: apiUrl, type: "GET", dataType: "json" }),
            $.ajax({ url: brandsAPI, type: "GET", dataType: "json" }),
            $.ajax({ url: categoriesAPI, type: "GET", dataType: "json" })
        ])
            .then(([productsResponse, brandsResponse, categoriesResponse]) => {
                if (!productsResponse.success || !brandsResponse.success || !categoriesResponse.success) {
                    throw new Error("API response not successful");
                }

                // Xử lý dữ liệu
                const brandMap = createBrandMap(brandsResponse.data);
                const categoryMap = createCategoryMap(categoriesResponse.data);
                const mergedProducts = mergeProductData(productsResponse.data, brandMap, categoryMap);

                // Hiển thị dữ liệu
                prepareProductDataWithStock(mergedProducts).then((productsWithStock) => {
                    renderProductList(productsWithStock, offset);
                    updatePaginationInfo(productsResponse.totalElements, offset, limit);
                });
            })
            .catch(error => {
                console.error("Lỗi API:", error);
                showError("Không thể tải dữ liệu sản phẩm");
            })
            .finally(() => {
                hideLoading();
            });
    }

    // =========================================== CÁC HÀM HỖ TRỢ =========================================

    function createBrandMap(brands) {
        return Object.fromEntries(brands.map(b => [b.id, b.name]));
    }

    function createCategoryMap(categories) {
        return Object.fromEntries(categories.map(c => [c.id, c.name]));
    }

    function mergeProductData(products, brandMap, categoryMap) {
        return products.map(product => ({
            ...product,
            brand_name: brandMap[product.brand_id] || "Không xác định",
            category_name: categoryMap[product.category_id] || "Không xác định",
            stop_selling: product.stop_selling ? 'Yes' : 'No'
        }));
    }

    function updatePaginationInfo(totalItems, startIndex, perPage) {
        const displayStart = totalItems > 0 ? startIndex + 1 : 0;
        const displayEnd = Math.min(startIndex + perPage, totalItems);
        const currentPage = Math.floor(startIndex / perPage) + 1;

        productPagination.updateRecordInfo(displayStart, displayEnd, totalItems);
        productPagination.totalItems = totalItems;
        productPagination.currentPage = currentPage;
        productPagination.itemsPerPage = perPage;
        productPagination.render(totalItems);
    }

    /**
     * Hiển thị danh sách sản phẩm
     * @param {Array} products - Danh sách sản phẩm
     * @param {number} offset - Vị trí bắt đầu
     */
    function renderProductList(products, offset) {
        const tableBody = document.getElementById('product-table');
        if (!tableBody) {
            console.error("Không tìm thấy bảng sản phẩm");
            return;
        }

        if (products.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="10" class="text-center">No data available</td></tr>`;
            return;
        }

        tableBody.innerHTML = products.map((product, index) => {
            const rowNumber = offset + index + 1;
            return `
        <tr class="align-middle">
            <td class="text-center">${rowNumber}</td>
            <td class="text-center">${product.id}</td>
            <td>${product.name}</td>
            <td>${product.brand_name}</td>
            <td>${product.category_name}</td>
            <td class="text-center">${product.stock_quantity || 0}</td>
            <td class="text-center">${product.stop_selling}</td>
            <td class="text-center">
                <a href="index.php?page=pages/Product/productVariationsList.php&product_id=${product.id}" 
                   class="btn btn-primary btn-sm py-1 px-2">
                   <i class="bi bi-boxes"></i> Variants
                </a>
            </td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-info btn-sm py-1 px-2 btn-view" data-id="${product.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm py-1 px-2 btn-update" data-id="${product.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm py-1 px-2 btn-delete" data-id="${product.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
        }).join('');

        addProductButtonEvents();
    }

    /**
     * Thêm sự kiện cho các nút trên bảng sản phẩm
     */
    function addProductButtonEvents() {
        // Xử lý sự kiện cho nút View
        $('.btn-view').off('click').on('click', function () {
            const productId = $(this).data('id');
            // Xử lý xem chi tiết sản phẩm
            console.log("View product:", productId);
        });

        // Xử lý sự kiện cho nút Edit
        $('.btn-update').off('click').on('click', function () {
            const productId = $(this).data('id');
            // Xử lý chỉnh sửa sản phẩm
            console.log("Edit product:", productId);
        });

        // Xử lý sự kiện cho nút Delete
        $('.btn-delete').off('click').on('click', function () {
            const productId = $(this).data('id');
            // Xử lý xóa sản phẩm
            console.log("Delete product:", productId);
        });
    }


    // Các hàm utility
    function showLoading() {
        const loader = $("#loading-indicator");
        if (loader.length) {
            loader.show();
        } else {
            console.warn("Không tìm thấy loading indicator");
        }
    }

    function hideLoading() {
        const loader = $("#loading-indicator");
        if (loader.length) {
            loader.hide();
        }
    }

    function showError(message) {
        console.error(message);
        // Có thể thay thế bằng toast notification hoặc modal đẹp hơn
        alert(message);
    }


    // ======================== CÁC HÀM LỌC VÀ TÌM KIẾM PRODUCT ========================

    /**
     * Gắn sự kiện cho các chức năng filter và tìm kiếm product
     */
    function bindProductFilterEvents() {
        // Tìm kiếm nhanh với debounce
        $("#keyword").on("input", debounce(function () {
            const searchValue = $("#keyword").val().trim();
            productPagination.setSearchValue(searchValue);
            productPagination.currentPage = 1;
            productPagination.loadData();
        }, 300));

        // Lọc nâng cao
        $(document).on("click", ".btn-filter", function () {
            applyProductAdvancedFilters();
        });

        // Reset bộ lọc
        $("#resetFilter").click(function () {
            $("#filterForm")[0].reset();
            $("#keyword").val('');
            // pagination.setSearchValue('');
            // pagination.loadData();
        });
    }

    /**
     * Xử lý áp dụng bộ lọc nâng cao cho product
     */
    function applyProductAdvancedFilters() {
        try {
            // Lấy toàn bộ dữ liệu từ form filter
            const formData = $("#filterForm").serialize();
            const filterParams = new URLSearchParams(formData);
            const searchValue = $("#keyword").val().trim();

            // Reset về trang đầu tiên khi áp dụng filter mới
            productPagination.currentPage = 1;

            // Cập nhật giá trị tìm kiếm
            productPagination.setSearchValue(searchValue);

            // Tạo object params mới
            const currentParams = productPagination.extraParams
                ? new URLSearchParams(productPagination.extraParams)
                : new URLSearchParams();

            // Xóa các params cũ
            currentParams.forEach((_, key) => currentParams.delete(key));

            // Thêm params mới từ form (chỉ thêm nếu có giá trị)
            filterParams.forEach((value, key) => {
                if (value && value.toString().trim() !== "") {
                    // Xử lý đặc biệt cho các select2 (brand, category)
                    if (key === 'brand_id' || key === 'category_id') {
                        if (value !== '') {
                            currentParams.set(key, value);
                        }
                    } else {
                        currentParams.set(key, value);
                    }
                }
            });

            // Cập nhật extraParams và load lại dữ liệu
            productPagination.extraParams = currentParams.toString();
            productPagination.loadData();

        } catch (error) {
            console.error("Lỗi khi áp dụng bộ lọc sản phẩm:", error);
            showError(error.message);
        }
    }

    // Hàm debounce (nếu chưa có)
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

    // ================================= CHI TIẾT SẢN PHẨM =================================
    // CALL API LẤY CHI TIẾT SẢN PHẨM
    function fetchAPIProductDetail(productId) {
        // Kiểm tra productId hợp lệ
        if (!productId) {
            return Promise.reject("Product ID không hợp lệ");
        }

        let APIProductDetailurl = `${BASE_API_URL}/api/products/${productId}`;

        return new Promise((resolve, reject) => {
            $.ajax({
                url: APIProductDetailurl,
                type: "GET",
                contentType: "application/json",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        resolve(response.data);  // Trả về dữ liệu khi thành công
                    } else {
                        reject("Lỗi khi tải dữ liệu chi tiết sản phẩm");
                    }
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 404) {
                        reject("Sản phẩm không tồn tại");
                    } else {
                        reject("Lỗi API: " + error);
                    }
                }
            });
        });
    }

    // ========================== HIỆN MODAL CHI TIẾT SẢN PHẨM ========================== 
    $(document).on('click', '.btn-view', async function () {
        let productId = $(this).data('id');
        console.log("Product ID:", productId);

        try {
            let productDetail = await fetchAPIProductDetail(productId);
            console.log("Product Detail:", productDetail);

            if (productDetail) {
                showDataProduct(productDetail); // Hiển thị chi tiết sản phẩm
            } else {
                alert("Không tìm thấy thông tin sản phẩm!");
            }
        } catch (error) {
            alert("Failed to fetch product details!");
        }
    });



    function showDataProduct(product) {
        console.log("Product:", product);
        if (!product) {
            console.error("Invalid product data.");
            return;
        }

        // Thông tin cơ bản
        $("#product_id").text(product.id ?? "No data available");
        $("#product_name").text(product.name ?? "No data available");
        $("#model").text(product.model ?? "No data available");
        $("#description").text(product.description ?? "No data available");

        // Show modal
        $("#modalView").modal("show");
    }

    // ========================== HIỆN MODAL UPDATE SẢN PHẨM ==========================
    $(document).on('click', '.btn-update', async function () {
        let productId = $(this).data('id');
        console.log("Product ID:", productId);
        let productToUpdateModal = await fetchAPIProductDetail(productId);
        console.log("Product to Update:", productToUpdateModal);
        fillUpdateModal(productToUpdateModal); // Đổ dữ liệu lên modal

    });

    // Hiện dữ liệu lên modal update
    function fillUpdateModal(product) {
        let modal = document.querySelector("#modalUpdate");

        modal.querySelector("#productId").value = product.id || "";
        modal.querySelector("#product_name").value = product.name || "";
        modal.querySelector("#brand_id").value = product.brand_id || "";
        modal.querySelector("#model").value = product.model || "";
        modal.querySelector("#category_id").value = product.category_id || "";
        modal.querySelector("#description").value = product.description || "";
        modal.querySelector("#image_name").value = product.image_name || "";
        modal.querySelector("#stop_selling").checked = product.stop_selling || false;

        // Show modal
        $("#modalUpdate").modal("show");
    }

    // Lấy dữ liệu từ modal khi người dùng nhấn "Save Changes"
    function getUpdatedProductInfo() {
        let modal = document.querySelector("#modalUpdate");
        let productInfo = {
            id: modal.querySelector("#productId").value,
            name: modal.querySelector("#product_name").value,
            brand_id: modal.querySelector("#brand_id").value,
            model: modal.querySelector("#model").value,
            category_id: modal.querySelector("#category_id").value,
            description: modal.querySelector("#description").value,
            image_name: modal.querySelector("#image_name").value,  // Tên ảnh nếu có từ trước
            stop_selling: modal.querySelector("#stop_selling").checked,
        };

        return productInfo;
    }


    // ========================== CẬP NHẬT THÔNG TIN SẢN PHẨM ==========================
    $("#saveChanges").click(function () {
        let modal = document.querySelector("#modalUpdate");
        let productInfo = getUpdatedProductInfo();
        if (!validateProduct(productInfo)) {
            return;
        }
        // Kiểm tra và thêm ảnh mới nếu có
        let imageFile = modal.querySelector("#image_upload").files[0];
        if (imageFile) {
            const reader = new FileReader();
            reader.onloadend = function () {
                let base64Image = reader.result.split(',')[1]; // Lấy phần base64 của ảnh
                productInfo.image_base64 = base64Image; // Thêm ảnh base64 vào dữ liệu JSON
                console.log("Base64 Image:", base64Image);
                console.log("Product Info:", productInfo);
                // Gửi yêu cầu PUT để cập nhật sản phẩm
                updateProduct(productInfo);
            };
            reader.readAsDataURL(imageFile);
        } else {
            // Nếu không có ảnh mới, gửi yêu cầu PUT để cập nhật sản phẩm
            updateProduct(productInfo);
        }
    });

    // Hàm cập nhật sản phẩm
    function updateProduct(productData) {
        // Log đối tượng JSON để kiểm tra
        console.log("Dữ liệu JSON gửi lên:", productData);

        // Gửi yêu cầu PUT đến API dưới dạng JSON
        $.ajax({
            url: `${BASE_API_URL}/api/products/${productData.id}`, // API endpoint
            type: "PUT",
            contentType: "application/json", // Đặt contentType là application/json
            data: JSON.stringify(productData), // Chuyển đối tượng JSON thành chuỗi
            success: function (response) {
                if (response.success) {
                    clearAllErrors(); // Xóa tất cả lỗi trước khi cập nhật
                    console.log("Cập nhật thành công:", response);
                    toast("Cập nhật thành công!", "success", true);
                    fetchAPIProducts(); // Load lại dữ liệu sau khi cập nhật
                    $("#modalUpdate").modal("hide"); // Ẩn modal sau khi cập nhật
                } else {
                    toast("Cập nhật thất bại!", "error", true);
                }
            },
            error: function (xhr, status, error) {
                console.error("Lỗi cập nhật:", error);
                toast("Lỗi khi gửi dữ liệu lên server!", "error", true);
            }
        });
    }

    // =========================== XÓA SẢN PHẨM ==========================


    // Lấy tất cả variation theo id của sản phẩm
    async function getVariationByProductId(productId) {
        try {
            const response = await $.ajax({
                url: `${BASE_API_URL}/api/products/variations?product_id=${productId}`,
                type: "GET",
                dataType: "json"
            });

            if (response.success) {
                return response.data;
            } else {
                console.error("Lỗi khi tải dữ liệu biến thể sản phẩm");
                return [];
            }
        } catch (error) {
            console.error("Lỗi API:", error);
            return [];
        }
    }

    async function prepareProductDataWithStock(products) {
        const preparedProducts = await Promise.all(products.map(async (product) => {
            const variations = await getVariationByProductId(product.id);
            const totalStock = totalStockQuantityProduct(variations);
            return {
                ...product,
                stock_quantity: totalStock
            };
        }));
        return preparedProducts;
    }



    // Hàm tính tổng stock quantity của variation cho sản phẩm
    function totalStockQuantityProduct(variationList) {
        return variationList.reduce((total, item) => total + (item.stock_quantity || 0), 0);
    }


});
