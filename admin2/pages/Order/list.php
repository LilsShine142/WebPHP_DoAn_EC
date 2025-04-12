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
                <div class="row g-2 mt-2" style="display: flex; flex-wrap: nowrap;">
                    <div class="col-md-4">
                        <select id="deliveryState" class="form-select">
                            <option value="">All</option>
                            <!-- Các trạng thái giao hàng sẽ được tải vào đây -->
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary" id="approveOrderBtn">Approve Order</button>
                    </div>
                </div>
            </div>


            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Total (cents)</th>
                            <th style="width: 24%;">Delivery Address</th>
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
                                $("#deliveryState").append(`<option value="${state.id}">${state.name}</option>`);
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
                // reset nút checkall
                $("#selectAll").prop("checked", false);   
                $.ajax({
                    url: `${BASE_API_URL}/api/orders`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            renderOrders(response.data);
                        } else {
                            $("#orderTableBody").html("<tr><td colspan='10' class='text-center'>No orders found</td></tr>");
                        }
                    },
                    error: function() {
                        $("#orderTableBody").html("<tr><td colspan='10' class='text-center text-danger'>Failed to load data</td></tr>");
                    }
                });
            }

            function renderOrders(data) {
                let html = "";
                data.forEach(order => {
                    var stateName = deliveryStateMap[order.delivery_state_id] || "Unknown";
                    let formatCurrency = (amount) => new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                    html += `<tr>
                        <td><input type="checkbox" class="order-checkbox" value="${order.id}"></td>
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
                let selectedState = $("#deliveryState").val();

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
                                let matchesDeliveryState = selectedState === "" || order.delivery_state_id.toString() === selectedState;

                                return matchesSearch && matchesStartDate && matchesEndDate && matchesMinPrice && matchesMaxPrice && matchesDeliveryState;
                            });

                            renderOrders(filteredData);
                        } else {
                            $("#orderTableBody").html("<tr><td colspan='10' class='text-center'>No matching orders</td></tr>");
                        }
                    },
                    error: function() {
                        $("#orderTableBody").html("<tr><td colspan='10' class='text-center text-danger'>Failed to filter data</td></tr>");
                    }
                });
            }

            // Load dữ liệu ban đầu
            loadDeliveryStates().done(function() {
                loadOrders();
            });

            // Select/Deselect all checkboxes
            $("#selectAll").on("change", function() {
                $(".order-checkbox").prop("checked", this.checked);
            });

            // Allow filtering when input changes
            $("#searchInput, #startDate, #endDate, #minPrice, #maxPrice, #deliveryState").on("change input", function() {
                filterOrders();
            });

            // Approve selected orders
            $("#approveOrderBtn").on("click", function() {
                // Lấy danh sách các ID đơn hàng đã chọn nhưng phải là pending
                const selectedOrders = $(".order-checkbox:checked").filter(function() {
                    const orderId = $(this).val();
                    const orderRow = $(this).closest('tr');
                    const stateCell = orderRow.find('td:nth-child(6)'); // Adjust index based on your table structure

                    return stateCell.text().trim() === "Pending"; // Check if the state is 'Pending'
                }).map(function() {
                    return $(this).val();
                }).get();

                if (selectedOrders.length === 0) {
                    alert("Please select at least one order with state 'Pending' to approve.");
                    return;
                }
                // nhắc nhở người dùng trước khi phê duyệt
                if (confirm(`Are you sure you want to approve the selected orders?`)) {
                    selectedOrders.forEach(orderId => {
                    $.ajax({
                        url: `${BASE_API_URL}/api/orders/${orderId}`,
                        type: 'PUT', // Cập nhật đơn hàng
                        contentType: 'application/json',
                        data: JSON.stringify({
                            delivery_state_id: 2, // Cập nhật trạng thái giao hàng thành "Approved"
                        })
                    });
                    });
                    alert("Orders approved successfully!");
                    loadOrders(); // Tải lại danh sách đơn hàng sau khi phê duyệt
                }
            });
        });
    </script>

</body>

</html>