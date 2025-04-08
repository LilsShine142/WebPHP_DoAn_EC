<?php
if (!isset($_GET['id'])) {
    echo "<script>alert('Order ID is missing!'); window.location.href='index.php?page=pages/Order/list.php';</script>";
    exit;
}

$orderId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-5">
        <h2><i class="fas fa-receipt"></i> Order Details: <span id="orderIdText"></span></h2>

        <table class="table table-bordered mt-3">
            <tbody id="orderDetails">
                <tr><td colspan="2" class="text-center">Loading...</td></tr>
            </tbody>
        </table>

        <h3><i class="fas fa-box"></i> Order Items</h3>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th style="min-width: 100px;">Item ID</th>
                    <th style="min-width:500px;">Product SKU</th>
                    <th style="width:80px;">Image</th>
                    <th style="min-width:150px;">Price (VND)</th>
                </tr>
            </thead>
            <tbody id="orderItemsTable">
                <tr><td colspan="4" class="text-center">Loading...</td></tr>
            </tbody>
        </table>

        <a href="index.php?page=pages/Order/list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <script>
        $(document).ready(async function () {
            const orderId = "<?php echo $orderId; ?>";
            $("#orderIdText").text("#" + orderId);

            const formatCurrency = (amount) => 
                new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);

            try {
                // Gọi tất cả API bằng Promise.all để tăng tốc độ tải
                const [stateRes, orderRes] = await Promise.all([
                    $.getJSON(`${BASE_API_URL}/api/orders/delivery_states`),
                    $.getJSON(`${BASE_API_URL}/api/orders/${orderId}`)
                ]);

                if (!orderRes.success) throw new Error("Order not found.");

                // Xử lý dữ liệu trạng thái giao hàng
                let deliveryStateMap = {};
                if (stateRes.success) {
                    stateRes.data.forEach(state => {
                        deliveryStateMap[state.id] = state.name;
                    });
                }

                const order = orderRes.data[0];
                const deliveryState = deliveryStateMap[order.delivery_state_id] || "Unknown";

                // Lấy thông tin người dùng
                const userRes = await $.getJSON(`${BASE_API_URL}/api/users/${order.user_id}`);
                const user = userRes.success ? userRes.data : { full_name: "Unknown", phone_number: "N/A" };

                $("#orderDetails").html(`
                    <tr><th><i class="fas fa-user"></i> Customer Name</th><td>${user.full_name}</td></tr>
                    <tr><th><i class="fas fa-phone"></i> Phone Number</th><td>${user.phone_number}</td></tr>
                    <tr><th><i class="fas fa-money-bill-wave"></i> Total</th><td>${formatCurrency(order.total_cents)}</td></tr>
                    <tr><th><i class="fas fa-map-marker-alt"></i> Delivery Address</th><td>${order.delivery_address}</td></tr>
                    <tr><th><i class="fas fa-truck"></i> Delivery State</th><td>${deliveryState}</td></tr>
                    <tr><th><i class="fas fa-calendar-day"></i> Order Date</th><td>${order.order_date}</td></tr>
                    <tr><th><i class="fas fa-calendar-check"></i> Estimate Received Date</th><td>${order.estimate_received_date}</td></tr>
                    <tr><th><i class="fas fa-box"></i> Received Date</th><td>${order.received_date ?? 'N/A'}</td></tr>
                    <tr><th><i class="fas fa-credit-card"></i> Payment Method</th><td>${order.payment_method}</td></tr>
                `);

                // Lấy danh sách sản phẩm
                const itemsRes = await $.getJSON(`${BASE_API_URL}/api/orders/items?order_id=${orderId}`);
                if (!itemsRes.success || !itemsRes.data.length) {
                    $("#orderItemsTable").html("<tr><td colspan='4' class='text-center'>No items found.</td></tr>");
                    return;
                }

                let itemsHtml = "";
                let productPromises = [];

                for (const item of itemsRes.data) {
                    const sku = item.product_instance_sku;
                    const price = formatCurrency(item.price_cents);
                    let imagePath = "../backend/uploads/products/default.png"; 

                    // Đẩy các promise lấy sản phẩm vào mảng
                    productPromises.push(
                        $.getJSON(`${BASE_API_URL}/api/products/instances/${sku}`)
                            .then(productRes => {
                                if (productRes.success) {
                                    return $.getJSON(`${BASE_API_URL}/api/products/variations/${productRes.data.product_variation_id}`)
                                        .then(variationRes => variationRes.success ? `../backend/uploads/products/${variationRes.data.image_name}` : imagePath);
                                }
                                return imagePath;
                            })
                            .catch(() => imagePath)
                    );

                    itemsHtml += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${sku}</td>
                            <td class="product-image"><img src="${imagePath}" alt="Product Image" width="80" height="80"></td>
                            <td>${price}</td>
                        </tr>
                    `;
                }

                $("#orderItemsTable").html(itemsHtml);

                // Cập nhật ảnh sản phẩm sau khi lấy dữ liệu
                const images = await Promise.all(productPromises);
                $(".product-image img").each((index, img) => {
                    $(img).attr("src", images[index]);
                });

            } catch (error) {
                alert(error.message);
                window.location.href = "index.php?page=pages/Order/list.php";
            }
        });
    </script>
</body>
</html>