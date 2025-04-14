document.addEventListener("DOMContentLoaded", function () {

    // ========================== THÊM SẢN PHẨM VÀ BIẾN THỂ SẢN PHẨM ==========================
    // Lấy dữ liệu từ modal khi người dùng nhấn "Save Product"
    $("#btn-create-save").click(function () {
        let modal = document.querySelector("#productForm");
        // Lấy thông tin sản phẩm từ form
        let productData = getNewProductInfo();
        console.log("Product Data:", productData);
        // Validate dữ liệu
        // if (!validateProduct(productData)) {
        //     return;
        // }
        // Kiểm tra và thêm ảnh mới nếu có
        // Lấy thông tin biến thể từ form
        let variationData = getNewVariationInfo();
        console.log("Variation Data:", variationData);
        let imageFile = modal.querySelector("#image_upload").files[0];
        if (imageFile) {
            const reader = new FileReader();
            reader.onloadend = function () {
                let base64Image = reader.result.split(',')[1]; // Lấy phần base64 của ảnh
                variationData.image_base64 = base64Image; // Thêm ảnh base64 vào dữ liệu JSON
                console.log("Base64 Image:", base64Image);
                console.log("Product Info:", productData);
                console.log("variationData Info:", variationData);
                // Gọi hàm lưu sản phẩm và biến thể
                saveProduct(productData, variationData);
            };
            reader.readAsDataURL(imageFile);
        } else {
            saveProduct(productData, variationData);
        }

    });

    function getNewProductInfo() {
        let modal = document.querySelector("#productForm");
        return {
            name: modal.querySelector('#product_name').value,
            brand_id: modal.querySelector('#brand_id').value,
            model: modal.querySelector('#model').value,
            category_id: modal.querySelector('#category_id').value,
            description: modal.querySelector('#description').value,
            //image_name: modal.querySelector('#image_name').value,
            stop_selling: modal.querySelector('#stop_selling').checked
        };

        // return {
        //     name: "Smartwatch X Pro",
        //     brand_id: 1,
        //     model: "XPRO-2025",
        //     category_id: 3,
        //     description: "Đồng hồ thông minh với nhiều tính năng hiện đại",
        //     image_name: modal.querySelector('#image_name').value,
        //     stop_selling: false,
        // };
    }

    // Hàm lấy thông tin biến thể từ form
    function getNewVariationInfo() {
        let modal = document.querySelector("#productForm");
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

        console.log("Formatted Date:", formattedDate);

        return {
            watch_size_mm: modal.querySelector('#watch_size_mm').value,
            watch_color: modal.querySelector('#watch_color').value,
            display_type: modal.querySelector('#display_type').value,
            display_size_mm: modal.querySelector('#display_size_mm').value,
            resolution_w_px: modal.querySelector('#resolution_w_px').value,
            resolution_h_px: modal.querySelector('#resolution_h_px').value,
            ram_bytes: modal.querySelector('#ram_bytes').value,
            rom_bytes: modal.querySelector('#rom_bytes').value,
            connectivity: modal.querySelector('#connectivity').value,
            sensor: modal.querySelector('#sensors').value,
            case_material: modal.querySelector('#case_material').value,
            band_material: modal.querySelector('#band_material').value,
            band_size_mm: modal.querySelector('#band_size_mm').value,
            band_color: modal.querySelector('#band_color').value,
            battery_life_mah: modal.querySelector('#battery_life_mah').value,
            water_resistance_value: modal.querySelector('#water_resistance_value').value,
            water_resistance_unit: modal.querySelector('#water_resistance_unit').value,
            weight_milligrams: modal.querySelector('#weight_miligam').value,
            base_price_cents: modal.querySelector('#base_price_cents').value,
            price_cents: modal.querySelector('#price_cents').value,
            os_id: parseInt(modal.querySelector('#os_select').value) || 1,
            release_date: formattedDate, // Sửa từ relase_date
            stop_selling: modal.querySelector('#stop_selling').checked
            // image_name: modal.querySelector('#image_name').value,
        };
        // return {
        //     //image_name: modal.querySelector('#image_name').value,
        //     os_id: modal.querySelector('#os_select').value,
        //     watch_size_mm: 44,
        //     watch_color: "Black",
        //     display_type: "AMOLED",
        //     display_size_mm: 1.78,
        //     resolution_w_px: 368,
        //     resolution_h_px: 448,
        //     ram_bytes: 1024, // 1GB
        //     rom_bytes: 8192, // 8GB
        //     connectivity: "Bluetooth 5.2, Wi-Fi",
        //     sensor: "Heart Rate, SpO2, GPS",
        //     case_material: "Titanium",
        //     band_material: "Silicone",
        //     band_size_mm: 22,
        //     band_color: "Black",
        //     battery_life_mah: 450,
        //     water_resistance_value: 50,
        //     water_resistance_unit: "meters",
        //     weight_milligrams: 35000, // 35g
        //     base_price_cents: 19900, // $199.00
        //     price_cents: 24900, // $249.00
        //     release_date: formattedDate, // Thêm ngày hiện tại vào đây
        //     stop_selling: false,
        // };
    }

    // Hàm lưu sản phẩm và biến thể
    function saveProduct(productData, variationData) {
        let productId; // Lưu ID sản phẩm

        console.log("Product Data:", productData);
        console.log("Variation Data:", variationData);

        // Thêm sản phẩm -> Lưu ID -> Thêm biến thể -> Xử lý lỗi nếu có
        addProduct(productData)
            .then((product) => {
                if (!product) throw new Error("Không nhận được ID sản phẩm");
                productId = product.id; // Lưu ID sản phẩm
                console.log("ID sản phẩm mới:", productId);
                console.log("Product", product);
                // Kiểm tra ID trước khi thêm biến thể
                variationData.product_id = productId;
                //variationData.image_name = product.image_name; // Sử dụng ảnh sản phẩm cho biến thể
                return addVariation(variationData);
            })
            .then(() => {
                console.log('Sản phẩm và biến thể đã thêm thành công!');
                alert('Thêm sản phẩm và biến thể thành công!');
            })
            .catch((error) => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra, đang xử lý...');

                // Nếu lỗi xảy ra khi thêm biến thể, xóa sản phẩm đã thêm
                if (productId) {
                    deleteProduct(productId)
                        .then(() => {
                            alert('Có lỗi xảy ra, sản phẩm đã bị xóa. Vui lòng thử lại!');
                        })
                        .catch((deleteError) => {
                            console.error("Lỗi khi xóa sản phẩm:", deleteError);
                        });
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            });
    }


    function addProduct(productData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${BASE_API_URL}/api/products`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(productData),
                success: function (response) {
                    if (response.success) {
                        console.log("Sản phẩm đã thêm:", response.data);
                        resolve(response.data); // Trả về sản phẩm
                    } else {
                        reject(new Error(`Lỗi thêm sản phẩm: ${response.message || 'Unknown error'}`));
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Chi tiết lỗi:", xhr.responseText);
                    reject(new Error(`Lỗi khi thêm sản phẩm: ${xhr.status} - ${xhr.responseText}`));
                }
            });
        });
    }

    function addVariation(variationData) {
        if (!variationData.product_id) {
            return Promise.reject(new Error("Thiếu product_id, không thể thêm biến thể!"));
        }

        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${BASE_API_URL}/api/products/variations`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(variationData),
                success: function (response) {
                    if (response.success) {
                        console.log("Biến thể đã thêm:", response.data);
                        resolve(response.data);
                    } else {
                        reject(new Error(`Lỗi thêm biến thể: ${response.message || 'Unknown error'}`));
                    }
                },
                error: function (xhr) {
                    console.error("Lỗi khi thêm biến thể:", xhr.responseText);
                    reject(new Error(`Lỗi khi thêm biến thể: ${xhr.status} - ${xhr.responseText}`));
                }
            });
        });
    }

    // Hàm xóa sản phẩm nếu có lỗi
    function deleteProduct(productId) {
        return $.ajax({
            url: `${BASE_API_URL}/api/products/${productId}`,
            method: 'DELETE',
        }).then((response) => {
            if (response.success) {
                console.log('Sản phẩm đã được xóa do lỗi.');
            } else {
                console.error(`Không thể xóa sản phẩm: ${response.message || 'Unknown error'}`);
            }
        }).catch((deleteError) => {
            console.error('Lỗi khi xóa sản phẩm:', deleteError);
        });
    }
});