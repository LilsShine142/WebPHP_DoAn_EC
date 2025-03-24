document.addEventListener("DOMContentLoaded", function () {

    // =================================LẤY THÔNG TIN SẢN PHẨM =================================
    // CALL API LẤY THÔNG TIN SẢN PHẨM

    // function fetchAPIProductsAndVariations(productId) {
    //     let productsAPI = productId ? `${BASE_API_URL}/api/products/${productId}` : `${BASE_API_URL}/api/products`;
    //     let variationsAPI = productId ? `${BASE_API_URL}/api/products/variations?product_id=${productId}` : `${BASE_API_URL}/api/products/variations`;

    //     // Gọi API song song bằng Promise.all
    //     Promise.all([
    //         $.ajax({ url: productsAPI, type: "GET", dataType: "json" }),
    //         $.ajax({ url: variationsAPI, type: "GET", dataType: "json" })
    //     ])
    //         .then(([productsResponse, variationsResponse]) => {
    //             if (productsResponse.success && variationsResponse.success) {
    //                 let products = productsResponse.data;
    //                 let variations = variationsResponse.data;

    //                 // Gộp variations vào products dựa trên product_id
    //                 let mergedProducts = products.map(product => {
    //                     return {
    //                         ...product,
    //                         variations: variations.filter(v => v.product_id === product.id) // Lọc variations theo product_id
    //                     };
    //                 });

    //                 console.log("Merged Products:", mergedProducts);
    //                 loadProductDataToTable(mergedProducts);
    //             } else {
    //                 console.error("Lỗi khi tải dữ liệu sản phẩm hoặc biến thể");
    //             }
    //         })
    //         .catch(error => {
    //             console.error("Lỗi API:", error);
    //         });
    // }

    function fetchAPIProducts(productId) {
        let productsAPI = productId ? `${BASE_API_URL}/api/products/${productId}` : `${BASE_API_URL}/api/products`;
        let brandsAPI = `${BASE_API_URL}/api/products/brands`;
        let categoriesAPI = `${BASE_API_URL}/api/products/categories`;

        // Gọi API song song để lấy sản phẩm, biến thể, thương hiệu, danh mục
        Promise.all([
            $.ajax({ url: productsAPI, type: "GET", dataType: "json" }),
            $.ajax({ url: brandsAPI, type: "GET", dataType: "json" }),
            $.ajax({ url: categoriesAPI, type: "GET", dataType: "json" })
        ])
            .then(([productsResponse, brandsResponse, categoriesResponse]) => {
                if (productsResponse.success && brandsResponse.success && categoriesResponse.success) {
                    let products = productsResponse.data;
                    let brands = brandsResponse.data;
                    let categories = categoriesResponse.data;

                    // Tạo map để tra cứu nhanh brand và category theo id
                    let brandMap = Object.fromEntries(brands.map(b => [b.id, b.name]));
                    let categoryMap = Object.fromEntries(categories.map(c => [c.id, c.name]));

                    // Gộp dữ liệu vào product
                    let mergedProducts = products.map(product => ({
                        ...product,
                        brand_name: brandMap[product.brand_id] || "Không xác định",
                        category_name: categoryMap[product.category_id] || "Không xác định",
                    }));

                    console.log("Merged Products:", mergedProducts);
                    loadProductDataToTable(mergedProducts);
                } else {
                    console.error("Lỗi khi tải dữ liệu sản phẩm, biến thể, thương hiệu hoặc danh mục");
                }
            })
            .catch(error => {
                console.error("Lỗi API:", error);
            });
    }


    // LOAD DỮ LIỆU SẢN PHẨM LÊN FORM
    function loadProductDataToTable(productData) {
        let productTable = document.getElementById("product-table");
        productTable.innerHTML = "";
        productData.forEach((product, index) => {
            let imageUrl = product.image_name ? `../backend/uploads/products/${product.image_name}` : "default-image.jpg"; // Ảnh mặc định nếu không có ảnh
            console.log("Image URL:", imageUrl);
            productTable.innerHTML += `
        <tr>
            <td>${index + 1}</td>
            <td>${product.id}</td>
            <td><img src="${imageUrl}" width="50"></td>
            <td>${product.name}</td>
            <td>${product.brand_name}</td>
            <td>${product.category_name}</td>
            <td>${product.stock_quantity}</td>
            <td>${product.stop_selling}</td>
            <td>
                <button class="btn btn-info btn-view" data-id="${product.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-update" data-id="${product.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="${product.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        `;
        });
    }

    $(document).ready(function () {
        fetchAPIProducts(); // Gọi API để load danh sách sản phẩm
    });

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
                productData.image_base64 = base64Image; // Thêm ảnh base64 vào dữ liệu JSON
                console.log("Base64 Image:", base64Image);
                console.log("Product Info:", productData);
                console.log("variationData Info:", variationData);
                // Gọi hàm lưu sản phẩm và biến thể
                saveProduct(productData, variationData);
            };
            reader.readAsDataURL(imageFile);
        } else {

        }

    });

    function getNewProductInfo() {
        let modal = document.querySelector("#productForm");
        // return {
        //     name: modal.querySelector('#product_name').value,
        //     brand_id: modal.querySelector('#brand_id').value,
        //     model: modal.querySelector('#model').value,
        //     category_id: modal.querySelector('#category_id').value,
        //     description: modal.querySelector('#description').value,
        //     image_name: modal.querySelector('#image_name').value,
        //     stop_selling: modal.querySelector('#stop_selling').checked,
        // };

        return {
            name: "Smartwatch X Pro",
            brand_id: 1,
            model: "XPRO-2025",
            category_id: 3,
            description: "Đồng hồ thông minh với nhiều tính năng hiện đại",
            image_name: modal.querySelector('#image_name').value,
            stop_selling: false,
        };
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

        // return {
        //     watch_size_mm: modal.querySelector('#watch_size_mm').value,
        //     watch_color: modal.querySelector('#watch_color').value,
        //     display_type: modal.querySelector('#display_type').value,
        //     display_size_mm: modal.querySelector('#display_size_mm').value,
        //     resolution_w_px: modal.querySelector('#resolution_w_px').value,
        //     resolution_h_px: modal.querySelector('#resolution_h_px').value,
        //     ram_bytes: modal.querySelector('#ram_bytes').value,
        //     rom_bytes: modal.querySelector('#rom_bytes').value,
        //     connectivity: modal.querySelector('#connectivity').value,
        //     sensor: modal.querySelector('#sensors').value,
        //     case_material: modal.querySelector('#case_material').value,
        //     band_material: modal.querySelector('#band_material').value,
        //     band_size_mm: modal.querySelector('#band_size_mm').value,
        //     band_color: modal.querySelector('#band_color').value,
        //     battery_life_mah: modal.querySelector('#battery_life_mah').value,
        //     water_resistance_value: modal.querySelector('#water_resistance_value').value,
        //     water_resistance_unit: modal.querySelector('#water_resistance_unit').value,
        //     weight_milligrams: modal.querySelector('#weight_miligam').value,
        //     base_price_cents: modal.querySelector('#base_price_cents').value,
        //     price_cents: modal.querySelector('#price_cents').value,
        //     relase_date: formattedDate, // Thêm ngày hiện tại vào đây
        //     stop_selling: modal.querySelector('#stop_selling').checked,
        // };
        return {
            //image_name: modal.querySelector('#image_name').value,
            os_id: modal.querySelector('#os_select').value,
            watch_size_mm: 44,
            watch_color: "Black",
            display_type: "AMOLED",
            display_size_mm: 1.78,
            resolution_w_px: 368,
            resolution_h_px: 448,
            ram_bytes: 1024, // 1GB
            rom_bytes: 8192, // 8GB
            connectivity: "Bluetooth 5.2, Wi-Fi",
            sensor: "Heart Rate, SpO2, GPS",
            case_material: "Titanium",
            band_material: "Silicone",
            band_size_mm: 22,
            band_color: "Black",
            battery_life_mah: 450,
            water_resistance_value: 50,
            water_resistance_unit: "meters",
            weight_milligrams: 35000, // 35g
            base_price_cents: 19900, // $199.00
            price_cents: 24900, // $249.00
            release_date: formattedDate, // Thêm ngày hiện tại vào đây
            stop_selling: false,
        };
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
                variationData.image_name = product.image_name; // Sử dụng ảnh sản phẩm cho biến thể
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