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

            <div class="card-search px-3 py-2">
                <div class="row g-2">
                    <!-- Ô tìm kiếm -->
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search orders...">
                    </div>
                    <!-- Ngày bắt đầu -->
                    <div class="col-md-2">
                        <input type="date" id="startDate" class="form-control" placeholder="Start Date">
                    </div>
                    <!-- Ngày kết thúc -->
                    <div class="col-md-2">
                        <input type="date" id="endDate" class="form-control" placeholder="End Date">
                    </div>
                    <!-- Giá tối thiểu -->
                    <div class="col-md-2">
                        <input type="number" id="minPrice" class="form-control" placeholder="Min Price">
                    </div>
                    <!-- Giá tối đa -->
                    <div class="col-md-2">
                        <input type="number" id="maxPrice" class="form-control" placeholder="Max Price">
                    </div>
                </div>
            </div>


            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Total (cents)</th>
                            <th>Delivery Address</th>
                            <th>State</th>
                            <th>Order Date</th>
                            <th>Received Date</th>
                            <th>Payment</th>
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
                    url: `${BASE_API_URL}/api/orders/delivery_states`,
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
                    url: `${BASE_API_URL}/api/orders`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            renderOrders(response.data);
                        } else {
                            $("#orderTableBody").html("<tr><td colspan='9' class='text-center'>No orders found</td></tr>");
                        }
                    },
                    error: function() {
                        $("#orderTableBody").html("<tr><td colspan='9' class='text-center text-danger'>Failed to load data</td></tr>");
                    }
                });
            }

            function renderOrders(data) {
                let html = "";
                data.forEach(order => {
                    var stateName = deliveryStateMap[order.delivery_state_id] || "Unknown";
                    let formatCurrency = (amount) => new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(amount);
                    html += `<tr>
                        <td>${order.id}</td>
                        <td>${order.user_id}</td>
                        <td>${formatCurrency(order.total_cents)}</td>
                        <td>${order.delivery_address}</td>
                        <td>${stateName}</td>
                        <td>${order.order_date}</td>
                        <td>${order.estimate_received_date}</td>
                        <td>${order.payment_method}</td>
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
            }

            function filterOrders() {
                let searchValue = $("#searchInput").val().toLowerCase();
                let startDate = $("#startDate").val();
                let endDate = $("#endDate").val();
                let minPrice = $("#minPrice").val();
                let maxPrice = $("#maxPrice").val();

                $.ajax({
                    url: `${BASE_API_URL}/api/orders`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let filteredData = response.data.filter(order => {
                                let matchesSearch = searchValue === "" ||
                                    order.id.toString().includes(searchValue) ||
                                    order.user_id.toString().includes(searchValue) ||
                                    order.delivery_address.toLowerCase().includes(searchValue);

                                let matchesStartDate = startDate === "" || new Date(order.order_date) >= new Date(startDate);
                                let matchesEndDate = endDate === "" || new Date(order.order_date) <= new Date(endDate);

                                let matchesMinPrice = minPrice === "" || order.total_cents >= parseInt(minPrice);
                                let matchesMaxPrice = maxPrice === "" || order.total_cents <= parseInt(maxPrice);

                                return matchesSearch && matchesStartDate && matchesEndDate && matchesMinPrice && matchesMaxPrice;
                            });

                            renderOrders(filteredData);
                        } else {
                            $("#orderTableBody").html("<tr><td colspan='9' class='text-center'>No matching orders</td></tr>");
                        }
                    },
                    error: function() {
                        $("#orderTableBody").html("<tr><td colspan='9' class='text-center text-danger'>Failed to filter data</td></tr>");
                    }
                });
            }

            // Load dữ liệu ban đầu
            loadDeliveryStates().done(function() {
                loadOrders();
            });

            // Cho phép lọc ngay khi nhập vào các ô
            $("#searchInput, #startDate, #endDate, #minPrice, #maxPrice").on("input", function() {
                filterOrders();
            });
        });
    </script>

</body>

</html>