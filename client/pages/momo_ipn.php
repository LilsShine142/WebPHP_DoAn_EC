<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "../../api/config/constants.php";

// Khởi động session
session_start();

// Nhận dữ liệu từ MoMo (nếu gửi dưới dạng query string)
$data = $_REQUEST; // Hoặc $_GET, $_POST tùy vào phương thức gửi của MoMo

// Kiểm tra nếu không có dữ liệu
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
    exit();
}

// Lấy dữ liệu từ request;
$resultCode = $data['resultCode'] ?? '';
$orderId = $_SESSION["orderId"] ?? null;
$amount = $_SESSION["amount"] ?? null;
$paymentMethod = $_SESSION["paymentMethod"] ?? null;
$userId = $_SESSION["userId"] ?? null;
$totalCents = $_SESSION["totalCents"] ?? null;
$deliveryAddress = $_SESSION["deliveryAddress"] ?? null;
$orderDate = $_SESSION["orderDate"] ?? null;
$estimateReceivedDate = $_SESSION["estimateReceivedDate"] ?? null;
$receivedDate = $_SESSION["receivedDate"] ?? null;
$productVariationIds = $_SESSION["productVariationIds"] ?? [];
$quantities = $_SESSION["quantities"] ?? [];
$priceCents = $_SESSION["price"] ?? [];

// Nếu thanh toán thành công (resultCode = 0)
if ($resultCode == 0) {
    // Tạo đơn hàng
    $orderData = [
        "id" => $orderId,
        "user_id" => $userId,
        "total_cents" => $totalCents/100,
        "delivery_address" => $deliveryAddress,
        "order_date" => $orderDate,
        "delivery_state_id" => 1,
        "estimate_received_date" => $estimateReceivedDate,
        "received_date" => $receivedDate,
        "payment_method" => $paymentMethod
    ];
    $orderResponse = apiRequest(BASE_API_URL . "/api/orders", "POST", $orderData);

    if ($orderResponse && $orderResponse["success"]) {
        
        foreach ($productVariationIds as $index => $productVariationId) {
            // Xóa sản phẩm khỏi giỏ hàng
            apiRequest(BASE_API_URL . "/api/carts?user_id=$userId&product_variation_id=$productVariationId", "DELETE");
            
            // Lấy danh sách SKU cho từng sản phẩm
            $skuResponse = apiRequest(BASE_API_URL . "/api/products/instances?product_variation_id=$productVariationId&quantity={$quantities[$index]}", "GET");
            
            if ($skuResponse && $skuResponse["success"]) {
                foreach ($skuResponse["data"] as $instance) {
                    $sku = $instance["sku"];
                    $orderItemData = [
                        "order_id" => $orderId,
                        "product_instance_sku" => $sku,
                        "price_cents" => $priceCents[$index]
                    ];
                    apiRequest(BASE_API_URL . "/api/orders/items", "POST", $orderItemData);
                }
            }
        }
        
        // Xóa session
        unset($_SESSION["productVariationIds"], $_SESSION["quantities"], $_SESSION["orderId"]);
        // xóa session storage
        echo "<script>sessionStorage.removeItem('selected_products');</script>";
        echo "<script>alert('Đặt hàng thành công!'); </script>";
        echo "<script>window.location.href = '" . BASE_API_URL . "/client/index.php?content=pages/user-order.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi đặt hàng.');</script>";
    }
} else {
    // Nếu thanh toán thất bại
    echo json_encode(["status" => "error", "message" => "Thanh toán thất bại: $message"]);
}

/**
 * Gửi yêu cầu API
 */
function apiRequest($url, $method, $data = []) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method !== "GET" && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>
