<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Order List</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card-search" style="display: flex; margin: 15px;">
            <div class="search" style="width: 80%; margin-right: 30px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Search orders...">
            </div>
            <a href="index.php?page=pages/Order/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Order
            </a>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Total (cents)</th>
                        <th>Delivery Address</th>
                        <th>Delivery State</th>
                        <th>Order Date</th>
                        <th>Estimate Received Date</th>
                        <th>Received Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <!-- Dữ liệu sẽ được AJAX tải vào đây -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript xử lý AJAX -->
<script>
$(document).ready(function() {
    let deliveryStateMap = {};

    function loadDeliveryStates() {
        return $.ajax({
            url: "http://localhost:81/WebPHP_DoAn_EC/api/orders/delivery_states",
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    response.data.forEach(state => {
                        deliveryStateMap[state.id] = state.name;
                    });
                } else {
                    console.warn("No delivery states found");
                }
            },
            error: function() {
                console.error("Failed to load delivery states");
            }
        });
    }

    function loadOrders() {
        $.ajax({
            url: "http://localhost:81/WebPHP_DoAn_EC/api/orders",
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = "";
                    response.data.forEach(order => {
                        var stateName = deliveryStateMap[order.delivery_state_id] || "Unknown";
                        html += `<tr>
                                    <td>${order.id}</td>
                                    <td>${order.user_id}</td>
                                    <td>${order.total_cents.toLocaleString()}</td>
                                    <td>${order.delivery_address}</td>
                                    <td>${stateName}</td>
                                    <td>${order.order_date}</td>
                                    <td>${order.estimate_received_date}</td>
                                    <td>${order.received_date ?? 'N/A'}</td>
                                    <td>
                                        <a href='index.php?page=pages/Order/details.php&id=${order.id}' class='btn btn-info btn-sm' title='View'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <a href='index.php?page=pages/Order/update.php&id=${order.id}' class='btn btn-warning btn-sm' title='Edit'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <button class='btn btn-danger btn-sm' title='Delete' onclick='deleteOrder(${order.id})'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>`;
                    });
                    $("#orderTableBody").html(html);
                } else {
                    $("#orderTableBody").html("<tr><td colspan='9' class='text-center'>No orders found</td></tr>");
                }
            },
            error: function() {
                $("#orderTableBody").html("<tr><td colspan='9' class='text-center text-danger'>Failed to load data</td></tr>");
            }
        });
    }

    // Đảm bảo API delivery_states tải xong trước khi gọi loadOrders
    loadDeliveryStates().done(function() {
        loadOrders();
    });
});
</script>

</body>
</html>
