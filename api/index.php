<?php

declare(strict_types=1);

require_once "./config/settings.php";

$classMap = require_once "./classmap.php";
spl_autoload_register(function ($class) use ($classMap): void {
    if (isset($classMap[$class])) {
        require_once $classMap[$class];
    }
});

set_exception_handler("ErrorHandler::handleException");

header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER["REQUEST_METHOD"];
$uri = trim(parse_url($_SERVER["REQUEST_URI"])["path"], "/");
$uri_parts = explode("/", $uri);

$usr_email = $_SERVER["PHP_AUTH_USER"] ?? null;
$usr_pwd = $_SERVER["PHP_AUTH_PW"] ?? null;
$limit = isset($_GET["limit"]) ? abs((int) $_GET["limit"]) : null;
$offset = isset($_GET["offset"]) ? abs((int) $_GET["offset"]) : null;
$id = is_numeric(end($uri_parts)) ? (int) end($uri_parts) : null;

$db = new Database("localhost", "smartwatch_db", "root", "");
$auths = new Auths($db, $usr_email, $usr_pwd);

$uri = preg_replace('/\/[0-9]+$/', '', $uri); // Remove {id} if exists

switch (true) {
    case str_contains($uri, SOURCE_URI . "/products"):
        $controller = new ProductController(new ProductGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/product_os"):
        $controller = new ProductOSController(new ProductOSGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/product_brands"):
        $controller = new ProductBrandController(new ProductBrandGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/product_categories"):
        $controller = new ProductCategoryController(new ProductCategoryGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/product_instances"):
        $controller = new ProductInstanceController(new ProductInstanceGateway($db), $auths);
        $id = $_GET["sku"] ?? null;
        break;

    case str_contains($uri, SOURCE_URI . "/product_variations"):
        $controller = new ProductVariationController(new ProductVariationGateway($db), $auths, new Utils());
        break;

    case str_contains($uri, SOURCE_URI . "/orders"):
        $controller = new OrdersController(new OrdersGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/order_delivery_states"):
        $controller = new OrderDeliveryStateController(new OrderDeliveryStateGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/order_items"):
        $controller = new OrderItemsController(new OrderItemsGateway($db), $auths);
        break;

    case str_contains($uri, SOURCE_URI . "/users"):
        include_once "./routes/user.php";
        break;

    case str_contains($uri, SOURCE_URI . "/carts"):
        include_once "./routes/cart.php";
        break;

    case str_contains($uri, SOURCE_URI . "/providers"):
        include_once "./routes/provider.php";
        break;

    case str_contains($uri, SOURCE_URI . "/goods_receipt_notes"):
        include_once "./routes/goodsReceiptNote.php";
        break;

    default:
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Request not found!"]);
        exit;
}

try {
    if (isset($controller)) {
        $controller->processRequest($method, $id, $limit, $offset);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal Server Error", "error" => $e->getMessage()]);
}