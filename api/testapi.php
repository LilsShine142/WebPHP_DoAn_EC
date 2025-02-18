<?php
// Đặt header để trả về JSON
header("Content-Type: application/json");
//file test api
function getGetAPI()
{
    return json_encode(["message" => "get API"]);
}

function PostAPI()
{
    // Lấy dữ liệu từ POST request
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $age = isset($_POST['age']) ? $_POST['age'] : null;

    // Tạo mảng dữ liệu
    $data = [
        "name" => $name,
        "age" => $age
    ];
    return json_encode(["message" => "post API", "data" => $data]);
}
// Kiểm tra phương thức HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo getGetAPI();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo PostAPI();
} else {
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
}
