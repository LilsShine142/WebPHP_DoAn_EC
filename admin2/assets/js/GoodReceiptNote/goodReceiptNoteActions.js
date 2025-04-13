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




    // function getAPIGoodReceiptNotes(receiptId) {
    //     let goodReceiptNoteAPIURL = receiptId ? `${BASE_API_URL}/api/goods_receipt_notes/${receiptId}` : `${BASE_API_URL}/api/goods_receipt_notes`;
    //     console.log("RECEIPT ID", receiptId);
    //     $.ajax({
    //         url: goodReceiptNoteAPIURL,
    //         method: "GET",
    //         dataType: "json",
    //         success: function (response) {
    //             if (response.success) {
    //                 const goodReceiptNodeData = response.data;
    //                 console.log("goodReceiptNodeData DATA ", goodReceiptNodeData);
    //                 let providerId = goodReceiptNodeData[0].provider_id;
    //                 let staffId = goodReceiptNodeData[0].staff_id;
    //                 getProviderAndStaffData(goodReceiptNodeData, providerId, staffId);
    //             }
    //         },
    //         error: function (xhr) {
    //             let error = JSON.parse(xhr.responseText);
    //             alert("Error: " + error.message);
    //         }
    //     })
    // }

    // //Call api lấy tên của nhà cung cấp và tên của nhân viên để render trong good receipt note
    // async function getProviderAndStaffData(goodReceiptNodeData, providerId, staffId) {

    //     let providerAPIURL = `${BASE_API_URL}/api/providers/${providerId}`;
    //     let staffAPIURL = `${BASE_API_URL}/api/users/${staffId}`;
    //     console.log("PROVIDER URL", providerAPIURL);
    //     console.log("STAFF URL", staffAPIURL);
    //     if (!providerId || !staffId) {
    //         console.error("Không có dữ liệu nhà cung cấp hoặc nhân viên");
    //         return;
    //     }
    //     // Gọi API song song 
    //     Promise.all([
    //         $.ajax({ url: providerAPIURL, type: "GET", dataType: "json" }),
    //         $.ajax({ url: staffAPIURL, type: "GET", dataType: "json" }),
    //     ]).then(([providerResponse, staffResponse]) => {
    //         if (providerResponse.success && staffResponse.success) {
    //             let provider = providerResponse.data;
    //             let staff = staffResponse.data;
    //             console.log("Provider", provider);
    //             console.log("Staff", staff);

    //             // Gộp dữ liệu vào good Receipt Node
    //             let mergedgoodReceiptNodeDatas = goodReceiptNodeData.map(receipt => ({
    //                 ...receipt,
    //                 provider_name: provider.full_name || "Không xác định",
    //                 staff_name: staff.full_name || "Không xác định",
    //             }));

    //             console.log("Merged mergedgoodReceiptNodeDatas:", mergedgoodReceiptNodeDatas);
    //             renderGooReceiptNoteToTable(mergedgoodReceiptNodeDatas);
    //         } else {
    //             console.error("Lỗi khi tải dữ liệu good receipt note, provider hoặc staff");
    //         }
    //     })
    //         .catch(error => {
    //             console.error("Lỗi API:", error);
    //         });
    // }

    // // RENDER DỮ LIỆU RA BẢNG
    // function renderGooReceiptNoteToTable(goodReceiptNodeDatas) {
    //     let goodReceiptNoteTable = document.getElementById("good_receipt_note-table");
    //     console.log("goodReceiptNoteTable", goodReceiptNoteTable);
    //     goodReceiptNoteTable.innerHTML = "";
    //     console.log("goodReceiptNodeDatas", goodReceiptNodeDatas);
    //     goodReceiptNodeDatas.forEach((receipt, index) => {
    //         let total_price_cents = formatCurrencyVND(receipt.total_price_cents);
    //         console.log("format tiền", total_price_cents);
    //         goodReceiptNoteTable.innerHTML += `
    //     <tr>
    //         <td>${index + 1}</td>
    //             <td>${receipt.id}</td>
    //             <td>${receipt.name}</td>
    //             <td>${receipt.provider_name}</td>
    //             <td>${receipt.staff_name}</td>
    //             <td>${total_price_cents}</td>
    //             <td>${receipt.quantity}</td>
    //             <td>${receipt.created_at}</td>
    //             <td>
    //                 <button class="btn btn-info btn-view" data-id="${receipt.id}">
    //                     <i class="fas fa-eye"></i>
    //                 </button>
    //                 <button class="btn btn-warning btn-update" data-id="${receipt.id}">
    //                     <i class="fas fa-edit"></i>
    //                 </button>
    //                 <button class="btn btn-danger btn-delete" data-id="${receipt.id}">
    //                     <i class="fas fa-trash"></i>
    //                 </button>
    //             </td>
    //     </tr>
    //     `;
    //     });
    // }















    // // ======================== GLOBAL VARIABLES AND CONFIG ========================
    // const GOODS_RECEIPT_CONFIG = {
    //     DEFAULT_ITEMS_PER_PAGE: 5,
    //     DEFAULT_EXTRA_PARAMS: ""
    // };

    // const goodsReceiptPagination = new Pagination(GOODS_RECEIPT_CONFIG.DEFAULT_ITEMS_PER_PAGE);
    // let goodsReceiptListData = [];

    // $(document).ready(function () {
    //     initializeGoodsReceiptPage();
    //     bindGoodsReceiptFilterEvents();
    // });

    // function initializeGoodsReceiptPage() {
    //     goodsReceiptPagination.init(function (searchValue, extraParams, page, perPage) {
    //         loadGoodsReceiptData(searchValue, extraParams, page, perPage);
    //     });
    //     goodsReceiptPagination.loadData();
    // }

    // async function loadGoodsReceiptData(searchValue = "", extraParams = "", page = 1, perPage = GOODS_RECEIPT_CONFIG.DEFAULT_ITEMS_PER_PAGE) {
    //     try {
    //         showLoading();

    //         const offset = (page - 1) * perPage;
    //         const params = new URLSearchParams({
    //             limit: perPage,
    //             offset: offset
    //         });

    //         if (searchValue) params.append('search', searchValue);

    //         if (extraParams) {
    //             const filters = JSON.parse(extraParams);
    //             for (const [key, value] of Object.entries(filters)) {
    //                 if (value) params.append(key, value);
    //             }
    //         }

    //         const response = await $.ajax({
    //             url: `${BASE_API_URL}/api/goods_receipt_notes?${params.toString()}`,
    //             type: 'GET',
    //             dataType: 'json'
    //         });

    //         if (!response.success) {
    //             throw new Error(response.message || "Failed to load data");
    //         }

    //         goodsReceiptListData = response.data;

    //         if (goodsReceiptListData.length > 0) {
    //             await getProviderAndStaffData(goodsReceiptListData,
    //                 goodsReceiptListData[0].provider_id,
    //                 goodsReceiptListData[0].staff_id);
    //         } else {
    //             renderGoodsReceiptTable([]);
    //         }

    //         updateGoodsReceiptPaginationInfo(
    //             response.totalElements || 0,
    //             offset,
    //             perPage
    //         );

    //     } catch (error) {
    //         console.error("Error loading data:", error);
    //         showError("Could not load data: " + error.message);
    //     } finally {
    //         hideLoading();
    //     }
    // }

    // function renderGoodsReceiptTable(receipts) {
    //     const content = document.getElementById("goods-receipt-table");
    //     if (!content) {
    //         console.error("Table element not found");
    //         return;
    //     }

    //     content.innerHTML = "";

        // if (!receipts || receipts.length === 0) {
        //     content.innerHTML = `<tr><td colspan="9" class="text-center">No data available</td></tr>`;
        //     return;
        // }

    //     try {
    //         let html = '';
    //         receipts.forEach((receipt, index) => {
    //             const totalPrice = formatCurrencyVND(receipt.total_price_cents);
    //             html += `
    //         <tr id="receipt-${receipt.id}">
    //             <td>${index + 1}</td>
    //             <td>${receipt.id || ''}</td>
    //             <td>${receipt.name || ''}</td>
    //             <td>${receipt.provider_name || 'N/A'}</td>
    //             <td>${receipt.staff_name || 'N/A'}</td>
    //             <td>${totalPrice}</td>
    //             <td>${receipt.quantity || 0}</td>
    //             <td>${formatDate(receipt.created_at)}</td>
    //             <td>
    //                 <button class="btn btn-info btn-view" data-id="${receipt.id}">
    //                     <i class="fas fa-eye"></i>
    //                 </button>
    //                 <button class="btn btn-warning btn-update" data-id="${receipt.id}">
    //                     <i class="fas fa-edit"></i>
    //                 </button>
    //                 <button class="btn btn-danger btn-delete" data-id="${receipt.id}">
    //                     <i class="fas fa-trash"></i>
    //                 </button>
    //             </td>
    //         </tr>`;
    //         });
    //         content.innerHTML = html;
    //     } catch (error) {
    //         console.error("Error rendering table:", error);
    //         content.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>`;
    //     }
    // }

    // function updateGoodsReceiptPaginationInfo(totalItems, startIndex, perPage) {
    //     const recordInfo = document.getElementById('goods-receipt-record-info');
    //     if (recordInfo) {
    //         const endIndex = Math.min(startIndex + perPage, totalItems);
    //         const displayStart = totalItems > 0 ? startIndex + 1 : 0;
    //         recordInfo.textContent = `Showing ${displayStart}-${endIndex} of ${totalItems} items`;
    //     }

    //     // Update pagination info
    //     goodsReceiptPagination.totalItems = totalItems;
    //     goodsReceiptPagination.currentPage = Math.floor(startIndex / perPage) + 1;
    //     goodsReceiptPagination.itemsPerPage = perPage;

    //     // Re-render pagination
    //     goodsReceiptPagination.render(totalItems);
    // }

    // // ======================== FILTER AND SEARCH FUNCTIONS ========================
    // function bindGoodsReceiptFilterEvents() {
    //     // Quick search with debounce
    //     $("#goods-receipt-keyword").on("input", debounce(function () {
    //         const searchValue = $(this).val().trim();
    //         goodsReceiptPagination.setSearchValue(searchValue);
    //         goodsReceiptPagination.currentPage = 1;
    //         goodsReceiptPagination.loadData();
    //     }, 300));

    //     // Advanced filter
    //     $(document).on("click", ".btn-goods-receipt-filter", function () {
    //         applyGoodsReceiptAdvancedFilters();
    //     });

    //     // Reset filter
    //     $("#goods-receipt-resetFilter").click(function () {
    //         $("#goods-receipt-filterForm")[0].reset();
    //         goodsReceiptPagination.setExtraParams("");
    //         goodsReceiptPagination.setSearchValue("");
    //         goodsReceiptPagination.currentPage = 1;
    //         goodsReceiptPagination.loadData();
    //     });
    // }

    // function applyGoodsReceiptAdvancedFilters() {
    //     const filters = {
    //         from_date: $("#goods-receipt-filterForm input[name='from_date']").val(),
    //         to_date: $("#goods-receipt-filterForm input[name='to_date']").val(),
    //         provider_id: $("#goods-receipt-filterForm select[name='provider_id']").val(),
    //         status: $("#goods-receipt-filterForm select[name='status']").val()
    //     };

    //     // Update extraParams and reload data
    //     goodsReceiptPagination.setExtraParams(JSON.stringify(filters));
    //     goodsReceiptPagination.currentPage = 1;
    //     goodsReceiptPagination.loadData();
    // }

    // // ======================== PROVIDER AND STAFF DATA FUNCTIONS ========================
    // async function getProviderAndStaffData(receipts, providerId, staffId) {
    //     try {
    //         showLoading();

    //         // Call both APIs in parallel
    //         const [providerResponse, staffResponse] = await Promise.all([
    //             $.ajax({
    //                 url: `${BASE_API_URL}/api/providers/${providerId}`,
    //                 type: 'GET',
    //                 dataType: 'json'
    //             }),
    //             $.ajax({
    //                 url: `${BASE_API_URL}/api/users/${staffId}`,
    //                 type: 'GET',
    //                 dataType: 'json'
    //             })
    //         ]);

    //         let mergedData = receipts;

    //         if (providerResponse.success && staffResponse.success) {
    //             mergedData = receipts.map(receipt => ({
    //                 ...receipt,
    //                 provider_name: providerResponse.data.full_name,
    //                 staff_name: staffResponse.data.full_name
    //             }));
    //         }

    //         renderGoodsReceiptTable(mergedData);

    //     } catch (error) {
    //         console.error("Error getting provider/staff data:", error);
    //         // Render with default values if error occurs
    //         renderGoodsReceiptTable(receipts.map(receipt => ({
    //             ...receipt,
    //             provider_name: "N/A",
    //             staff_name: "N/A"
    //         })));
    //     } finally {
    //         hideLoading();
    //     }
    // }

    // // ======================== UTILITY FUNCTIONS ========================
    // function formatCurrencyVND(amount) {
    //     if (!amount) return '0 ₫';
    //     return new Intl.NumberFormat('vi-VN', {
    //         style: 'currency',
    //         currency: 'VND'
    //     }).format(amount);
    // }

    // function showLoading() {
    //     $("#loading-indicator").show();
    // }

    // function hideLoading() {
    //     $("#loading-indicator").hide();
    // }

    // function debounce(func, wait, immediate) {
    //     let timeout;
    //     return function () {
    //         const context = this, args = arguments;
    //         const later = function () {
    //             timeout = null;
    //             if (!immediate) func.apply(context, args);
    //         };
    //         const callNow = immediate && !timeout;
    //         clearTimeout(timeout);
    //         timeout = setTimeout(later, wait);
    //         if (callNow) func.apply(context, args);
    //     };
    // }

    // function showError(message) {
    //     Toastify({
    //         text: message,
    //         duration: 3000,
    //         close: true,
    //         gravity: "top",
    //         position: "right",
    //         style: {
    //             background: "linear-gradient(to right, #ff5f6d, #ffc371)",
    //         }
    //     }).showToast();
    // }

    // function showSuccess(message) {
    //     Toastify({
    //         text: message,
    //         duration: 3000,
    //         close: true,
    //         gravity: "top",
    //         position: "right",
    //         style: {
    //             background: "linear-gradient(to right, #00b09b, #96c93d)",
    //         }
    //     }).showToast();
    // }



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
                    <button class="btn btn-warning btn-update" data-id="${receipt.id}">
                        <i class="fas fa-edit"></i>
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