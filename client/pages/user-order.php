<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-5">
    <div style="width: 80%; margin: auto; margin-bottom: 40px;">
        <!-- Top Navigation -->
        <div class="flex flex-wrap justify-between bg-white p-4 shadow-md rounded-lg mb-4" style="margin-top: 40px;">
            <div class="w-full mb-2 flex justify-center">
                <button class="filter-btn px-4 py-2 bg-blue-500 text-white" data-status="all">All</button>
                <button class="filter-btn px-4 py-2" data-status="1">Pending</button>
                <button class="filter-btn px-4 py-2" data-status="2">Approve</button>
                <button class="filter-btn px-4 py-2" data-status="3">Canceled</button>
            </div>
            <div class="w-full flex justify-center">
                <input type="text" id="searchInput" class="w-1/3 p-2 border rounded-md" placeholder="You can search by Order ID or Product name">
            </div>
        </div>

        
        <!-- Danh sách đơn hàng -->
        <div id="ordersList" class="space-y-4">
            <!-- Mỗi đơn hàng -->
            
        </div>

        <script>
            document.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');
                    filterOrders(this.getAttribute('data-status'));
                });
            });
            
            function filterOrders(status) {
                document.querySelectorAll('.order').forEach(order => {
                    order.style.display = (status === 'all' || order.getAttribute('data-status') === status) ? 'block' : 'none';
                });
            }
            
            document.getElementById('searchInput').addEventListener('input', function () {
                let keyword = this.value.toLowerCase();
                document.querySelectorAll('.order').forEach(order => {
                    let orderId = order.querySelector('h2').textContent.toLowerCase();
                    let productName = order.querySelector('.font-semibold').textContent.toLowerCase();
                    order.style.display = (orderId.includes(keyword) || productName.includes(keyword)) ? 'block' : 'none';
                });
            });
        </script>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
    }

    $(document).ready(function() {
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            
            $.ajax({
                url: `${BASE_API_URL}/api/orders?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    $("#ordersList").empty();
                    
                    response.data.forEach(order => {
                        const orderElement = $(`
                            <div class="order bg-white p-4 rounded-lg shadow-md" data-status="${order.delivery_state_id}">
                                <h2 class="font-bold text-lg">Order ID: #${order.id}</h2>
                                <p class="text-sm text-gray-600">Status: ${order.delivery_state_id == 1 ? "Pending" : order.delivery_state_id == 2 ? "Approved" : "Canceled"}</p>
                                <div class="grid grid-cols-1 gap-3 mt-3" id="orderItems">
                                    
                                </div>
                                <div class="mt-3 text-right font-bold text-lg">Total: ${formatCurrency(order.total_cents)}</div>
                            </div>
                        `);
                        $("#ordersList").append(orderElement);
                        $.ajax({
                            url: `${BASE_API_URL}/api/orders/${order.id}`,
                            type: "GET",
                            success: function(response) {
                                response.data.forEach(variant => {
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
                                console.log("Có lỗi xảy ra khi lấy thông tin đơn hàng.");
                            }
                        });
                    });
                },
                error: function() {
                    console.log("Có lỗi xảy ra khi lấy danh sách đơn hàng.");
                }
            });
        } else {
            console.log("Không tìm thấy dữ liệu user trong localStorage.");
        }
    });
</script>
