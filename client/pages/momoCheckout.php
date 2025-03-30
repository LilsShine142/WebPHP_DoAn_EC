<?php
session_start();
header("Content-Type: application/json");

// Kiểm tra nếu có dữ liệu được gửi từ POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = $_POST["orderId"] ?? null;
    $amount = $_POST["amount"] ?? null;
    $paymentMethod = $_POST["paymentMethod"] ?? null;

    if ($orderId !== null && $amount !== null && $paymentMethod !== null) {
        // Lưu thông tin vào session để hiển thị trên trang
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

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán Momo</title>
</head>
<body>
    <h2>Thông tin đơn hàng</h2>
    <?php if ($orderId && $amount && $paymentMethod): ?>
        <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($orderId) ?></p>
        <p><strong>Số tiền:</strong> <?= htmlspecialchars($amount) ?> VND</p>
        <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($paymentMethod) ?></p>
    <?php else: ?>
        <p>Không có dữ liệu đơn hàng!</p>
    <?php endif; ?>
</body>
</html>
