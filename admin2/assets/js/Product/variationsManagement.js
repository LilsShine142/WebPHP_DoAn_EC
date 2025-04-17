document.addEventListener("DOMContentLoaded", function () {
    $(document).ready(function () {
        // Khởi tạo
        loadOperatingSystems();

        // Xử lý ảnh chính
        document.getElementById('main_image').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('main_image_preview');
            const removeBtn = document.getElementById('remove_main_image');
            const fileNameDisplay = document.getElementById('main_image_name');

            if (file) {
                // Hiển thị tên file
                fileNameDisplay.textContent = file.name;

                // Hiển thị preview
                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.classList.remove('d-none');
                    removeBtn.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        // Nút xóa ảnh chính
        document.getElementById('remove_main_image').addEventListener('click', function () {
            const input = document.getElementById('main_image');
            const preview = document.getElementById('main_image_preview');
            const removeBtn = document.getElementById('remove_main_image');
            const fileNameDisplay = document.getElementById('main_image_name');

            input.value = '';
            preview.src = '#';
            preview.classList.add('d-none');
            removeBtn.classList.add('d-none');
            fileNameDisplay.textContent = 'No file chosen';
        });

        // Mảng lưu trữ các file ảnh đã chọn
        let detailImages = [];

        // Xử lý khi chọn ảnh phụ
        document.getElementById('detail_images').addEventListener('change', function (e) {
            const files = Array.from(e.target.files);

            // Thêm ảnh mới vào mảng (không thay thế)
            detailImages = [...detailImages, ...files];
            updateDetailImagesPreview();

            // Reset input để có thể chọn lại
            this.value = '';
        });

        // Xóa ảnh phụ
        document.getElementById('detail_images_preview').addEventListener('click', function (e) {
            if (e.target.closest('.remove-detail-image')) {
                const index = e.target.closest('.remove-detail-image').dataset.index;
                detailImages.splice(index, 1);
                updateDetailImagesPreview();
            }
        });

        // Cập nhật preview ảnh phụ
        function updateDetailImagesPreview() {
            const previewContainer = document.getElementById('detail_images_preview');
            const countDisplay = document.getElementById('detail_images_count');

            // Cập nhật số lượng ảnh
            countDisplay.textContent = `${detailImages.length} ${detailImages.length === 1 ? 'file' : 'files'} selected`;

            // Xóa preview cũ
            previewContainer.innerHTML = '';

            // Hiển thị preview mới
            detailImages.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-6 col-md-4 col-lg-3';
                    colDiv.innerHTML = `
                <div class="thumbnail-wrapper position-relative mb-2">
                    <img src="${event.target.result}" class="img-thumbnail w-100" 
                         style="height: 120px; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-detail-image" 
                            data-index="${index}" style="padding: 0.15rem 0.3rem;">
                        <i class="bi bi-x"></i>
                    </button>
                    <div class="file-name text-truncate small mt-1">${file.name}</div>
                </div>
            `;
                    previewContainer.appendChild(colDiv);
                };
                reader.readAsDataURL(file);
            });

            // Cập nhật lại DataTransfer để form submit
            const dataTransfer = new DataTransfer();
            detailImages.forEach(file => dataTransfer.items.add(file));
            document.getElementById('detail_images').files = dataTransfer.files;
        }

        // Load danh sách hệ điều hành
        function loadOperatingSystems() {
            $.ajax({
                url: `${BASE_API_URL}/api/products/os`,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        const osData = response.data;

                        // Dropdown trong form sản phẩm
                        const $formSelect = $('#os_select');
                        $formSelect.empty().append('<option value="" selected disabled>Select Operating System</option>');
                        osData.forEach(os => {
                            $formSelect.append(`<option value="${os.id}">${os.name}</option>`);
                        });
                    }
                },
                error: function (xhr) {
                    console.error('Error loading OS:', xhr.responseText);
                    showToast('Error loading operating systems', 'error');
                }
            });
        }

        // Xử lý khi submit form
        $('#productVariationForm').submit(function (e) {
            e.preventDefault();
            console.log("Đang gửi dữ liệu biến thể...");

            // Validate form trước khi submit
            // if (!validateVariationForm()) {
            //     return;
            // }

            // Lấy productId từ URL
            // const productId = getProductIdFromURL();
            const productId = 1;
            if (!productId) {
                showToast("Không tìm thấy ID sản phẩm trong URL", 'error');
                return;
            }

            // Lấy dữ liệu từ form
            const variationData = getVariationFormData();
            variationData.product_id = productId;
            console.log("Dữ liệu biến thể:", variationData);
            // Xử lý ảnh chính nếu có
            const mainImageFile = $('#main_image')[0].files[0];
            if (mainImageFile) {
                processMainImage(mainImageFile, variationData)
                    .then(dataWithImage => saveVariation(dataWithImage))
                    .catch(error => {
                        console.error("Lỗi xử lý ảnh:", error);
                        showToast("Lỗi khi xử lý ảnh sản phẩm", 'error');
                    });
            } else {
                // Nếu không có ảnh, gửi luôn dữ liệu
                saveVariation(variationData);
            }
        });

        // Hàm validate form
        function validateVariationForm() {
            let isValid = true;

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Validate required fields
            const requiredFields = [
                'os_select', 'watch_size_mm', 'watch_color',
                'display_type', 'display_size_mm', 'price_cents'
            ];

            requiredFields.forEach(fieldId => {
                const value = $(`#${fieldId}`).val();
                if (!value) {
                    $(`#${fieldId}`).addClass('is-invalid');
                    $(`#${fieldId}`).after(`<div class="invalid-feedback">Vui lòng điền thông tin này</div>`);
                    isValid = false;
                }
            });

            // Validate price must be number and > 0
            const price = $('#price_cents').val();
            if (isNaN(price) || Number(price) <= 0) {
                $('#price_cents').addClass('is-invalid');
                $('#price_cents').after(`<div class="invalid-feedback">Giá phải là số lớn hơn 0</div>`);
                isValid = false;
            }

            return isValid;
        }

        // Hàm xử lý ảnh chính
        function processMainImage(file, variationData) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = function (event) {
                    // Chuyển ảnh sang base64 để gửi lên server
                    const base64String = event.target.result.split(',')[1];

                    // Thêm thông tin ảnh vào dữ liệu biến thể
                    variationData.image_base64 = base64String;
                    variationData.image_name = file.name;
                    variationData.image_type = file.type;

                    resolve(variationData);
                };

                reader.onerror = function (error) {
                    reject(error);
                };

                reader.readAsDataURL(file);
            });
        }

        // Hàm lấy dữ liệu từ form
        function getVariationFormData() {
            // Lấy ngày và giờ hiện tại
            const currentDate = new Date();

            // Định dạng ngày giờ thành YYYY-MM-DD HH:MI:SS
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
            const day = String(currentDate.getDate()).padStart(2, '0');
            const hours = String(currentDate.getHours()).padStart(2, '0');
            const minutes = String(currentDate.getMinutes()).padStart(2, '0');
            const seconds = String(currentDate.getSeconds()).padStart(2, '0');

            // Chuỗi ngày giờ chuẩn theo định dạng API yêu cầu
            const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            return {
                os_id: $('#os_select').val(),
                watch_size_mm: $('#watch_size_mm').val(),
                watch_color: $('#watch_color').val(),
                display_type: $('#display_type').val(),
                display_size_mm: $('#display_size_mm').val(),
                resolution_w_px: $('#resolution_w_px').val(),
                resolution_h_px: $('#resolution_h_px').val(),
                ram_bytes: $('#ram_bytes').val(),
                rom_bytes: $('#rom_bytes').val(),
                release_date: formattedDate, // Sửa từ relase_date
                connectivity: $('#connectivity').val(),
                sensor: $('#sensors').val(),
                case_material: $('#case_material').val(),
                band_material: $('#band_material').val(),
                band_size_mm: $('#band_size_mm').val(),
                band_color: $('#band_color').val(),
                battery_life_mah: $('#battery_life_mah').val(),
                water_resistance_value: $('#water_resistance_value').val(),
                water_resistance_unit: $('#water_resistance_unit').val(),
                weight_milligrams: $('#weight_miligam').val(),
                base_price_cents: $('#base_price_cents').val() || 0,
                price_cents: $('#price_cents').val(),
                stop_selling: $('#stop_selling').checked
            };
        }

        // Hàm lưu biến thể lên server
        function saveVariation(variationData) {
            $.ajax({
                url: `${BASE_API_URL}/api/products/variations`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(variationData),
                beforeSend: function () {
                    // Hiển thị loading
                    $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...');
                },
                success: function (response) {
                    if (response.success) {
                        showToast("Thêm biến thể thành công!", 'success');
                        resetVariationForm();

                        // Nếu cần chuyển hướng hoặc làm mới trang
                        // window.location.href = `/product-detail.html?productId=${variationData.product_id}`;
                    } else {
                        showToast("Lỗi khi thêm biến thể: " + (response.message || 'Vui lòng thử lại'), 'error');
                    }
                },
                error: function (xhr) {
                    console.error("Lỗi khi thêm biến thể:", xhr.responseText);
                    let errorMsg = "Có lỗi xảy ra khi thêm biến thể";

                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMsg = errorResponse.message;
                        }
                    } catch (e) { }

                    showToast(errorMsg, 'error');
                },
                complete: function () {
                    $('#submitBtn').prop('disabled', false).html('Thêm biến thể');
                }
            });
        }

        // Hàm reset form sau khi submit thành công
        function resetVariationForm() {
            $('#productVariationForm')[0].reset();

            // Xóa preview ảnh
            $('#main_image').val('');
            $('#main_image_preview').attr('src', '#').addClass('d-none');
            $('#remove_main_image').addClass('d-none');
            $('#main_image_name').text('No file chosen');

            // Focus vào field đầu tiên
            $('#os_select').focus();
        }

        // Hàm lấy productId từ URL
        function getProductIdFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get("productId");
        }

        // Hàm hiển thị thông báo
        function showToast(message, type = 'success') {
            const backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: backgroundColor,
            }).showToast();
        }
    });
});