let providerListData = []; // Biến toàn cục lưu danh sách nhà cung cấp
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

    // ======================== BIẾN TOÀN CỤC VÀ CẤU HÌNH ========================
    const PROVIDER_CONFIG = {
        DEFAULT_ITEMS_PER_PAGE: 2,
        DEFAULT_EXTRA_PARAMS: ""
    };

    const pagination = new Pagination(PROVIDER_CONFIG.DEFAULT_ITEMS_PER_PAGE);
    let providerListData = [];

    // ======================== KHỞI TẠO ========================
    $(document).ready(function () {
        initializePage();
        bindFilterEvents();
    });

    function initializePage() {
        // Khởi tạo phân trang với callback
        pagination.init(function (searchValue, extraParams, page, perPage) {
            loadProviderData(searchValue, extraParams, page, perPage);
        });

        // Load dữ liệu ban đầu
        pagination.loadData();
    }

    // ======================== CÁC HÀM CHÍNH XỬ LÝ DỮ LIỆU ========================
    async function loadProviderData(searchValue = "", extraParams = "", page = 1, perPage = PROVIDER_CONFIG.DEFAULT_ITEMS_PER_PAGE) {
        try {
            showLoading();

            // Tính toán offset dựa trên trang hiện tại
            const offset = (page - 1) * perPage;

            // Xây dựng query params
            const params = new URLSearchParams({
                limit: perPage,
                offset: offset
            });

            // Thêm search value nếu có
            if (searchValue) {
                params.append('full_name', searchValue);
            }

            // Thêm các filter từ extraParams
            if (extraParams) {
                const extraParamsObj = new URLSearchParams(extraParams);
                extraParamsObj.forEach((value, key) => {
                    if (value) params.append(key, value);
                });
            }

            // Gọi API lấy dữ liệu
            const response = await $.ajax({
                url: `${BASE_API_URL}/api/providers?${params.toString()}`,
                type: 'GET',
                dataType: 'json'
            });

            if (!response.success) {
                throw new Error(response.message || "Failed to load provider data");
            }

            // Cập nhật dữ liệu và giao diện
            providerListData = response.data;
            renderProviderTable(providerListData);

            // Cập nhật thông tin phân trang từ response
            updatePaginationInfo(
                response.totalElements || 0,
                offset,
                perPage
            );

        } catch (error) {
            console.error("Lỗi khi tải dữ liệu nhà cung cấp:", error);
            showError("Không thể tải dữ liệu nhà cung cấp");
        } finally {
            hideLoading();
        }
    }

    // ======================== CÁC HÀM HIỂN THỊ ========================
    function renderProviderTable(providers) {
        const content = document.getElementById("provider-data-table");
        content.innerHTML = "";

        if (!providers || providers.length === 0) {
            content.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">Không có dữ liệu</td>
            </tr>`;
            return;
        }

        providers.forEach((provider, index) => {
            content.innerHTML += `
        <tr id="provider-${provider.id}">
            <td>${index + 1}</td>
            <td>${provider.id}</td>
            <td>${provider.full_name}</td>
            <td>${provider.email}</td>
            <td>${provider.phone_number}</td>
            <td>${formatDate(provider.created_at)}</td>
            <td>${formatDate(provider.updated_at)}</td>
            <td>
                <button class="btn btn-warning btn-update" data-id="${provider.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="${provider.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        });
    }

    function updatePaginationInfo(totalItems, startIndex, perPage) {
        const displayStart = totalItems > 0 ? startIndex + 1 : 0;
        const displayEnd = Math.min(startIndex + perPage, totalItems);
        const currentPage = Math.floor(startIndex / perPage) + 1;
        // Cập nhật thông tin hiển thị
        pagination.updateRecordInfo(displayStart, displayEnd, totalItems);
        // Cập nhật thông tin phân trang
        pagination.totalItems = totalItems;
        pagination.currentPage = currentPage;
        pagination.itemsPerPage = perPage;

        // Render lại phân trang
        pagination.render(totalItems);
    }

    // ======================== CÁC HÀM LỌC VÀ TÌM KIẾM ========================
    function bindFilterEvents() {
        // Tìm kiếm nhanh với debounce
        $("#keyword").on("input", debounce(function () {
            const searchValue = $(this).val().trim();
            pagination.setSearchValue(searchValue);
            pagination.currentPage = 1;
            pagination.loadData();
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

    function applyAdvancedFilters() {
        // Lấy dữ liệu từ form
        const formData = $("#filterForm").serialize();
        const filterParams = new URLSearchParams(formData);
        const searchValue = $("#keyword").val().trim();

        // Reset về trang đầu tiên khi áp dụng bộ lọc mới
        pagination.currentPage = 1;

        // Cập nhật tham số tìm kiếm
        pagination.setSearchValue(searchValue);

        // Kết hợp tham số lọc với tham số hiện có (nếu cần)
        const currentParams = pagination.extraParams ? new URLSearchParams(pagination.extraParams) : new URLSearchParams();

        // Xóa các params cũ trước khi thêm mới
        currentParams.forEach((value, key) => {
            currentParams.delete(key);
        });

        // Thêm các params mới từ form
        filterParams.forEach((value, key) => {
            if (value && value.toString().trim() !== "") {
                currentParams.set(key, value.toString().trim());
            }
        });

        // Cập nhật extraParams với các tham số lọc mới
        pagination.extraParams = currentParams.toString();

        // Load lại dữ liệu với các tham số mới
        pagination.loadData();
    }

    // ======================== CÁC HÀM TIỆN ÍCH ========================
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

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
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

















    // //====================================== MODAL UPDATE NHÀ CUNG CẤP KHI BẤM UPDATE ====================================

    $(document).on('click', '.btn-update', function () {
        let providerId = this.getAttribute("data-id");
        console.log("ID nhà cung cấp cần xem:", providerId);

        let apiProviderDetail = `${BASE_API_URL}/api/providers/${providerId}`;
        console.log("API lấy chi tiết nhà cung cấp:", apiProviderDetail);

        $.ajax({
            url: apiProviderDetail,
            method: "GET",
            success: function (response) {
                if (response.success) {
                    console.log("Chi tiết nhà cung cấp:", response.data);
                    renderProviderModal(response.data);
                    //loadProviderList();
                    // Hiển thị modal
                    $("#updateProviderModal").modal("show");
                } else {
                    console.error("Lỗi lấy chi tiết nhà cung cấp:", response.message);
                }
            },
            error: function (error) {
                console.error("Lỗi lấy chi tiết nhà cung cấp:", error);
            },
        });
    });

    function renderProviderModal(providerData) {
        let modal = document.querySelector("#updateProviderForm");

        modal.querySelector("#provider_id").value = providerData.id || "";
        modal.querySelector("#provider_name").value = providerData.full_name || "";
        modal.querySelector("#provider_email").value = providerData.email || "";
        modal.querySelector("#provider_phone").value = providerData.phone_number || "";
        modal.querySelector("#provider_created_at").value = providerData.created_at || "";

        // Xóa lỗi khi người dùng nhập lại
        clearError(modal.querySelector("#provider_name"));
        clearError(modal.querySelector("#provider_email"));
        clearError(modal.querySelector("#provider_phone"));
    }

    // //====================================== BẤM NÚT SAVE ĐỂ CẬP NHẬT NHÀ CUNG CẤP ====================================
    $(document).on('click', '#saveProvider', async function () {
        let modal = document.querySelector("#updateProviderForm");
        let provider = getProviderDataFromForm(modal);
        console.log("Dữ liệu nhà cung cấp cần cập nhật:", provider);
        // Kiểm tra dữ liệu
        if (! await validateProvider(provider, true)) {
            return;
        }
        // Gọi API cập nhật
        callAPIUpdateProvider(provider);

    });

    // Hàm lấy dữ liệu từ form 
    function getProviderDataFromForm(modal) {

        let provider = {
            id: modal.querySelector("#provider_id").value.trim(),
            full_name: modal.querySelector("#provider_name").value.trim(),
            email: modal.querySelector("#provider_email").value.trim(),
            phone_number: modal.querySelector("#provider_phone").value.trim(),
            created_at: modal.querySelector("#provider_created_at").value.trim(),
            updated_at: getCurrentDateTime(), // Lấy ngày giờ hiện tại
        };

        return provider;
    }

    // Hàm lấy ngày giờ hiện tại theo định dạng "YYYY-MM-DD HH:mm:ss"
    function getCurrentDateTime() {
        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
        let day = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }


    // Hàm call api cập nhật nhà cung cấp
    function callAPIUpdateProvider(providerData) {
        let apiProviderUpdate = `${BASE_API_URL}/api/providers/${providerData.id}`;
        console.log("API cập nhật nhà cung cấp:", apiProviderUpdate);
        $.ajax({
            url: apiProviderUpdate,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(providerData),
            success: function (response) {
                if (response.success) {
                    console.log("Cập nhật nhà cung cấp thành công!", response.data);
                    pagination.loadData();

                    // Đóng modal
                    $("#updateProviderModal").modal("hide");
                    showSuccess("Cập nhật nhà cung cấp thành công!", "success");
                } else {
                    console.error("Lỗi cập nhật nhà cung cấp:", response.message);
                    showError("Lỗi cập nhật nhà cung cấp!", "error");
                }
            },
            error: function (error) {
                console.error("Lỗi cập nhật nhà cung cấp:", error);
                console.log("Chi tiết lỗi:", error.responseText);
                showError("Lỗi cập nhật nhà cung cấp!", "error");
            }
        });
    }
    //============================================== XÓA NHÀ CUNG CẤP ===============================================

    $(document).on('click', '.btn-delete', function () {
        let providerId = this.getAttribute("data-id");
        console.log("ID nhà cung cấp cần xóa:", providerId);

        if (confirm("Bạn có chắc chắn muốn xóa nhà cung cấp này?")) {
            callAPIDeleteProvider(providerId);
        }
    });

    function callAPIDeleteProvider(providerId) {
        let apiProviderDelete = `${BASE_API_URL}/api/providers/${providerId}`;
        console.log("API xóa nhà cung cấp:", apiProviderDelete);
        $.ajax({
            url: apiProviderDelete,
            method: "DELETE",
            success: function (response) {
                if (response.success) {
                    console.log("Xóa nhà cung cấp thành công!", response.data);
                    // Load dữ liệu ban đầu
                    pagination.loadData();
                    showSuccess("Xóa nhà cung cấp thành công!", "success");
                } else {
                    console.error("Lỗi xóa nhà cung cấp:", response.message);
                    showError("Lỗi xóa nhà cung cấp!", "error");
                }
            },
            error: function (error) {
                console.error("Lỗi xóa nhà cung cấp:", error);
                console.log("Chi tiết lỗi:", error.responseText);
                showError("Lỗi xóa nhà cung cấp!", "error");
            }
        });
    }

});
