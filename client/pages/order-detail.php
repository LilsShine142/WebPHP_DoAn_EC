<?php
$order_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$order_id) {
    echo "Không tìm thấy sản phẩm!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .stepper {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            width: 80%;
        }
        .step {
            flex: 1;
            text-align: center;
        }
        .step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-weight: bold;
        }
        .step .line {
            height: 2px;
            width: 82%;
            background-color: #e0e0e0;
            flex-grow: 1;
            margin: 0 auto;
            position: relative;
            top: 15px; /* Center the line with respect to the circle */
            right: 50%;
        }
        .step .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            top: -10px; /* Adjust position to align with the circle */
        }
        .step .icon {
            font-size: 20px;
            color: white; /* Change icon color to white */
        }
        .step .text {
            margin-top: 10px;
        }

        .completed .circle {
            background-color: #4caf50; 
            border-color: #4caf50;
            color: white;
        }
        .completed .icon-circle {
            background-color: #4caf50; 
        }
        .completed .line {
            background-color: #4caf50; 
        }
    </style>
</head>
<body class="bg-gray-100 p-5">
    <div class="container mx-auto my-10" style="display: flex; flex-direction: column; align-items: center;">
        <h1 class="text-2xl font-bold mb-5">Order Detail #<?php echo htmlspecialchars($order_id); ?></h1>

        <div class="stepper">
            <div class="step pending">
                <div class="icon-circle">
                    <i class="fas fa-shopping-cart icon"></i>
                </div>
                <div class="text">Pending</div>
            </div>
            <div class="step approved">
                <div class="line"></div>
                <div class="icon-circle">
                    <i class="fas fa-check-circle icon"></i>
                </div>
                <div class="text">Approved</div>
            </div>
            <div class="step to-ship">
                <div class="line"></div>
                <div class="icon-circle">
                    <i class="fas fa-truck icon"></i>
                </div>
                <div class="text">To Ship</div>
            </div>
            <div class="step received">
                <div class="line"></div>
                <div class="icon-circle">
                    <i class="fas fa-box-open icon"></i>
                </div>
                <div class="text">Received</div>
            </div>
        </div>

        <div id="orderDetails" class="bg-white p-5 rounded-lg shadow-md" style="width: 80%;">
            <!-- Thông tin đơn hàng sẽ được tải ở đây -->
        </div>
    </div>

    
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;

            // Gọi API để lấy thông tin chi tiết đơn hàng
            $.ajax({
                url: `${BASE_API_URL}/api/orders/${<?php echo $order_id; ?>}`,
                type: "GET",
                success: function(response) {
                    $("#orderDetails").empty();

                    const order = response.data;

                    // Cập nhật trạng thái cho các bước trong stepper
                    $(".step").removeClass("completed");
                    if (order[0].delivery_state_id == 1) {
                        $(".step.pending").addClass("completed");
                    } else if (order[0].delivery_state_id == 2) {
                        $(".step.pending").addClass("completed");
                        $(".step.approved").addClass("completed");
                    } else if (order[0].delivery_state_id == 4) {
                        $(".step.pending").addClass("completed");
                        $(".step.approved").addClass("completed");
                        $(".step.to-ship").addClass("completed");
                    } else if (order[0].delivery_state_id == 5) {
                        $(".step.pending").addClass("completed");
                        $(".step.approved").addClass("completed");
                        $(".step.to-ship").addClass("completed");
                        $(".step.received").addClass("completed");
                    }
                    
                    const orderElement = $(`
                        <div class="order bg-white p-4 rounded-lg shadow-md" data-status="${order[0].delivery_state_id}">
                            <h2 class="font-bold text-lg">Order ID: #${order[0].id}</h2>

                            <p class="text-lg">Shipping Address: ${order[0].delivery_address}</p>

                            <p class="font-bold text-lg">Delivery State: ${order[0].delivery_state_id == 1 ? "Pending" : order[0].delivery_state_id == 2 ? "Approved" : order[0].delivery_state_id == 3 ? "Canceled" : order[0].delivery_state_id == 4 ? "To Ship" : "Received"}</p>

                            <p class="text-lg">Payment Method: ${order[0].payment_method}</p>

                            <div class="grid grid-cols-1 gap-3 mt-3" id="orderItems">
                                
                            </div>

                            <div class="mt-3 text-right font-bold text-lg">Total: ${formatCurrency(order[0].total_cents)}</div>
                        </div>
                    `);
                    $("#orderDetails").append(orderElement);

                    order.forEach(variant => {
                        const orderItems = $(`
                        <div class="flex items-center p-3 border rounded-lg">
                            <img src="../backend/uploads/products/${variant.image_name}" alt="Sản phẩm" class="w-16 h-16 object-cover rounded-md">
                            <div class="ml-3">
                                <p class="font-semibold">${variant.name}</p>
                                <p class="text-sm text-gray-600">Viriation: ${variant.watch_color} - ${variant.watch_size_mm} mm</p>
                                <p class="text-sm">Quantity: ${variant.quantity}</p>
                                <p class="text-sm font-bold">Price: ${formatCurrency(variant.price_cents)}</p>
                            </div>
                        </div>`);
                        orderElement.find("#orderItems").append(orderItems);
                    });
                },
                error: function() {
                    console.log("Có lỗi xảy ra khi lấy đơn hàng.");
                }
            });
        } else {
            $("#orderDetails").append(`<p>Không tìm thấy dữ liệu người dùng trong localStorage.</p>`);
        }
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(amount); // Chia cho 100 nếu giá được lưu dưới dạng cents
    }
</script>
