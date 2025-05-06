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

    // Hàm toggleFilter 
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


    // =========================== LẤY DANH SÁCH GOOD RECEIPT NOTE ===========================
    // CALL API LẤY DANH SÁCH GOOD RECEIPT NOTE
    // $(document).ready(function () {
    //     console.log("GOOD RECEIPT NOTE");
    //     getAPIGoodReceiptNotes();
    // });
    // Hàm format tiền Việt 
    function formatCurrencyVND(amount) {
        if (isNaN(amount)) {
            console.error("Invalid amount:", amount);
            return "0 ₫";
        }
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
    /**
 * Định dạng số tiền theo chuẩn USD (Đô la Mỹ)
 * @param {number} amount - Số tiền cần định dạng
 * @returns {string} Chuỗi đã được định dạng (ví dụ: $1,234.56)
 */
    function formatCurrencyUSD(amount) {
        // Kiểm tra đầu vào có phải là số hợp lệ không
        if (isNaN(amount)) {
            console.error("Số tiền không hợp lệ:", amount);
            return "$0.00";
        }

        // Định dạng theo USD
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    // ========================= Lấy danh sách nhà cung cấp để render trong filter =========================
    $(document).ready(function () {
        // Gọi API lấy danh sách nhà cung cấp
        loadProviders();

        // Set giá trị đã chọn nếu có trong URL
        const selectedProvider = "<?= $_GET['provider_id'] ?? '' ?>";
        if (selectedProvider) {
            $('select[name="provider_id"]').val(selectedProvider).trigger('change');
        }
    });
    /**
 * Hàm gọi API lấy danh sách nhà cung cấp
 */
    function loadProviders() {
        $.ajax({
            url: `${BASE_API_URL}/api/providers`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const select = $('select[name="provider_id"]');
                    // Xóa options cũ (ngoài option đầu tiên)
                    select.find('option:not(:first)').remove();

                    // Thêm từng nhà cung cấp vào select
                    response.data.forEach(provider => {
                        select.append(new Option(
                            provider.full_name + (provider.company ? ` (${provider.company})` : ''),
                            provider.id,
                            false,
                            false
                        ));
                    });
                } else {
                    console.error('Failed to load providers:', response.message);
                    showError('Could not load provider list');
                }
            },
            error: function (xhr) {
                console.error('Error loading providers:', xhr.responseText);
                showError('Error loading provider data');
            }
        });
    }

    // ========================= Lấy danh sách nhân viên trong filter =========================
    $(document).ready(function () {
        // Gọi API lấy danh sách nhân viên
        loadStaffMembers();

        // Set giá trị đã chọn nếu có trong URL
        const selectedStaff = "<?= $_GET['staff_id'] ?? '' ?>";
        if (selectedStaff) {
            $('select[name="staff_id"]').val(selectedStaff).trigger('change');
        }
    });

    /**
     * Hàm gọi API lấy danh sách nhân viên
     */
    function loadStaffMembers() {
        $.ajax({
            url: `${BASE_API_URL}/api/users?type=employee`, // Đổi thành endpoint API lấy nhân viên
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const select = $('select[name="staff_id"]');
                    // Xóa options cũ (ngoài option đầu tiên)
                    select.find('option:not(:first)').remove();

                    // Thêm từng nhân viên vào select
                    response.data.forEach(staff => {
                        if (staff.role_name && staff.role_name === "Staff") {
                            const displayText = staff.full_name;
                            select.append(new Option(
                                displayText,
                                staff.id,
                                false,
                                false
                            ));
                        }
                    });
                } else {
                    console.error('Failed to load staff:', response.message);
                    showError('Could not load staff list');
                }
            },
            error: function (xhr) {
                console.error('Error loading staff:', xhr.responseText);
                showError('Error loading staff data');
            }
        });
    }

    // ======================== KHAI BÁO BIẾN TOÀN CỤC VÀ CẤU HÌNH ========================
    const GOODS_RECEIPT_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 5, // Số lượng mặc định mỗi trang
        DEFAULT_EXTRA_PARAMS: ""   // Tham số filter mặc định
    };

    // Khởi tạo đối tượng phân trang
    const goodsReceiptPagination = new Pagination(GOODS_RECEIPT_CONFIG.DEFAULT_ITEMS_PER_PAGE);

    // Mảng lưu trữ dữ liệu phiếu nhập kho
    let goodsReceiptListData = [];

    // ======================== KHỞI TẠO TRANG ========================
    $(document).ready(function () {
        initializeGoodsReceiptPage();  // Khởi tạo trang
        bindGoodsReceiptFilterEvents(); // Gắn sự kiện filter
    });

    /**
     * Khởi tạo trang phiếu nhập kho
     */
    function initializeGoodsReceiptPage() {
        // Cấu hình phân trang với callback function
        goodsReceiptPagination.init(function (searchValue, extraParams, page, perPage) {
            loadGoodsReceiptData(searchValue, extraParams, page, perPage);
        });

        // Tải dữ liệu ban đầu
        goodsReceiptPagination.loadData();
    }

    // ======================== CÁC HÀM XỬ LÝ DỮ LIỆU CHÍNH ========================

    /**
     * Tải dữ liệu phiếu nhập kho từ API
     * @param {string} searchValue - Từ khóa tìm kiếm
     * @param {string} extraParams - Tham số filter bổ sung
     * @param {number} page - Trang hiện tại
     * @param {number} perPage - Số lượng mỗi trang
     */
    async function loadGoodsReceiptData(searchValue = "", extraParams = "", page = 1, perPage = GOODS_RECEIPT_CONFIG.DEFAULT_ITEMS_PER_PAGE) {
        try {
            showLoading(); // Hiển thị loading

            // Tính toán offset dựa trên trang hiện tại
            const offset = (page - 1) * perPage;

            // Xây dựng query params
            const params = new URLSearchParams({
                limit: perPage,
                offset: offset
            });

            // Thêm từ khóa tìm kiếm nếu có
            if (searchValue) params.append('name', searchValue);

            // Thêm các tham số filter nếu có
            // if (extraParams) {
            //     const filters = JSON.parse(extraParams);
            //     for (const [key, value] of Object.entries(filters)) {
            //         if (value) params.append(key, value);
            //     }
            // }
            // Thêm các filter từ extraParams
            if (extraParams) {
                const extraParamsObj = new URLSearchParams(extraParams);
                extraParamsObj.forEach((value, key) => {
                    if (value) params.append(key, value);
                });
            }

            // Gọi API lấy dữ liệu
            const response = await $.ajax({
                url: `${BASE_API_URL}/api/goods_receipt_notes?${params.toString()}`,
                type: 'GET',
                dataType: 'json'
            });

            // Kiểm tra kết quả trả về
            if (!response.success) {
                throw new Error(response.message || "Lỗi khi tải dữ liệu");
            }

            // Lưu dữ liệu nhận được
            goodsReceiptListData = response.data;

            // Nếu có dữ liệu thì lấy thông tin nhà cung cấp và nhân viên
            if (goodsReceiptListData.length > 0) {
                await getProviderAndStaffData(
                    goodsReceiptListData,
                    goodsReceiptListData[0].provider_id,
                    goodsReceiptListData[0].staff_id
                );
            } else {
                renderGoodsReceiptTable([]); // Hiển thị bảng trống
            }

            // Cập nhật thông tin phân trang
            updateGoodsReceiptPaginationInfo(
                response.totalElements || 0,
                offset,
                perPage
            );

        } catch (error) {
            console.error("Lỗi khi tải dữ liệu:", error);
            showError("Không thể tải dữ liệu: " + error.message);
        } finally {
            hideLoading(); // Ẩn loading
        }
    }

    /**
     * Hiển thị dữ liệu phiếu nhập kho lên bảng
     * @param {Array} receipts - Mảng dữ liệu phiếu nhập kho
     */
    function renderGoodsReceiptTable(receipts) {
        const content = document.getElementById("good_receipt_note-table");
        if (!content) {
            console.error("Không tìm thấy bảng dữ liệu");
            return;
        }

        content.innerHTML = ""; // Xóa nội dung cũ

        // Hiển thị thông báo nếu không có dữ liệu
        if (!receipts || receipts.length === 0) {
            content.innerHTML = `<tr><td colspan="9" class="text-center">No data available</td></tr>`;
            return;
        }

        try {
            let html = '';
            // Duyệt qua từng phiếu nhập kho và tạo HTML
            receipts.forEach((receipt, index) => {
                const totalPrice = formatCurrencyUSD(receipt.total_price_cents);
                html += `
            <tr id="receipt-${receipt.id}">
                <td>${index + 1}</td>
                <td>${receipt.id || ''}</td>
                <td>${receipt.name || ''}</td>
                <td>${receipt.provider_name || 'N/A'}</td>
                <td>${receipt.staff_name || 'N/A'}</td>
                <td>${totalPrice}</td>
                <td>${receipt.quantity || 0}</td>
                <td>${formatDate(receipt.created_at)}</td>
                <td>
                    <button class="btn btn-info btn-view" data-id="${receipt.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" data-id="${receipt.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });
            content.innerHTML = html;
        } catch (error) {
            console.error("Lỗi khi hiển thị bảng:", error);
            content.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-danger">Lỗi khi tải dữ liệu</td>
            </tr>`;
        }
    }

    /**
     * Cập nhật thông tin phân trang
     * @param {number} totalItems - Tổng số mục
     * @param {number} startIndex - Vị trí bắt đầu
     * @param {number} perPage - Số lượng mỗi trang
     */
    function updateGoodsReceiptPaginationInfo(totalItems, startIndex, perPage) {
        const displayStart = totalItems > 0 ? startIndex + 1 : 0;
        const displayEnd = Math.min(startIndex + perPage, totalItems);
        const currentPage = Math.floor(startIndex / perPage) + 1;
        // Cập nhật thông tin hiển thị
        goodsReceiptPagination.updateRecordInfo(displayStart, displayEnd, totalItems);
        // Cập nhật thông tin phân trang
        goodsReceiptPagination.totalItems = totalItems;
        goodsReceiptPagination.currentPage = currentPage;
        goodsReceiptPagination.itemsPerPage = perPage;

        // Hiển thị phân trang
        goodsReceiptPagination.render(totalItems);
    }

    // ======================== CÁC HÀM LỌC VÀ TÌM KIẾM ========================

    /**
     * Gắn sự kiện cho các chức năng filter và tìm kiếm
     */
    function bindGoodsReceiptFilterEvents() {
        // Tìm kiếm nhanh với debounce
        $("#keyword").on("input", debounce(function () {
            const searchValue = $("#keyword").val().trim();
            goodsReceiptPagination.setSearchValue(searchValue);
            goodsReceiptPagination.currentPage = 1;
            goodsReceiptPagination.loadData();
        }, 300));
        // Khai bá biến toàn cục để lưu trữ dữ liệu hiện tại
        let currentReceiptData = null;
        let currentProductInstances = [];
        let currentProductVariations = [];

        // View button
        $(document).on('click', '.btn-view', async function () {
            const receiptId = $(this).data('id');
            console.log("View receipt ID:", receiptId);

            try {
                const receiptData = await getAPIGoodReceiptNoteById(receiptId);
                console.log("Dữ liệu phiếu nhập:", receiptData);

                const productInstances = await getAPIProductInstanceByReceiptId(receiptId);
                console.log("Danh sách sản phẩm trong phiếu:", productInstances);

                // Lấy danh sách variation IDs duy nhất
                const variationIds = [...new Set(productInstances.map(inst => inst.product_variation_id))];

                // Gọi tất cả variation song song bằng Promise.all
                const variationPromises = variationIds.map(id => getAPIProductVariationId(id));
                const productVariations = await Promise.all(variationPromises);
                console.log("Chi tiết biến thể sản phẩm:", productVariations);
                // Lưu lại dữ liệu để in Excel
                currentReceiptData = receiptData;
                currentProductInstances = productInstances;
                currentProductVariations = productVariations;
                showReceiptDetail(receiptData, productInstances, productVariations);

            } catch (error) {
                console.error(error);
                showError('Không thể tải dữ liệu phiếu nhập');
            }
        });

        // Xóa phiếu nhập kho
        $(document).on('click', '.btn-delete', function () {
            const receiptId = $(this).data('id');
            console.log("Xóa phiếu nhập ID:", receiptId);
            if (confirm("Bạn có chắc chắn muốn xóa phiếu nhập này không?")) {
                $.ajax({
                    url: `${BASE_API_URL}/api/goods_receipt_notes/${receiptId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            showSuccess("Xóa phiếu nhập thành công!");
                            // Tải lại dữ liệu sau khi xóa
                            goodsReceiptPagination.loadData();
                        } else {

                            showError("Xóa phiếu nhập thất bại: " + response.message);
                        }
                    },
                    error: function (xhr) {
                        const error = JSON.parse(xhr.responseText);
                        alert("Lỗi khi xóa phiếu nhập: " + error.message);
                    }
                });
            }
        });

        // Lọc nâng cao
        $(document).on("click", ".btn-filter", function () {
            console.log("Bấm nút lọc nâng cao");
            applyAdvancedFilters();
        });

        // Reset bộ lọc
        $("#resetFilter").click(function () {
            $("#filterForm")[0].reset();
            $("#keyword").val('');
            // pagination.setSearchValue('');
            // pagination.loadData();
        });

        // In PDF
        $(document).on('click', '.btn-print', async function () {
            // if (!currentReceiptData || !currentProductInstances || !currentProductVariations) {
            //     console.log("Chưa có dữ liệu phiếu nhập!");
            //     return;
            // }

            // Gọi hàm in Excel
            await exportReceiptToExcel(currentReceiptData, currentProductInstances, currentProductVariations);
        });



    }

    // CALL API LẤY PHIẾU NHẬP KHO THEO ID
    function getAPIGoodReceiptNoteById(receiptId) {
        return $.ajax({
            url: `${BASE_API_URL}/api/goods_receipt_notes?id=${receiptId}`,
            type: 'GET',
            dataType: 'json'
        }).then(response => {
            if (response.success) {
                return response.data[0];
            } else {
                throw new Error(response.message);
            }
        });
    }

    //CALL API LẤY PRODUCT INSTANCE THEO ID PHIẾU NHẬP KHO
    function getAPIProductInstanceByReceiptId(receiptId) {
        return $.ajax({
            url: `${BASE_API_URL}/api/products/instances?goods_receipt_note_id=${receiptId}`,
            type: 'GET',
            dataType: 'json'
        }).then(response => {
            if (response.success) {
                return response.data;
            } else {
                throw new Error(response.message);
            }
        });
    }

    //CALL API LẤY PRODUCT VARIATION THEO ID PHIẾU NHẬP KHO
    function getAPIProductVariationId(variationId) {
        return $.ajax({
            url: `${BASE_API_URL}/api/products/variations/${variationId}`,
            type: 'GET',
            dataType: 'json'
        }).then(response => {
            if (response.success) {
                return response.data;
            } else {
                throw new Error(response.message);
            }
        });
    }

    /**
     * Show chi tiết phiếu nhập kho
     * @param {number} receiptId - ID của phiếu nhập kho
     * */
    // function showReceiptDetail(receiptData) {
    //     // Display basic info
    //     $('#receipt_id').text(receiptData.id || '-');
    //     $('#created_at').text(receiptData.created_at ? formatDate(receiptData.created_at) : '-');
    //     $('#staff_name').text(receiptData.staff?.name || '-');
    //     $('#provider_name').text(receiptData.provider?.name || '-');
    //     $('#total_price').text(receiptData.total_price ? formatCurrency(receiptData.total_price) : '-');

    //     // Display product list
    //     const $itemsContainer = $('#receipt_items');
    //     $itemsContainer.empty();

    //     if (receiptData.items?.length > 0) {
    //         receiptData.items.forEach((item, index) => {
    //             $itemsContainer.append(`
    //             <tr>
    //                 <td>${index + 1}</td>
    //                 <td>${item.sku || '-'}</td>
    //                 <td>${item.product_name || '-'}</td>
    //                 <td>${item.product_variation_id || '-'}</td>
    //                 <td>${item.price ? formatCurrency(item.price) : '-'}</td>
    //                 <td>${item.quantity || '0'}</td>
    //                 <td>${item.price && item.quantity ? formatCurrency(item.price * item.quantity) : '-'}</td>
    //             </tr>
    //         `);
    //         });
    //     } else {
    //         $itemsContainer.append('<tr><td colspan="7" class="text-center">No products found</td></tr>');
    //     }

    //     // Show modal
    //     const modal = new bootstrap.Modal(document.getElementById('modalReceiptDetail'));
    //     modal.show();
    // }

    // Format date as MM/DD/YYYY
    function showReceiptDetail(receipt, productInstances, productVariations) {
        // Hiển thị thông tin phiếu nhập
        $('#receipt_id').text(receipt.id || '-');
        $('#created_at').text(receipt.created_at ? formatDate(receipt.created_at) : '-');
        $('#staff_name').text(receipt.staff_name || '-');
        $('#provider_name').text(receipt.provider_name || '-');
        $('#total_price').text(receipt.total_price_cents ? formatCurrency(receipt.total_price_cents) : '-');

        // Gom nhóm các instance theo variation_id để đếm số lượng
        const variationMap = {}; // { variation_id: { quantity: x, instances: [...], variationInfo: {...} } }

        productInstances.forEach(inst => {
            const varId = inst.product_variation_id;
            if (!variationMap[varId]) {
                variationMap[varId] = { quantity: 0, instances: [], variationInfo: null };
            }
            variationMap[varId].quantity += 1;
            variationMap[varId].instances.push(inst);
        });

        // Gắn thông tin variation tương ứng vào mỗi nhóm
        productVariations.forEach(variation => {
            if (variationMap[variation.id]) {
                variationMap[variation.id].variationInfo = variation;
            }
        });

        // Hiển thị danh sách sản phẩm trong phiếu nhập
        const $itemsContainer = $('#receipt_items');
        $itemsContainer.empty();

        const variationIds = Object.keys(variationMap);
        if (variationIds.length === 0) {
            $itemsContainer.append('<tr><td colspan="7" class="text-center">Không có sản phẩm</td></tr>');
            return;
        }

        variationIds.forEach((varId, index) => {
            const item = variationMap[varId];
            const info = item.variationInfo;
            const price = info?.price_cents || 0;
            const quantity = item.quantity;
            const total = (price * quantity);

            $itemsContainer.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${item.instances[0].sku || '-'}</td>
                <td>${info?.case_material || 'N/A'} - ${info?.band_color || 'N/A'} (${info?.display_type || 'N/A'})</td>
                <td>${varId}</td>
                <td>${formatCurrency(price)}</td>
                <td>${quantity}</td>
                <td>${formatCurrency(total)}</td>
            </tr>
        `);
        });

        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('modalReceiptDetail'));
        modal.show();
    }

    /**
     * Xuất phiếu nhập kho ra file Excel
     * @param {Object} receiptData - Dữ liệu phiếu nhập kho
     * @param {Array} productInstances - Danh sách sản phẩm trong phiếu nhập kho
     * @param {Array} productVariations - Danh sách biến thể sản phẩm
     * */
    async function exportReceiptToExcel(receipt, productInstances, productVariations) {
        // Hiển thị loading
        const exportBtn = document.getElementById('exportExcelBtn');
        const originalBtnText = exportBtn.innerHTML;
        exportBtn.disabled = true;
        //     exportBtn.innerHTML = `
        //     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        //     Đang xuất file...
        // `;
        // Tạo và hiển thị spinner toàn màn hình
        const spinnerHTML = `
        <div id="globalSpinner" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        ">
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', spinnerHTML);

        try {
            // Thêm delay nhỏ để đảm bảo spinner hiển thị
            await new Promise(resolve => setTimeout(resolve, 50));

            const wb = XLSX.utils.book_new();
            const excelData = [
                ["THÔNG TIN PHIẾU NHẬP", "", "", ""],
                ["Mã phiếu:", receipt.id || '-', "", ""],
                ["Ngày tạo:", receipt.created_at ? formatDate(receipt.created_at) : '-', "", ""],
                ["Nhân viên:", receipt.staff_name || '-', "", ""],
                ["Nhà cung cấp:", receipt.provider_name || '-', "", ""],
                ["Tổng giá trị:", receipt.total_price_cents ? formatCurrency(receipt.total_price_cents) : '-', "", ""],
                [], // Dòng trống
                ["DANH SÁCH SẢN PHẨM", "", "", "", "", "", ""],
                ["No.", "SKU", "Product Name", "Variant (ID)", "Unit Price", "Quantity", "Amount"]
            ];

            // Gom nhóm sản phẩm
            const variationMap = productInstances.reduce((map, inst) => {
                const varId = inst.product_variation_id;
                if (!map[varId]) {
                    map[varId] = { quantity: 0, instances: [], variationInfo: null };
                }
                map[varId].quantity++;
                map[varId].instances.push(inst);
                return map;
            }, {});

            // Gắn thông tin variation
            productVariations.forEach(variation => {
                if (variationMap[variation.id]) {
                    variationMap[variation.id].variationInfo = variation;
                }
            });

            // Thêm dữ liệu sản phẩm
            Object.entries(variationMap).forEach(([varId, item], index) => {
                const variation = item.variationInfo;
                const price = variation?.price_cents || 0;
                const total = price * item.quantity;

                excelData.push([
                    index + 1,
                    item.instances[0]?.sku || '',
                    variation ? `${variation.case_material || ''} - ${variation.band_color || ''} (${variation.display_type || ''})` : '',
                    varId,
                    formatCurrency(price),
                    item.quantity,
                    formatCurrency(total)
                ]);
            });

            // Tạo sheet và định dạng
            const ws = XLSX.utils.aoa_to_sheet(excelData);
            ws['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } },
                { s: { r: 7, c: 0 }, e: { r: 7, c: 6 } }
            ];
            ws['!cols'] = [
                { wch: 5 }, { wch: 30 }, { wch: 40 },
                { wch: 15 }, { wch: 12 }, { wch: 10 }, { wch: 15 }
            ];

            XLSX.utils.book_append_sheet(wb, ws, "Phiếu nhập");
            XLSX.writeFile(wb, `PhieuNhap_${receipt.id}_${formatDate(new Date(), 'YYYYMMDD_HHmm')}.xlsx`);

        } catch (error) {
            console.error('Lỗi xuất file Excel:', error);
            alert('Có lỗi xảy ra khi xuất file. Vui lòng thử lại.');
        } finally {
            // Ẩn spinner dù thành công hay thất bại
            const spinner = document.getElementById('globalSpinner');
            if (spinner) {
                spinner.remove();
            }
        }
    }


    // Hàm phụ trợ formatDate (nếu chưa có)
    function formatDate(date, format = 'DD/MM/YYYY HH:mm') {
        if (!date) return '';
        const d = new Date(date);
        return format
            .replace('YYYY', d.getFullYear())
            .replace('MM', String(d.getMonth() + 1).padStart(2, '0'))
            .replace('DD', String(d.getDate()).padStart(2, '0'))
            .replace('HH', String(d.getHours()).padStart(2, '0'))
            .replace('mm', String(d.getMinutes()).padStart(2, '0'));
    }



    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US');
    }

    // Format currency (example: $1,000.00)
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    /**
     * Xử lý áp dụng bộ lọc nâng cao
     */
    function applyAdvancedFilters() {
        try {
            // Lấy toàn bộ dữ liệu từ form filter
            const formData = $("#filterForm").serialize();
            const filterParams = new URLSearchParams(formData);
            const searchValue = $("#keyword").val().trim();

            // Reset về trang đầu tiên khi áp dụng filter mới
            goodsReceiptPagination.currentPage = 1;

            // Cập nhật giá trị tìm kiếm
            goodsReceiptPagination.setSearchValue(searchValue);

            // Kiểm tra và validate các tham số
            const fromDate = filterParams.get('from_date');
            const toDate = filterParams.get('to_date');
            const minAmount = filterParams.get('min_amount');
            const maxAmount = filterParams.get('max_amount');

            // Validate khoảng ngày
            if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                throw new Error("Ngày bắt đầu phải trước ngày kết thúc");
            }

            // Validate khoảng giá
            if (minAmount && maxAmount && parseFloat(minAmount) > parseFloat(maxAmount)) {
                throw new Error("Giá tối thiểu phải nhỏ hơn giá tối đa");
            }

            // Tạo object params mới
            const currentParams = goodsReceiptPagination.extraParams
                ? new URLSearchParams(goodsReceiptPagination.extraParams)
                : new URLSearchParams();

            // Xóa các params cũ
            currentParams.forEach((_, key) => currentParams.delete(key));

            // Thêm params mới từ form
            filterParams.forEach((value, key) => {
                if (value && value.toString().trim() !== "") {
                    currentParams.set(key, value.toString().trim());
                }
            });

            // Cập nhật extraParams và load lại dữ liệu
            goodsReceiptPagination.extraParams = currentParams.toString();
            goodsReceiptPagination.loadData();

        } catch (error) {
            console.error("Lỗi khi áp dụng bộ lọc:", error);
            showError(error.message);
        }
    }


    // ======================== CÁC HÀM XỬ LÝ DỮ LIỆU LIÊN QUAN ========================

    /**
     * Lấy thông tin nhà cung cấp và nhân viên
     * @param {Array} receipts - Danh sách phiếu nhập kho
     * @param {number} providerId - ID nhà cung cấp
     * @param {number} staffId - ID nhân viên
     */
    async function getProviderAndStaffData(receipts, providerId, staffId) {
        try {
            showLoading();

            // Gọi song song 2 API để lấy thông tin
            const [providerResponse, staffResponse] = await Promise.all([
                $.ajax({
                    url: `${BASE_API_URL}/api/providers/${providerId}`,
                    type: 'GET',
                    dataType: 'json'
                }),
                $.ajax({
                    url: `${BASE_API_URL}/api/users/${staffId}`,
                    type: 'GET',
                    dataType: 'json'
                })
            ]);

            // Kết hợp dữ liệu
            let mergedData = receipts;
            if (providerResponse.success && staffResponse.success) {
                mergedData = receipts.map(receipt => ({
                    ...receipt,
                    provider_name: providerResponse.data.full_name,
                    staff_name: staffResponse.data.full_name
                }));
            }

            // Hiển thị dữ liệu đã kết hợp
            renderGoodsReceiptTable(mergedData);

        } catch (error) {
            console.error("Lỗi khi lấy thông tin nhà cung cấp/nhân viên:", error);
            // Hiển thị với giá trị mặc định nếu có lỗi
            renderGoodsReceiptTable(receipts.map(receipt => ({
                ...receipt,
                provider_name: "N/A",
                staff_name: "N/A"
            })));
        } finally {
            hideLoading();
        }
    }

    // ======================== CÁC HÀM TIỆN ÍCH ========================

    /**
     * Định dạng tiền tệ VND
     * @param {number} amount - Số tiền
     * @returns {string} Chuỗi đã định dạng
     */
    function formatCurrencyVND(amount) {
        if (!amount) return '0 ₫';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    /**
     * Hiển thị loading
     */
    function showLoading() {
        $("#loading-indicator").show();
    }

    /**
     * Ẩn loading
     */
    function hideLoading() {
        $("#loading-indicator").hide();
    }

    /**
     * Hàm debounce để giảm số lần gọi hàm
     * @param {Function} func - Hàm cần debounce
     * @param {number} wait - Thời gian chờ (ms)
     * @param {boolean} immediate - Gọi ngay lập tức
     * @returns {Function} Hàm đã được debounce
     */
    function debounce(func, wait, immediate) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            const later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Hiển thị thông báo lỗi
     * @param {string} message - Nội dung thông báo
     */
    function showError(message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
    }

    /**
     * Hiển thị thông báo thành công
     * @param {string} message - Nội dung thông báo
     */
    function showSuccess(message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
            }
        }).showToast();
    }

    /**
     * Định dạng ngày tháng
     * @param {string} dateString - Chuỗi ngày tháng
     * @returns {string} Ngày tháng đã định dạng
     */
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

});