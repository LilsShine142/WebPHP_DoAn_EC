<?php
class FinancialGateway
{
    private PDO $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->getConnection();
    }

    // Hàm lấy tổng chi phí và thông tin sản phẩm theo tháng hoặc ngày
    // public function getTotalExpense(?int $month = null, int $year)
    // {
    //     if (!$year) {
    //         return json_encode(["success" => false, "message" => "Vui lòng nhập năm."]);
    //     }

    //     $isMonthly = $month === null;
    //     $groupBy = $isMonthly ? "MONTH(o.order_date)" : "DAY(o.order_date)";
    //     $whereMonth = !$isMonthly ? "AND MONTH(o.order_date) = :month" : "";

    //     $sql = "SELECT 
    //             $groupBy AS period,
    //             o.order_date AS order_day,
    //             o.id AS order_id,
    //             p.id AS product_id,
    //             p.name AS product_name,
    //             pv.watch_size_mm,
    //             pv.watch_color,
    //             pi.sku AS product_instance_sku,
    //             pv.price_cents AS unit_price,
    //             COUNT(oi.product_instance_sku) AS total_quantity
    //         FROM order_items oi
    //         JOIN product_instances pi ON oi.product_instance_sku = pi.sku
    //         JOIN product_variations pv ON pi.product_variation_id = pv.id
    //         JOIN products p ON pv.product_id = p.id
    //         JOIN orders o ON oi.order_id = o.id
    //         WHERE YEAR(o.order_date) = :year
    //         $whereMonth
    //         GROUP BY period, o.id, p.id, p.name, pv.watch_size_mm, pv.watch_color, pi.sku, pv.price_cents
    //         ORDER BY period, o.id";

    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindValue(":year", $year, PDO::PARAM_INT);
    //     if (!$isMonthly) {
    //         $stmt->bindValue(":month", $month, PDO::PARAM_INT);
    //     }
    //     $stmt->execute();

    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     $formattedData = [];

    //     foreach ($result as $row) {
    //         $period = $row["period"];
    //         //$periodLabel = $isMonthly ? "Tháng $period" : "Ngày $period";
    //         $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    //         $periodLabel = $isMonthly ? $months[$period - 1] : "Day $period";
    //         $order_id = $row["order_id"];

    //         if (!isset($formattedData[$period])) {
    //             $formattedData[$period] = ["period" => $periodLabel, "orders" => []];
    //         }

    //         if (!isset($formattedData[$period]["orders"][$order_id])) {
    //             $formattedData[$period]["orders"][$order_id] = [
    //                 "order_id" => $order_id,
    //                 "total_expense" => 0,
    //                 "total_quantity" => 0,
    //                 "products" => []
    //             ];
    //         }

    //         $formattedData[$period]["orders"][$order_id]["total_expense"] += $row["unit_price"];
    //         $formattedData[$period]["orders"][$order_id]["total_quantity"] += $row["total_quantity"];
    //         $formattedData[$period]["orders"][$order_id]["products"][] = [
    //             "product_id" => $row["product_id"],
    //             "product_name" => $row["product_name"],
    //             "watch_size_mm" => $row["watch_size_mm"],
    //             "watch_color" => $row["watch_color"],
    //             "product_instance_sku" => $row["product_instance_sku"],
    //             "order_day" => $row["order_day"],
    //             "unit_price" => $row["unit_price"]
    //         ];
    //     }

    //     foreach ($formattedData as &$data) {
    //         $data["orders"] = array_values($data["orders"]);
    //     }

    //     return json_encode(["success" => true, "data" => array_values($formattedData)], JSON_PRETTY_PRINT);
    // }

    public function getTotalExpense(?int $month = null, int $year)
    {
        if (!$year) {
            return json_encode(["success" => false, "message" => "Vui lòng nhập năm."]);
        }

        $isMonthly = $month === null;
        $groupBy = $isMonthly ? "MONTH(o.order_date)" : "DAY(o.order_date)";
        $whereMonth = !$isMonthly ? "AND MONTH(o.order_date) = :month" : "";

        $sql = "SELECT 
            $groupBy AS period,
            o.order_date AS order_day,
            o.id AS order_id,
            p.id AS product_id,
            p.name AS product_name,
            pv.watch_size_mm,
            pv.watch_color,
            pv.stock_quantity,
            pv.stop_selling,
            pi.sku AS product_instance_sku,
            pv.price_cents AS unit_price,
            COUNT(oi.product_instance_sku) AS total_quantity
        FROM order_items oi
        JOIN product_instances pi ON oi.product_instance_sku = pi.sku
        JOIN product_variations pv ON pi.product_variation_id = pv.id
        JOIN products p ON pv.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE YEAR(o.order_date) = :year
        $whereMonth
        GROUP BY period, o.id, p.id, p.name, pv.watch_size_mm, pv.watch_color, 
                 pv.connectivity, pv.water_resistance_value, pv.water_resistance_unit, 
                 pv.battery_life_mah, pv.stop_selling, pi.sku, pv.price_cents
        ORDER BY period, o.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":year", $year, PDO::PARAM_INT);
        if (!$isMonthly) {
            $stmt->bindValue(":month", $month, PDO::PARAM_INT);
        }
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $formattedData = [];

        foreach ($result as $row) {
            $period = $row["period"];
            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $periodLabel = $isMonthly ? $months[$period - 1] : "Day $period";
            $order_id = $row["order_id"];

            if (!isset($formattedData[$period])) {
                $formattedData[$period] = ["period" => $periodLabel, "orders" => []];
            }

            if (!isset($formattedData[$period]["orders"][$order_id])) {
                $formattedData[$period]["orders"][$order_id] = [
                    "order_id" => $order_id,
                    "total_expense" => 0,
                    "total_quantity" => 0,
                    "products" => []
                ];
            }

            $formattedData[$period]["orders"][$order_id]["total_expense"] += $row["unit_price"] * $row["total_quantity"];
            $formattedData[$period]["orders"][$order_id]["total_quantity"] += $row["total_quantity"];
            $formattedData[$period]["orders"][$order_id]["products"][] = [
                "product_id" => $row["product_id"],
                "product_name" => $row["product_name"],
                "watch_size_mm" => $row["watch_size_mm"],
                "watch_color" => $row["watch_color"],
                "stock_quantity" => $row["stock_quantity"],
                "stop_selling" => $row["stop_selling"] ? "Ngừng bán" : "Đang bán",
                "product_instance_sku" => $row["product_instance_sku"],
                "order_day" => $row["order_day"],
                "unit_price" => $row["unit_price"],
                "total_quantity" => $row["total_quantity"],
                "total_cost" => $row["unit_price"] * $row["total_quantity"]
            ];
        }

        foreach ($formattedData as &$data) {
            $data["orders"] = array_values($data["orders"]);
        }

        return json_encode(["success" => true, "data" => array_values($formattedData)], JSON_PRETTY_PRINT);
    }


    // Hàm lấy tổng doanh thu theo tháng hoặc ngày
    public function getTotalRevenue(?int $month = null, ?int $year = null)
    {
        if ($year === null) {
            return json_encode([
                "success" => false,
                "message" => "Vui lòng chọn năm."
            ]);
        }

        if ($month === null) {
            // Nếu chỉ có năm -> Lấy tổng doanh thu theo từng tháng trong năm đó
            $sql = "SELECT MONTH(order_date) AS period, SUM(total_cents) AS revenue 
                FROM orders 
                WHERE delivery_state_id = 3 AND YEAR(order_date) = :year 
                GROUP BY MONTH(order_date) 
                ORDER BY period";
        } else {
            // Nếu có cả tháng và năm -> Lấy tổng doanh thu theo từng ngày trong tháng
            $sql = "SELECT DAY(order_date) AS period, SUM(total_cents) AS revenue 
                FROM orders 
                WHERE delivery_state_id = 3 
                AND YEAR(order_date) = :year 
                AND MONTH(order_date) = :month 
                GROUP BY DAY(order_date) 
                ORDER BY period";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":year", $year, PDO::PARAM_INT);
        if ($month !== null) {
            $stmt->bindValue(":month", $month, PDO::PARAM_INT);
        }
        $stmt->execute();
        $revenues = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Danh sách tên tháng
        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        // Chuyển đổi định dạng period
        $formattedRevenues = array_map(function ($row) use ($month, $months) {
            $period = $row["period"];
            $row["period"] = ($month === null) ? $months[$period - 1] : "Day $period";
            return $row;
        }, $revenues);

        return json_encode([
            "success" => true,
            "data" => $formattedRevenues
        ], JSON_UNESCAPED_UNICODE);
    }
}
