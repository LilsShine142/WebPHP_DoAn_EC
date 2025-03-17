document.addEventListener("DOMContentLoaded", function () {
    console.log("statisticsActions.js đã được tải thành công!");
    //================================================= BOX SMALL =================================================
    // Lấy dữ liệu oders để hiển thị vào box small
    function getBoxSmallData() {
        console.log("Gọi API lấy tổng số đơn hàng, sản phẩm, người dùng, doanh thu...");

        let apiOrders = `${BASE_API_URL}/api/orders`;
        console.log("apiOrders:", apiOrders);
        let apiProducts = `${BASE_API_URL}/api/products`;
        let apiUsers = `${BASE_API_URL}/api/users`;
        //let apiRevenue = `${BASE_API_URL}/api/revenue`;

        // Gọi tất cả API cùng lúc
        Promise.all([
            $.get(apiOrders),
            $.get(apiProducts),
            $.get(apiUsers),
            //$.get(apiRevenue)
        ])
            .then(([orders, products, users]) => {
                console.log("Dữ liệu API Orders:", orders);
                console.log("Dữ liệu API Products:", products);
                console.log("Dữ liệu API Users:", users);
                //console.log("Dữ liệu API Revenue:", revenue);

                if (orders.success) {
                    $(".totalOrders").text(orders.data.length);
                    $(".totalRevenue").text(getRevenueData(orders.data));
                }
                if (products.success) {
                    $(".totalProducts").text(products.data.length);
                }
                if (users.success) {
                    $(".totalUsers").text(users.data.length);
                }
                // if (revenue.success) {
                //     $(".totalRevenue").text(revenue.data.total);
                // }
            })
            .catch(error => {
                console.error("Lỗi khi lấy dữ liệu bảng điều khiển:", error);
            });
    }

    function getRevenueData(ordersData) {
        console.log("Gọi API lấy doanh thu...");

        let revenue = 0;

        // Lấy id của state = 3 Delivered (Đã giao hàng)
        if (ordersData.length > 0) {
            for (let i = 0; i < ordersData.length; i++) {
                if (ordersData[i].delivery_state_id === 3) {
                    revenue += ordersData[i].total_cents;
                }
            }
        }
        console.log("Doanh thu:", revenue);
        // Chuyển từ cents sang VND và định dạng tiền tệ
        let formattedRevenue = revenue.toLocaleString("vi-VN", { style: "currency", currency: "VND" });

        console.log("Tổng doanh thu:", formattedRevenue);
        return formattedRevenue;
    }

    getBoxSmallData();

    //================================================= TẠO BIỂU ĐỒ THỐNG KÊ =================================================
    // Lấy dữ liệu để tạo biểu đồ thống kê
    // Khi chọn bộ lọc, gọi API với tham số phù hợp
    $(document).ready(function () {
        document.getElementById("monthPicker").setAttribute("lang", "en");
        let revenueData = [];
        let expenseData = [];
        let labels = [];
        let revenueChart = null;

        const chartFilter = $("#chartFilter");
        const yearPickerContainer = $("#yearPickerContainer");
        const monthPickerContainer = $("#monthPickerContainer");
        const yearPicker = $("#yearPicker");
        const monthPicker = $("#monthPicker");

        // Lấy năm và tháng hiện tại
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().toISOString().slice(0, 7); // Format YYYY-MM
        console.log("Năm hiện tại:", currentYear);
        console.log("Tháng hiện tại:", currentMonth);
        // Gán giá trị mặc định cho input chọn năm & tháng
        yearPicker.val(currentYear);
        monthPicker.val(currentMonth);

        // Load dữ liệu ban đầu theo năm hiện tại
        loadStatistics(`year=${currentYear}`, "filter-year");

        // Xử lý thay đổi loại bộ lọc (Năm hoặc Tháng)
        chartFilter.change(function () {
            const selectedFilter = $(this).val();
            const isYearFilter = selectedFilter === "filter-year";

            yearPickerContainer.toggle(isYearFilter);
            monthPickerContainer.toggle(!isYearFilter);

            if (isYearFilter) {
                loadStatistics(`year=${yearPicker.val()}`, "filter-year");
            } else {
                const [year, month] = monthPicker.val().split("-");
                loadStatistics(`year=${year}&month=${month}`, "filter-month");
                console.log("Tháng:", month);
            }
        });
        console.log("expensedata:", expenseData);
        // Khi thay đổi năm, cập nhật dữ liệu
        yearPicker.change(() => loadStatistics(`year=${yearPicker.val()}`, "filter-year"));

        // Khi thay đổi tháng, cập nhật dữ liệu
        monthPicker.change(() => {
            const [year, month] = monthPicker.val().split("-");
            loadStatistics(`year=${year}&month=${month}`, "filter-month");
        });

        /**
         * Hàm gọi API để lấy dữ liệu thống kê
         * @param {string} period - Chuỗi truy vấn dạng `year=YYYY` hoặc `year=YYYY&month=MM`
         * @param {string} filterType - Loại bộ lọc (`filter-year` hoặc `filter-month`)
         */
        function loadStatistics(period, filterType) {
            $.ajax({
                url: `${BASE_API_URL}/api/statistics/financial?${period}`,
                method: "GET",
                dataType: "json",
                success: function (response) {
                    console.log("Dữ liệu thống kê:", response);

                    if (response.success && response.data.revenue && response.data.expense) {
                        revenueData = response.data.revenue;
                        expenseData = response.data.expense;
                        updateCharts(filterType);
                        // Gọi updateProductTable sau khi nhận dữ liệu API
                        console.log("expenseData cho table:", expenseData);
                        // Gọi cập nhật bảng sản phẩm theo ngày/tháng
                        updateProductTable(expenseData, filterType);
                    } else {
                        alert("Dữ liệu không hợp lệ!");
                    }
                },
                error: function () {
                    alert("Không thể tải dữ liệu thống kê!");
                }
            });
        }

        /**
         * Cập nhật biểu đồ với dữ liệu mới
         * @param {string} filterType - Loại bộ lọc (`filter-year` hoặc `filter-month`)
         */
        function updateCharts(filterType) {
            const revenueMap = new Map();
            const expenseMap = new Map();

            // Xác định nhãn (labels) dựa trên bộ lọc
            if (filterType === "filter-year") {
                labels = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
            } else {
                const [year, month] = monthPicker.val().split("-");
                const daysInMonth = new Date(year, month, 0).getDate();
                labels = Array.from({ length: daysInMonth }, (_, i) => `Day ${i + 1}`);
            }

            // Lưu doanh thu vào Map
            revenueData.forEach(item => revenueMap.set(item.period, Number(item.revenue)));

            // Tính tổng chi phí theo mỗi kỳ (ngày/tháng)
            expenseData.forEach(item => {
                //const totalExpense = item.orders.reduce((sum, order) => sum + order.total_expense, 0);
                expenseMap.set(item.period, item.orders[0].total_expense);  // Trong api có trả về total_expense nên không cần tính lại
            });

            // Chuyển dữ liệu từ Map sang mảng, gán mặc định 0 nếu không có dữ liệu
            const revenue = labels.map(label => revenueMap.get(label) || 0);
            const expense = labels.map(label => expenseMap.get(label) || 0);
            const profit = revenue.map((rev, index) => rev - expense[index]);

            // Vẽ biểu đồ
            drawChart(labels, revenue, expense, profit);
        }

        /**
         * Hàm vẽ biểu đồ bằng Chart.js
         * @param {Array} labels - Mảng nhãn (tháng hoặc ngày)
         * @param {Array} revenue - Mảng doanh thu
         * @param {Array} expense - Mảng chi phí
         * @param {Array} profit - Mảng lợi nhuận
         */
        function drawChart(labels, revenue, expense, profit) {
            const ctx = document.getElementById("revenue-chart-canvas").getContext("2d");

            // Hủy biểu đồ cũ trước khi vẽ mới
            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Revenue",
                            data: revenue,
                            backgroundColor: "rgb(54, 162, 235)" 
                        },
                        {
                            label: "Expense",
                            data: expense,
                            backgroundColor: "rgb(255, 99, 132)" 
                        },
                        {
                            label: "Profit",
                            data: profit,
                            backgroundColor: "rgb(75, 192, 192)" 
                        }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        //================================================= HIỂN THỊ DỮ LIỆU THỐNG KÊ RA BẢNG =================================================
        function updateProductTable(expenseData, filterType) {
            console.log("Dữ liệu truyền vào updateProductTable:", expenseData);
            let tableBody = $("#product-table tbody");
            tableBody.empty(); // Xóa dữ liệu cũ

            let productMap = new Map();

            // Kiểm tra nếu dữ liệu hợp lệ
            if (!expenseData || !Array.isArray(expenseData)) {
                console.error("Dữ liệu không hợp lệ!");
                return;
            }

            // Lặp qua các đơn hàng và sản phẩm
            expenseData.forEach(item => {
                if (!item.orders || !Array.isArray(item.orders)) return;

                item.orders.forEach(order => {
                    if (!order.products || !Array.isArray(order.products)) return;

                    order.products.forEach(product => {
                        let orderDate = new Date(product.order_day); // Lấy đúng order_day

                        let groupKey;
                        if (filterType === "filter-year") {
                            groupKey = `${orderDate.getFullYear()}-${(orderDate.getMonth() + 1).toString().padStart(2, '0')}`; // YYYY-MM
                        } else if (filterType === "filter-month") {
                            groupKey = orderDate.toISOString().split('T')[0]; // YYYY-MM-DD
                        } else {
                            groupKey = orderDate.getFullYear().toString(); // Lọc theo năm
                        }

                        let key = `${groupKey}-${product.product_id}`;

                        if (!productMap.has(key)) {
                            productMap.set(key, {
                                date: groupKey,
                                name: product.product_name || "N/A",
                                color: product.watch_color || "N/A",
                                size: product.watch_size_mm || "N/A",
                                stock_quantity: product.stock_quantity || "N/A",
                                quantity: 0,
                                status: product.stop_selling,
                                unitPrice: Number(product.unit_price) || 0,
                                total: 0
                            });
                        }

                        let productData = productMap.get(key);
                        productData.quantity += 1;
                        productData.total = productData.quantity * productData.unitPrice;
                    });

                    // Chuyển Map thành mảng để hiển thị
                    let mergedProducts = Array.from(productMap.values());

                    console.log(mergedProducts);
                });
            });

            // Hiển thị dữ liệu trong bảng
            productMap.forEach(product => {
                let row = `
        <tr>
            <td>${product.date}</td>
            <td>${product.name}</td>
            <td>${product.color}</td>
            <td>${product.size}</td>
            <td>${product.quantity}</td>
            <td>${product.stock_quantity}</td>
            <td>${product.status}</td>
            <td>${product.unitPrice.toLocaleString("vi-VN", { style: "currency", currency: "VND" })}</td>
            <td>${product.total.toLocaleString("vi-VN", { style: "currency", currency: "VND" })}</td>
        </tr>
        `;
                tableBody.append(row);
            });

            console.log("Dữ liệu sau khi thêm vào bảng:", $("#product-table tbody").html());
        }


    });

});