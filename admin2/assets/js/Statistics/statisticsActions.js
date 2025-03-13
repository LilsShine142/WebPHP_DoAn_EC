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

});