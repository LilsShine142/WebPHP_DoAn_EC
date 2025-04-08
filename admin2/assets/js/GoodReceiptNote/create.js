$(document).ready(function () {

    // Gọi ngay khi trang tải
    updateDateTime();

    // Cập nhật mỗi giây
    setInterval(updateDateTime, 1000);

    // Lấy danh sách nhà cung cấp, sản phẩm và load thông tin nhân viên
    fetchData();

    // Thêm sản phẩm mới
    $('#btnAddProduct').click(function () {
        addProduct();
    });

    // Cập nhật sản phẩm
    $('#btnUpdateProduct').click(function () {
        updateProduct();
    });

    // Submit form
    $('#goodsReceiptForm').submit(function (e) {
        e.preventDefault();
        saveGoodsReceiptNote();
    });
});

// Khởi tạo ngày hiện tại
function updateDateTime() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const localDatetime = `${year}-${month}-${day}T${hours}:${minutes}`;
    $('#created_at').val(localDatetime);
}

// Call api thấy danh sách nhà cung cấp, sản phẩm và thông tin nhân viên đang nhập
async function fetchData() {
    try {
        // Lấy staff_id từ input hidden trên giao diện
        const staff_id = $('input[name="staff_id"]').val();

        const [providersResponse, productsResponse, staffResponse] = await Promise.all([
            $.ajax({
                url: `${BASE_API_URL}/api/providers`,
                type: "GET",
                dataType: "json"
            }),
            $.ajax({
                url: `${BASE_API_URL}/api/products`,
                type: "GET",
                dataType: "json"
            }),
            staff_id ? $.ajax({
                url: `${BASE_API_URL}/api/users/${staff_id}`,
                type: "GET",
                dataType: "json"
            }) : Promise.resolve(null)
        ]);

        // Đổ dữ liệu nhà cung cấp vào select
        const providerSelect = $('#provider_id');
        providerSelect.empty().append('<option value="" selected disabled>-- Select Supplier --</option>');

        if (providersResponse.success && providersResponse.data) {
            providersResponse.data.forEach(provider => {
                providerSelect.append(
                    `<option value="${provider.id}">${provider.full_name}</option>`
                );
            });
        }

        // Đổ dữ liệu sản phẩm vào select
        const productSelect = $('#product_select');
        productSelect.empty().append('<option value="" selected disabled>-- Select Product --</option>');

        if (productsResponse.success && productsResponse.data) {
            productsResponse.data.forEach(product => {
                productSelect.append(
                    `<option value="${product.id}" 
                     productId="${product.id}">
                     ${product.name || "N/A"} 
                     </option>`
                );
            });
        }

        // Khi chọn sản phẩm, tự động điền đơn giá
        $('#product_select').change(function () {
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price');
            if (price) {
                $('#product_unit_price').val(price);
            }
        });

        // Cập nhật thông tin nhân viên nếu có
        if (staffResponse && staffResponse.success) {
            $('#staff_id_display').val(
                `${staffResponse.data.full_name} (ID: ${staffResponse.data.id})`
            );
            $('input[name="staff_id_display"]').val(staffResponse.data.id);
        }

        // Gắn sự kiện chọn sản phẩm -> gọi hàm loadProductVariations
        $('#product_select').off('change').on('change', function () {
            const productId = $('#product_select').val();
            console.log("PRODUCT ID", productId);
            // Hiện phần chọn phiên bản
            $('#product_variations_col').removeAttr('hidden');

            // Gọi hàm riêng để tải phiên bản
            if (productId) {
                loadProductVariations(productId);
            }
        });

    } catch (error) {
        console.error("Lỗi khi tải dữ liệu:", error);
        // Hiển thị thông báo lỗi cho người dùng
        alert('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.');
    }
}
// Hàm chú thích các keys của phiên bản sản phẩm
const attributeLabels = {
    watch_size_mm: "Watch Face Size",
    watch_color: "Watch Color",
    price_cents: "Price",
    display_size_mm: "Display Size",
    display_type: "Display Type",
    resolution_h_px: "Resolution (Height)",
    resolution_w_px: "Resolution (Width)",
    ram_bytes: "RAM Capacity",
    rom_bytes: "Internal Storage",
    connectivity: "Connectivity",
    battery_life_mah: "Battery Capacity",
    water_resistance_value: "Water Resistance",
    water_resistance_unit: "Water Resistance Unit",
    sensor: "Sensors",
    case_material: "Case Material",
    band_material: "Band Material",
    band_size_mm: "Band Size",
    band_color: "Band Color",
    weight_milligrams: "Weight",
    release_date: "Release Date",
};

// Hàm hiển thị tên phiên bản sản phẩm
function formatVariationText(variation, attributes) {
    return attributes
        .map(attr => {
            const label = attributeLabels[attr] || attr;
            const value = variation[attr];
            return value !== null && value !== undefined ? `${label}: ${value}` : null;
        })
        .filter(Boolean)
        .join(' | ');
}

// Xác định các thuộc tính phân biệt giữa các phiên bản (chỉ từ attributeLabels)
function getDistinctAttributes(data) {
    if (data.length === 0) return [];

    const attributeKeys = Object.keys(attributeLabels); // chỉ dùng các thuộc tính đã có chú thích

    return attributeKeys.filter(key => {
        const values = new Set(data.map(item => item[key]));
        return values.size > 1;
    }).slice(0, 4); // Lấy tối đa 4 thuộc tính khác nhau
}

// Load các phiên bản của sản phẩm
async function loadProductVariations(productId) {
    try {
        const variationSelect = $('#product_variation_select');
        variationSelect.empty().prop('disabled', true);

        const response = await $.ajax({
            url: `${BASE_API_URL}/api/products/variations?product_id=${productId}`,
            type: "GET",
            dataType: "json"
        });

        if (response.success && response.data.length > 0) {
            const variations = response.data;
            const distinctAttributes = getDistinctAttributes(variations);

            variationSelect.append('<option value="" selected disabled>-- Chọn phiên bản --</option>');

            let displayedVariations = variations;

            if (distinctAttributes.length === 0) {
                // Nếu không có điểm khác nhau, hiển thị tất cả phiên bản theo ID và giá
                displayedVariations.forEach(variation => {
                    const variationText = `ID: ${variation.id} | Price: ${variation.price_cents}`;
                    variationSelect.append(
                        `<option 
                value="${variation.id}" 
                productId="${productId}"
                product_variation_id="${variation.id}"
                data-price="${variation.price_cents}">
                ${variationText}
            </option>`
                    );
                });
            } else {
                // Có thuộc tính khác nhau, hiển thị theo các thuộc tính
                displayedVariations.forEach(variation => {
                    const variationText = formatVariationText(variation, distinctAttributes);
                    variationSelect.append(
                        `<option 
                value="${variation.id}" 
                productId="${productId}"
                product_variation_id="${variation.id}"
                data-price="${variation.price_cents}">
                ${variationText}
            </option>`
                    );
                });
            }


            variationSelect.prop('disabled', false);

            variationSelect.off('change').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                if (price) {
                    $('#product_unit_price').val(price);
                }
            });
        } else {
            variationSelect.append('<option value="" selected disabled>-- Không có phiên bản --</option>');
        }
    } catch (error) {
        console.error("Lỗi khi tải phiên bản sản phẩm:", error);
        $('#product_variation_select').empty().append('<option value="" selected disabled>-- Lỗi tải phiên bản --</option>');
    }
}


// Call api lấy product variation theo id của sản phẩm để lấy giá 
async function fetchProductVariation(productId) {
    try {
        const response = await $.ajax({
            url: `${BASE_API_URL}/api/products/${productId}`,
            type: "GET",
            dataType: "json"
        });
        if (response.success) {
            return response.data;
        } else {
            console.error("Lỗi khi tải dữ liệu sản phẩm:", response.message);
            return null;
        }
    } catch (error) {
        console.error("Lỗi API:", error);
        return null;
    }
}

let products = []; // Danh sách sản phẩm tạm
let editingIndex = null; // Vị trí sản phẩm đang chỉnh sửa

// Call api lấy product variation_id lớn nhất để tạo product instance
async function fetchMaxProductVariationId() {
    try {
        const response = await $.ajax({
            url: `${BASE_API_URL}/api/products/variations/latest`,
            type: "GET",
            dataType: "json"
        });
        if (response.success) {
            return response.data || 0;
        } else {
            console.error("Lỗi khi tải dữ liệu sản phẩm:", response.message);
            return null;
        }
    } catch (error) {
        console.error("Lỗi API:", error);
        return null;
    }
}

async function addProduct() {
    try {
        const productId = $('#product_select').val();
        const variationId = $('#product_variation_select').val();
        const productName = $('#product_select option:selected').text() + ' - ' + $('#product_variation_select option:selected').text();
        const quantity = parseInt($('#product_quantity').val()) || 0;
        const unitPrice = parseInt($('#product_unit_price').val()) || 0;

        if (!productId || !variationId || quantity <= 0 || unitPrice <= 0) {
            alert('Vui lòng điền đầy đủ thông tin sản phẩm với giá trị hợp lệ');
            return;
        }

        products.push({
            productId: productId,
            variationId: variationId,
            product_name: productName,
            quantity: quantity,
            unit_price: unitPrice
        });

        renderProductList();
        calculateTotals();
        resetAddProductForm();

    } catch (error) {
        console.error("Lỗi khi thêm sản phẩm:", error);
        alert("Có lỗi xảy ra khi thêm sản phẩm");
    }
}

async function updateProduct() {
    try {
        if (editingIndex === null || editingIndex < 0 || editingIndex >= products.length) {
            alert("Không tìm thấy sản phẩm cần cập nhật");
            return;
        }

        const productId = $('#product_select').val();
        const variationId = $('#product_variation_select').val();
        const productName = $('#product_select option:selected').text() + ' - ' + $('#product_variation_select option:selected').text();
        const quantity = parseInt($('#product_quantity').val());
        const unitPrice = parseFloat($('#product_unit_price').val());

        if (!productId || !variationId || isNaN(quantity) || quantity <= 0 || isNaN(unitPrice) || unitPrice <= 0) {
            alert('Vui lòng điền đầy đủ thông tin sản phẩm với giá trị hợp lệ');
            return;
        }

        products[editingIndex] = {
            productId: productId,
            variationId: variationId,
            product_name: productName,
            quantity: quantity,
            unit_price: unitPrice
        };

        renderProductList();
        calculateTotals();
        resetAddProductForm();

        $('#btnAddProduct').show();
        $('#btnUpdateProduct').hide();
        editingIndex = null;

    } catch (error) {
        console.error("Lỗi khi cập nhật sản phẩm:", error);
        alert("Có lỗi xảy ra khi cập nhật sản phẩm");
    }
}

function renderProductList() {
    const $productsList = $('#productsList');
    $productsList.empty();

    if (products.length === 0) {
        $productsList.append('<div class="alert alert-info">No products have been added yet</div>');
        return;
    }

    products.forEach((product, index) => {
        const $productItem = $(`
                    <div class="mb-3 p-3 bg-white rounded border" data-index="${index}">
                        <div class="row">
                            <div class="col-md-5">
                                <h6 class="mb-0">${product.product_name}</h6>
                            </div>
                            <div class="col-md-2">
                                <p class="mb-0">Quantity: ${product.quantity}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-0">Unit price: ${product.unit_price}</p>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-product">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);

        $productItem.hover(
            function () { $(this).addClass('bg-light'); },
            function () { $(this).removeClass('bg-light'); }
        );

        $productItem.click(function () {
            $('.border-primary').removeClass('border-primary');
            $(this).addClass('border-primary');
            editProduct(index);
        });

        $productsList.append($productItem);
    });

    // Xử lý sự kiện xóa sản phẩm
    $('.btn-remove-product').click(function (e) {
        e.stopPropagation();
        const index = $(this).closest('[data-index]').data('index');
        products.splice(index, 1);
        renderProductList();
        calculateTotals();
    });
}


function editProduct(index) {
    editingIndex = index;
    const product = products[index];

    $('#product_select').val(product.productId).trigger('change');

    // Đợi load xong các phiên bản rồi mới chọn
    setTimeout(() => {
        $('#product_variation_select').val(product.variationId);
        $('#product_quantity').val(product.quantity);
        $('#product_unit_price').val(product.unit_price);

        $('#btnAddProduct').hide();
        $('#btnUpdateProduct').show();

        $('html, body').animate({
            scrollTop: $('#product_select').offset().top - 100
        }, 300);
    }, 500);
}

function resetAddProductForm() {
    $('#product_select').val('');
    $('#product_quantity').val(1);
    $('#product_unit_price').val('');
}

function calculateTotals() {
    let totalQuantity = 0;
    let totalPrice = 0;

    products.forEach(product => {
        totalQuantity += product.quantity;
        totalPrice += product.quantity * product.unit_price;
    });

    $('#total_quantity').val(totalQuantity);
    $('#total_price_cents').val(totalPrice);
    //$('#total_price_vnd').val(formatCurrency(totalPrice));
}

function formatCurrency(amount) {
    // Chuyển từ cents sang VND (1 cent = 100 VND)
    const vnd = amount * 100;
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(vnd);
}

async function saveGoodsReceiptNote() {
    if (products.length === 0) {
        alert('Vui lòng thêm ít nhất một sản phẩm');
        return;
    }

    try {
        // 1. Lấy dữ liệu từ form để tạo phiếu nhập
        const noteData = {
            name: $('#name').val(),
            provider_id: $('#provider_id').val(),
            staff_id: $('#staff_id').val(),
            total_price_cents: $('#total_price_cents').val(),
            quantity: $('#total_quantity').val(),
            created_at: $('#created_at').val()
        };
        console.log("Note Data:", noteData);

        // 2. Gọi API tạo phiếu nhập
        const noteResponse = await $.ajax({
            url: `${BASE_API_URL}/api/goods_receipt_notes`,
            type: "POST",
            dataType: "json",
            data: JSON.stringify(noteData),
            contentType: "application/json"
        });

        if (!noteResponse.success) {
            throw new Error('Tạo phiếu nhập thất bại');
        }

        const goodsReceiptNoteId = noteResponse.data.id;
        console.log("Tạo phiếu nhập thành công, ID:", goodsReceiptNoteId);

        // 3. Gom nhóm sản phẩm theo productVariationId
        const productGroups = {};
        console.log("Danh sách sản phẩm:", products);
        products.forEach(product => {
            const variationId = product.variationId;
            if (!variationId) {
                throw new Error(`Thiếu productVariationId cho sản phẩm: ${product.productId}`);
            }

            if (!productGroups[variationId]) {
                productGroups[variationId] = {
                    quantity: product.quantity
                };
            } else {
                productGroups[variationId].quantity += product.quantity;
            }
        });

        console.log("Nhóm sản phẩm theo variation:", productGroups);

        // 4. Tạo danh sách promises tạo product_instance
        const instancePromises = [];

        Object.entries(productGroups).forEach(([variationId, group]) => {
            for (let i = 0; i < group.quantity; i++) {
                instancePromises.push(
                    $.ajax({
                        url: `${BASE_API_URL}/api/products/instances`,
                        type: "POST",
                        dataType: "json",
                        data: JSON.stringify({
                            product_variation_id: parseInt(variationId),
                            goods_receipt_note_id: goodsReceiptNoteId
                        }),
                        contentType: "application/json"
                    })
                );
            }
        });

        // 5. Gọi tất cả API tạo instance song song
        const results = await Promise.all(instancePromises);
        const failed = results.filter(r => !r.success);

        if (failed.length > 0) {
            console.error("Một số instances thất bại:", failed);
            throw new Error(`${failed.length}/${results.length} instances thất bại`);
        }

        console.log("Đã tạo thành công", results.length, "instances");
        alert(`Lưu thành công! Đã tạo ${results.length} sản phẩm chi tiết`);
        resetProductForm();

    } catch (error) {
        console.error("Lỗi khi lưu phiếu nhập:", error);
        alert("Lỗi: " + error.message);
    }
}


function resetProductForm() {
    // Reset các trường input
    $('#name').val('');
    $('#provider_id').val('');
    $('#product_select').val('');
    $('#product_variation_select').empty().append('<option value="" selected disabled>-- Select first product --</option>').prop('disabled', true);
    $('#product_quantity').val(1);
    $('#product_unit_price').val('');

    // Reset danh sách sản phẩm và tổng kết
    $('#productsList').html(`
        <div class="alert alert-info">No products have been added yet</div>
    `);

    $('#total_quantity').val('0');
    $('#total_price_cents').val('0');
    //$('#total_price_vnd').val('0 ₫');
}