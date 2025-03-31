<?php
session_start();
header("Content-Type: application/json");

// Kiểm tra nếu có dữ liệu được gửi từ POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = $_POST["orderId"] ?? null;
    $amount = $_POST["amount"] ?? null;
    $paymentMethod = $_POST["paymentMethod"] ?? null;
    $userId = $_POST["userId"] ?? null;
    $totalCents = $_POST["total_cents"] ?? null;
    $deliveryAddress = $_POST["delivery_address"] ?? null;
    $orderDate = $_POST["order_date"] ?? null;
    $estimateReceivedDate = $_POST["estimate_received_date"] ?? null;
    $receivedDate = $_POST["received_date"] ?? null;
    $productVariationIds = $_POST["product_variation_id"] ?? [];
    $quantities = $_POST["quantity"] ?? [];

    if ($orderId !== null && $amount !== null && $paymentMethod !== null) {
        // Lưu thông tin vào session
        $_SESSION["orderId"] = $orderId;
        $_SESSION["amount"] = $amount;
        $_SESSION["paymentMethod"] = $paymentMethod;
        $_SESSION["userId"] = $userId;
        $_SESSION["totalCents"] = $totalCents;
        $_SESSION["deliveryAddress"] = $deliveryAddress;
        $_SESSION["orderDate"] = $orderDate;
        $_SESSION["estimateReceivedDate"] = $estimateReceivedDate;
        $_SESSION["receivedDate"] = $receivedDate;
        $_SESSION["productVariationIds"] = $productVariationIds;
        $_SESSION["quantities"] = $quantities;

        echo json_encode([
            "status" => "success",
            "message" => "Dữ liệu đơn hàng đã được lưu vào session."
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Thiếu dữ liệu đơn hàng!"
        ]);
        exit;
    }
}

// Hiển thị dữ liệu nếu có trong session
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

function apiRequest($url, $method, $data = []) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method !== "GET" && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

if ($paymentMethod === 'Momo') {
    // Cấu hình thông tin MoMo
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
    $partnerCode = 'MOMOBKUN20180529';
    $accessKey = 'klm05TvNBzhg7h7j';
    $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    
    $requestId = time() . "";
    $orderInfo = "Thanh toán đơn hàng #" . $orderId;
    $redirectUrl = "http://localhost:81/WebPHP_DoAn_EC/client";
    $ipnUrl = "http://localhost:81/WebPHP_DoAn_EC/client";
    $extraData = "";
    $requestType = "payWithATM";
    // Dữ liệu yêu cầu thanh toán
    $requestData = [
        "partnerCode" => $partnerCode,
        "accessKey" => $accessKey,
        "requestId" => $requestId,
        "amount" => $_SESSION["amount"],
        "orderId" => $_SESSION["orderId"],
        "orderInfo" => $orderInfo,
        "redirectUrl" => $redirectUrl,
        "ipnUrl" => $ipnUrl,
        "extraData" => $extraData,
        "requestType" => $requestType
    ];

    // Tạo chữ ký (signature)
    ksort($requestData);
    $rawHash = "";
    foreach ($requestData as $key => $value) {
        $rawHash .= "$key=$value&";
    }
    $rawHash = rtrim($rawHash, "&");
    $signature = hash_hmac("sha256", $rawHash, $secretKey);
    $requestData["signature"] = $signature;

    // Gửi request đến MoMo
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    if (isset($result["payUrl"])) {
        // Tạo đơn hàng
        $orderData = [
            "id" => $orderId,
            "user_id" => $userId,
            "total_cents" => $totalCents,
            "delivery_address" => $deliveryAddress,
            "order_date" => $orderDate,
            "delivery_state_id" => 1,
            "estimate_received_date" => $estimateReceivedDate,
            "received_date" => $receivedDate,
            "payment_method" => $paymentMethod
        ];
        $orderResponse = apiRequest("http://localhost:81/WebPHP_DoAn_EC/api/orders", "POST", $orderData);

        if ($orderResponse && $orderResponse["success"]) {
            
            foreach ($productVariationIds as $index => $productVariationId) {
                // Xóa sản phẩm khỏi giỏ hàng
                apiRequest("http://localhost:81/WebPHP_DoAn_EC/api/carts?user_id=$userId&product_variation_id=$productVariationId", "DELETE");
                
                // Lấy danh sách SKU cho từng sản phẩm
                $skuResponse = apiRequest("http://localhost:81/WebPHP_DoAn_EC/api/products/instances?product_variation_id=$productVariationId&quantity={$quantities[$index]}", "GET");
                
                if ($skuResponse && $skuResponse["success"]) {
                    foreach ($skuResponse["data"] as $instance) {
                        $sku = $instance["sku"];
                        $orderItemData = [
                            "order_id" => $orderId,
                            "product_instance_sku" => $sku,
                            "price_cents" => $totalCents
                        ];
                        apiRequest("http://localhost:81/WebPHP_DoAn_EC/api/orders/items", "POST", $orderItemData);
                    }
                }
            }
            
            // Xóa session
            unset($_SESSION["productVariationIds"], $_SESSION["quantities"], $_SESSION["orderId"]);
            
            echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'http://localhost:81/WebPHP_DoAn_EC/client/index.php?content=pages/shopping-cart.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi đặt hàng.');</script>";
        }

        header("Location: " . $result["payUrl"]);
        exit();
    } else {
        echo "Lỗi khi tạo yêu cầu thanh toán! Chi tiết lỗi: <pre>";
        print_r($result);
        echo "</pre>";
    }  
}
?>

