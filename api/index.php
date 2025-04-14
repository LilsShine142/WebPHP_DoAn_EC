<?php
//API v1.1

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
header("Access-Control-Allow-Origin: *"); // Allow your origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allowed request methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allowed headers

$method = $_SERVER["REQUEST_METHOD"];
if ($method === "OPTIONS") {
  http_response_code(200); // Respond with OK for preflight requests
  exit;
}
$uri = trim(parse_url($_SERVER["REQUEST_URI"])["path"], "/"); //web2-project/api/...
$uri_parts = explode("/", $uri);

$usr_email = $_SERVER["PHP_AUTH_USER"] ?? null;
$usr_pwd = $_SERVER["PHP_AUTH_PW"] ?? null;
$limit = isset($_GET["limit"]) ? abs((int) $_GET["limit"]) : null;
$offset = isset($_GET["offset"]) ? abs((int) $_GET["offset"]) : null;
$id = is_numeric(end($uri_parts)) ? (int) end($uri_parts) : null;

$db = new Database("localhost:3306", "webphp_ec", "root", "");
$auths = new Auths($db, $usr_email, $usr_pwd);

$uri = preg_replace('/\/[0-9]+$/', '', $uri); //AI gen: remove {id} if exist
switch (true) {
  case str_contains($uri, SOURCE_URI . "/products"):
    include_once "./routes/product.php";
    break;

  case str_contains($uri, SOURCE_URI . "/users"):
    include_once "./routes/user.php";
    break;

  case str_contains($uri, SOURCE_URI . "/carts"):
    include_once "./routes/cart.php";
    break;

  case str_contains($uri, SOURCE_URI . "/orders"):
    include_once "./routes/order.php";
    break;

  case str_contains($uri, SOURCE_URI . "/providers"):
    include_once "./routes/provider.php";
    break;

  case str_contains($uri, SOURCE_URI . "/goods_receipt_notes"):
    include_once "./routes/goodsReceiptNote.php";
    break;

  case str_contains($uri, SOURCE_URI . "/statistics"):
    include_once "./routes/statistics.php";
    break;

  case str_contains($uri, SOURCE_URI . "/feedbacks"):
    include_once "./routes/feedback.php";
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
