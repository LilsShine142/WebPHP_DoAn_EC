<?php
session_start();
header("Content-Type: application/json");

// Kiểm tra nếu có dữ liệu được gửi từ POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = $_POST["orderId"] ?? null;
    $amount = $_POST["amount"] ?? null;
    $paymentMethod = $_POST["paymentMethod"] ?? null;

    if ($orderId !== null && $amount !== null && $paymentMethod !== null) {
        // Lưu thông tin vào session
        $_SESSION["orderId"] = $orderId;
        $_SESSION["amount"] = $amount;
        $_SESSION["paymentMethod"] = $paymentMethod;

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
        header("Location: " . $result["payUrl"]);
        exit();
    } else {
        echo "Lỗi khi tạo yêu cầu thanh toán! Chi tiết lỗi: <pre>";
        print_r($result);
        echo "</pre>";
    }  
}
?>

