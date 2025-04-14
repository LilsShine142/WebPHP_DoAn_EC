// FILE NÀY DÀNH CHO CÁC XỬ LÝ CỦA VARIATIONS CHƯA CÓ NHẬP HÀNG (CHƯA CÓ TRONG PRODUCT INSTANCE)
document.addEventListener("DOMContentLoaded", function () {
    // Lấy id từ URL
    function getParameterByName(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
    // =================================LẤY THÔNG TIN  BIẾN THỂ SẢN PHẨM (variations) =================================
    // CALL API LẤY THÔNG TIN PHIÊN BẢN SẢN PHẨM

    function fetchAPIProductsVariations(productId) {
        let productsVariationsAPI = `${BASE_API_URL}/api/products/variations?product_id=${productId}`

        let productInstanceAPI = `${BASE_API_URL}/api/products/instances`;

        console.log("API URL Variations:", productsVariationsAPI);
        console.log("API URL Instances:", productInstanceAPI);

        // Gọi API song song
        Promise.all([
            $.ajax({ url: productsVariationsAPI, type: "GET", dataType: "json" }),
            $.ajax({ url: productInstanceAPI, type: "GET", dataType: "json" })
        ])
            .then(([productsVariationsResponse, productsInstanceResponse]) => {
                if (productsVariationsResponse.success && productsInstanceResponse.success) {
                    let variations = productsVariationsResponse.data;
                    let instances = productsInstanceResponse.data;
                    console.log("Variations:", variations);
                    console.log("Instances:", instances);
                    // Gộp dữ liệu vào sản phẩm
                    let mergedProducts = variations.map(variation => ({
                        ...variation,
                        instances: instances.filter(i => i.product_variation_id === variation.id) // Lọc instances theo product_variation_id
                    }));

                    console.log("Merged Products:", mergedProducts);
                    loadProductVariationsDataToTable(mergedProducts);
                } else {
                    console.error("Lỗi khi tải dữ liệu sản phẩm hoặc biến thể");
                }
            })
            .catch(error => {
                console.error("Lỗi API:", error);
            });
    }



    // LOAD DỮ LIỆU PHIÊN BẢN SẢN PHẨM LÊN FORM
    function loadProductVariationsDataToTable(productVariationData) {
        const productVariationTable = document.getElementById("product-variations-table");
        productVariationTable.innerHTML = "";

        // Xử lý trường hợp không có dữ liệu
        if (!productVariationData || productVariationData.length === 0) {
            productVariationTable.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-3">No data available</td>
            </tr>`;
            return;
        }

        let index = 0;
        productVariationData.forEach((variation) => {
            const imageUrl = variation.image_name
                ? `${BASE_API_URL}/backend/uploads/products/${variation.image_name}`
                : "default-image.jpg";
            console.log("Image URL:", imageUrl);
            variation.instances.forEach((instance) => {
                index += 1;
                productVariationTable.innerHTML += `
                <tr class="align-middle">
                    <td class="text-center">${index}</td>
                    <td class="text-center">${variation.product_id}</td>
                    <td class="text-center">${variation.id}</td>
                    <td class="text-center">
                        <img src="${imageUrl}" 
                             width="60" 
                             class="img-thumbnail"
                             onerror="this.onerror=null; this.src='default-image.jpg';"
                             alt="Variation ${variation.id}">
                    </td>
                    <td class="text-center">${instance.sku}</td>
                    <td class="text-center">${variation.watch_size_mm || '-'}</td>
                    <td class="text-center">${variation.display_size_mm || '-'}</td>
                    <td class="text-center">${variation.price_cents || '0'}</td>
                    <td class="text-center">${variation.stock_quantity || '0'}</td>
                    <td class="text-center">${variation.stop_selling ? 'Yes' : 'No'}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-info btn-sm py-1 px-2 btn-view" 
                                    data-id="${variation.id}" 
                                    title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm py-1 px-2 btn-update" 
                                    data-id="${variation.id}" 
                                    title="Edit">
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
            });
        });
    }

    $(document).ready(function () {
        let productId = getParameterByName('product_id'); // Lấy ID từ URL
        console.log("Product ID:", productId);

        if (productId) {
            fetchAPIProductsVariations(productId);
        }
        loadProductVariationsDataToTable(null); // Gọi API để load danh sách sản phẩm
    });

    // ================================= CHI TIẾT PHIÊN BẢN SẢN PHẨM =================================
    // CALL API LẤY CHI TIẾT PHIÊN BẢN SẢN PHẨM
    async function fetchAPIProductOSAndVariations(productVariationId) {
        let APIProductVariationDetailurl = `${BASE_API_URL}/api/products/variations/${productVariationId}`;
        let APIOSProducturl = `${BASE_API_URL}/api/products/os`;

        try {
            let [variationResponse, osResponse] = await Promise.all([
                $.ajax({ url: APIProductVariationDetailurl, type: "GET", contentType: "application/json", dataType: "json" }),
                $.ajax({ url: APIOSProducturl, type: "GET", contentType: "application/json", dataType: "json" })
            ]);

            console.log("API Response Product Variation Detail:", variationResponse);
            console.log("API Response OS List:", osResponse);

            if (variationResponse.success && variationResponse.data && osResponse.success && osResponse.data) {
                let variation = { ...variationResponse.data }; // Sao chép dữ liệu từ API
                let osList = osResponse.data || [];

                // Tạo biến os_name và gán giá trị từ danh sách OS
                let foundOS = osList.find(os => os.id === variation.os_id);
                variation.os_name = foundOS ? foundOS.name : "Unknown OS"; // Nếu không tìm thấy, gán "Unknown OS"

                console.log("Product Detail (with OS):", variation);
                return variation; // Trả về object đã cập nhật
            } else {
                throw new Error("Dữ liệu trả về không hợp lệ.");
            }
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }




    // ========================== HIỆN MODAL CHI TIẾT SẢN PHẨM ========================== 
    $(document).on('click', '.btn-view', async function () {
        let productVariationId = $(this).data('id');
        console.log("Product Variation ID:", productVariationId);

        try {
            let productVariationDetail = await fetchAPIProductOSAndVariations(productVariationId);
            console.log("Product Detail (with OS):", productVariationDetail);
            if (productVariationDetail) {
                showDataProductVariation(productVariationDetail); // Hiển thị chi tiết phiên bản sản phẩm
            } else {
                alert("Không tìm thấy thông tin sản phẩm!");
            }
        } catch (error) {
            alert("Failed to fetch product details!");
        }
    });



    function showDataProductVariation(variation) {
        console.log("Variation:", variation);
        if (!variation) {
            console.error("Invalid product variation data.");
            return;
        }

        // View modal data
        // Thông tin hệ điều hành
        $("#os_name").text(variation.os_name ?? "No data available");

        // Thông số kỹ thuật
        $("#watch_size_mm").text(variation.watch_size_mm ? `${variation.watch_size_mm}mm` : "No data available");
        $("#watch_color").text(variation.watch_color ?? "No data available");
        $("#display_type").text(variation.display_type ?? "No data available");
        $("#display_size_mm").text(variation.display_size_mm ? `${variation.display_size_mm}mm` : "No data available");
        $("#resolution_w_px").text(variation.resolution_w_px && variation.resolution_h_px ? `${variation.resolution_w_px} × ${variation.resolution_h_px}px` : "No data available");
        $("#ram_bytes").text(variation.ram_bytes ? `${(variation.ram_bytes / 1024).toFixed(2)} GB` : "No data available");
        $("#rom_bytes").text(variation.rom_bytes ? `${(variation.rom_bytes / 1024).toFixed(2)} GB` : "No data available");
        $("#connectivity").text(variation.connectivity ?? "No data available");
        $("#sensors").text(variation.sensor ?? "No data available");

        // Vật liệu & Kết cấu
        $("#case_material").text(variation.case_material ?? "No data available");
        $("#band_material").text(variation.band_material ?? "No data available");
        $("#band_size_mm").text(variation.band_size_mm ? `${variation.band_size_mm}mm` : "No data available");
        $("#band_color").text(variation.band_color ?? "No data available");

        // Kích thước & Khả năng chống nước
        $("#battery_life_mah").text(variation.battery_life_mah ? `${variation.battery_life_mah}mAh` : "No data available");
        $("#water_resistance_value").text(variation.water_resistance_value ?? "No data available");
        $("#water_resistance_unit").text(variation.water_resistance_unit ?? "No data available");
        $("#weight_miligam").text(variation.weight_milligrams ? `${(variation.weight_milligrams / 1_000).toFixed(2)}g` : "No data available");

        // Giá & Thời gian ra mắt
        $("#stock_quantity").text(variation.stock_quantity ?? "No data available");
        $("#base_price_cents").text(variation.base_price_cents ? `${(variation.base_price_cents / 1000).toFixed(3)} VND` : "Không có dữ liệu");
        $("#price_cents").text(variation.price_cents ? `${(variation.price_cents / 1000).toFixed(3)} VND` : "Không có dữ liệu");
        $("#release_date").text(variation.release_date ?? "No data available");
        $("#stop_selling").text(variation.stop_selling ?? "No data available");

        // Show modal
        $("#modalView").modal("show");
    }

    // ========================== HIỆN MODAL UPDATE SẢN PHẨM ==========================
    $(document).on('click', '.btn-update', async function () {
        let productVariationId = $(this).data('id');
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
                url: `${BASE_API_URL}/api/products/variations/${updatedProductVariation.id}`, // URL API backend
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