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
        <h2>
            <i class="fas fa-receipt"></i> Order Details: <span id="orderIdText"></span>
            <span id="orderActions" class="ms-3"></span>
        </h2>

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
                    <th style="min-width:400px;">Product SKU</th>
                    <th style="width:60px;">Image</th>
                    <th style="min-width:200px;">Price (VND)</th>
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
                // Lấy thông tin đơn hàng & trạng thái giao hàng song song
                const [stateRes, orderRes] = await Promise.all([
                    $.getJSON(`${BASE_API_URL}/api/orders/delivery_states`),
                    $.getJSON(`${BASE_API_URL}/api/orders/${orderId}`)
                ]);

                if (!orderRes.success) throw new Error("Order not found.");

                let deliveryStateMap = {};
                if (stateRes.success) {
                    stateRes.data.forEach(state => {
                        deliveryStateMap[state.id] = state.name;
                    });
                }

                const order = orderRes.data[0];
                const deliveryState = deliveryStateMap[order.delivery_state_id] || "Unknown";

                // Hiển thị nút Approve & Cancel nếu trạng thái là Pending
                if (deliveryState.toLowerCase() === "pending") {
                    if(order.payment_method.toLowerCase() === "momo"){
                        $("#orderActions").html(`
                        <button class="btn btn-success me-2" id="approveOrder"><i class="fas fa-check"></i> Approve</button>
                        <button class="btn btn-primary" id="shipOrder"><i class="fas fa-truck"></i>To Ship</button>
                        <button class="btn btn-warning" id="receivedOrder"><i class="fas fa-check"></i> Received</button>
                        `);

                        $("#approveOrder").click(() => confirmUpdate(orderId, 2)); // ID 2 cho "Approved"
                        $("#shipOrder").click(() => confirmUpdate(orderId, 4)); // ID 4 cho "To Ship"
                        $("#receivedOrder").click(() => confirmUpdate(orderId, 5)); // ID 5 cho "Received"
                    }
                    else {
                        $("#orderActions").html(`
                        <button class="btn btn-success me-2" id="approveOrder"><i class="fas fa-check"></i> Approve</button>
                        <button class="btn btn-primary" id="shipOrder"><i class="fas fa-truck"></i>To Ship</button>
                        <button class="btn btn-warning" id="receivedOrder"><i class="fas fa-check"></i> Received</button>
                        <button class="btn btn-danger" id="cancelOrder"><i class="fas fa-times"></i> Cancel</button>
                        `);

                        $("#approveOrder").click(() => confirmUpdate(orderId, 2)); // ID 2 cho "Approved"
                        $("#cancelOrder").click(() => confirmUpdate(orderId, 3)); // ID 3 cho "Canceled"
                        $("#shipOrder").click(() => confirmUpdate(orderId, 4)); // ID 4 cho "To Ship"
                        $("#receivedOrder").click(() => confirmUpdate(orderId, 5)); // ID 5 cho "Received"
                    }
                }  
                else if (deliveryState.toLowerCase() === "approved") {
                    if(order.payment_method.toLowerCase() === "momo") {
                        $("#orderActions").html(`
                        <button class="btn btn-primary" id="shipOrder"><i class="fas fa-truck"></i>To Ship</button>
                        <button class="btn btn-warning" id="receivedOrder"><i class="fas fa-check"></i> Received</button>
                        `);

                        $("#shipOrder").click(() => confirmUpdate(orderId, 4)); // ID 4 cho "To Ship"
                        $("#receivedOrder").click(() => confirmUpdate(orderId, 5)); // ID 5 cho "Received"
                    }
                    else {
                        $("#orderActions").html(`
                        <button class="btn btn-primary" id="shipOrder"><i class="fas fa-truck"></i>To Ship</button>
                        <button class="btn btn-warning" id="receivedOrder"><i class="fas fa-check"></i> Received</button>
                        <button class="btn btn-danger" id="cancelOrder"><i class="fas fa-times"></i> Cancel</button>
                    `);
                        $("#shipOrder").click(() => confirmUpdate(orderId, 4)); // ID 4 cho "To Ship"
                        $("#cancelOrder").click(() => confirmUpdate(orderId, 3)); // ID 3 cho "Canceled"
                        $("#receivedOrder").click(() => confirmUpdate(orderId, 5)); // ID 5 cho "Received"
                    }
                }
                else if (deliveryState.toLowerCase() === "to ship") {
                    $("#orderActions").html(`
                        <button class="btn btn-warning" id="receivedOrder"><i class="fas fa-check"></i> Received</button>
                    `);
                    $("#receivedOrder").click(() => confirmUpdate(orderId, 5)); // ID 5 cho "Received"
                }

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
                            <td class="product-image"><img src="${imagePath}" alt="Product Image" width="60" height="60"></td>
                            <td>${price}</td>
                        </tr>
                    `;
                }

                $("#orderItemsTable").html(itemsHtml);

                // Cập nhật ảnh sản phẩm
                const images = await Promise.all(productPromises);
                $(".product-image img").each((index, img) => {
                    $(img).attr("src", images[index]);
                });

            } catch (error) {
                alert(error.message);
                window.location.href = "index.php?page=pages/Order/list.php";
            }
        });

        function confirmUpdate(orderId, newStateId) {
            const action = newStateId === 2 ? "approve" : newStateId === 3 ? "cancel" : newStateId === 4 ? "ship" : "receive";
            if (confirm(`Are you sure you want to ${action} this order?`)) {
                updateOrderStatus(orderId, newStateId);
            }
        }

        async function updateOrderStatus(orderId, newStateId) {
            try {
                const response = await $.ajax({
                    url: `${BASE_API_URL}/api/orders/${orderId}`,
                    type: 'PUT', // Cập nhật đơn hàng
                    contentType: 'application/json',
                    data: JSON.stringify({
                        delivery_state_id: newStateId, // Cập nhật trạng thái giao hàng mới
                    }),
                });

                if (response.success) {
                    alert(`Order has been ${newStateId === 2 ? 'approved' : newStateId === 3 ? 'canceled' : newStateId === 4 ? 'shipped' : 'received'} successfully!`);
                    window.location.href = `index.php?page=pages/Order/list.php`;
                } else {
                    alert(`Failed to update order: ${response.message || 'Unknown error'}`);
                }
            } catch (error) {
                alert(`Error updating order: ${error.message}`);
            }
        }
    </script>
</body>
</html>
