<?php

class ProductVariationInstanceController extends ErrorHandler
{
    private Utils $utils;

    public function __construct(private ProductVariationInstanceGateway $gateway, private Auths $auths)
    {
        $this->utils = new Utils;
    }

    public function processRequest(string $method, ?string $action = null, ?int $id, ?int $limit, ?int $offset): void
    {

        if ($action === 'latest') {
            $this->processLatestRequest($method);
            return;
        }

        if ($id) {
            $this->processResourceRequest($method, $id);
            return;
        }

        $this->processCollectionRequest($method, $limit, $offset);
    }
    // Lấy id lớn nhất (id sau cùng) trong bảng product_variation
    private function processLatestRequest(string $method): void
    {
        if ($method !== "GET") {
            $this->sendErrorResponse(405, "Only GET method is allowed");
            header("Allow: GET");
            return;
        }

        $latestVariation = $this->gateway->getLatestId();

        if (!$latestVariation) {
            $this->sendErrorResponse(404, "No product variations found");
            return;
        }

        echo json_encode([
            "success" => true,
            "data" => $latestVariation
        ]);
    }

    private function processResourceRequest(string $method, int $id): void
    {
        $product = $this->gateway->get($id);
        if (!$product) {
            $this->sendErrorResponse(404, "Product variation with an id $id not found");
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode([
                    "success" => true,
                    "data" => $product
                ]);
                break;

            case "PUT":
                $this->auths->verifyAction("UPDATE_PRODUCT_VARIATION");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $data = $this->gateway->update($product, $data);

                echo json_encode([
                    "success" => true,
                    "message" => "Product variation id $id updated",
                    "data" => $data
                ]);
                break;

            case "DELETE":
                $this->auths->verifyAction("DELETE_PRODUCT_VARIATION");
                $this->gateway->delete($id);

                echo json_encode([
                    "success" => true,
                    "message" => "Product variation id $id was deleted or stop_selling if there is a constrain"
                ]);
                break;

            default:
                $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
                header("Allow: GET, PUT, DELETE");
        }
    }

    private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void
    {
        switch ($method) {
            case "GET":
                // Xử lý các tham số phân trang
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

                $filters = [
                    "id" => $_GET["id"] ?? null,
                    "product_id" => $_GET["product_id"] ?? null,
                    "price_cents_min" => $_GET["price_cents_min"] ?? null,
                    "price_cents_max" => $_GET["price_cents_max"] ?? null,
                    "os_id" => $_GET["os_id"] ?? null,
                    "stop_selling" => $_GET["stop_selling"] ?? null,
                ];
                // if ($filters) {
                $this->handleFiltersWithInstanceByPagination($filters, $limit, $offset);

                break;

            case "POST":
                $this->auths->verifyAction("CREATE_PRODUCT_VARIATION");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $data = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Product variation created",
                    "data" => $data
                ]);
                break;

            default:
                $this->sendErrorResponse(405, "only allow GET, POST method");
                header(header: "Allow: GET, POST");
        }
    }

    // Hàm lọc theo các trường và có thêm instance
    private function handleFiltersWithInstanceByPagination(array $filters, int $limit, int $offset): void
    {
        try {
            // Kiểm tra và validate các filter
            if (
                $filters['price_cents_min'] !== null && $filters['price_cents_max'] !== null
                && $filters['price_cents_min'] > $filters['price_cents_max']
            ) {
                throw new InvalidArgumentException("Giá tối thiểu không thể lớn hơn giá tối đa");
            }

            // Loại bỏ các filter null
            $activeFilters = array_filter($filters, fn($value) => $value !== null);

            // Gọi thẳng gateway bất kể có filter hay không
            $result = $this->gateway->getFlattenedVariationInstancesWithPagination(
                $filters['id'] ?? null,
                $filters['product_id'] ?? null,
                $filters['price_cents_min'] ?? null,
                $filters['price_cents_max'] ?? null,
                $filters['os_id'] ?? null,
                $filters['stop_selling'] ?? null,
                $limit,
                $offset
            );

            $response = [
                "success" => true,
                "meta" => [
                    "totalElements" => $result['total'],
                    "limit" => $limit,
                    "offset" => $offset,
                    "filtered" => count($result['data']),
                    "filters" => $activeFilters
                ],
                "data" => $result['data']
            ];

            http_response_code(200);
            header("Content-Type: application/json");
            echo json_encode($response, JSON_PRETTY_PRINT);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage(),
                "filters" => $filters
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Đã xảy ra lỗi khi xử lý yêu cầu",
                "detail" => $e->getMessage()
            ]);
        }
    }


    // Hàm lọc theo các trường
    private function handleFiltersByPagination($filters, $limit, $offset): void
    {
        $hasFilters = !empty(array_filter($filters));

        if ($hasFilters) {
            $result = $this->gateway->getVariationsByFiltersWithPagination(
                $filters['id'],
                $filters['product_id'],
                $filters['price_cents_min'],
                $filters['price_cents_max'],
                $filters['os_id'],
                $filters['stop_selling'],
                $limit,
                $offset
            );
            $data = $result['data'];
            $totalElements = $result['total'];
        } else {
            // Gọi getAll khi không có filter
            $data = $this->gateway->getAll($limit, $offset);
            $totalElements = $this->gateway->countAll();
        }

        echo json_encode([
            "success" => true,
            "totalElements" => $totalElements,
            "limit" => $limit,
            "offset" => $offset,
            "length" => count($data),
            "filters" => array_filter($filters), // Chỉ trả về các filter đang active
            "data" => $data
        ]);
    }

    // private function getValidationErrors(array $data, bool $new=true): array {
    //   $errors = [];

    //   if($new) { //check all fields for new product
    //     if(empty($data["product_id"]) || !is_numeric($data["product_id"])) $errors[] = "product_id is required";
    //     if(empty($data["watch_size_mm"]) || !is_numeric($data["watch_size_mm"])) $errors[] = "watch_size_mm is required with integer value";
    //     if(empty($data["watch_color"])) $errors[] = "watch_color is required";
    //     //stock_quantity field: auto calculate in mySQL
    //     if(empty($data("price_cents")) || !is_numeric($data["price_cents"])) $errors[] = "price_cents is required with integer value";
    //     if(empty($data("base_price_cents")) || !is_numeric($data["base_price_cents"])) $errors[] = "base_price_cents is required with integer value";
    //     if(empty($data["image_name"])) $errors[] = "image_name is required";
    //     if(empty($data["display_size_mm"]) || !is_numeric($data["display_size_mm"])) $errors[] = "display_size_mm is required with integer value";
    //     if(empty($data["display_type"])) $errors[] = "display_type is required";
    //     if(empty($data["resolution_h_px"]) || !is_numeric($data["resolution_h_px"])) $errors[] = "resolution_h_px is required with integer value";
    //     if(empty($data["resolution_w_px"]) || !is_numeric($data["resolution_w_px"])) $errors[] = "resolution_w_px is required with integer value";
    //     if(empty($data["ram_bytes"]) || !is_numeric($data["ram_bytes"])) $errors[] = "ram_bytes is required with integer value";
    //     if(empty($data["rom_bytes"]) || !is_numeric($data["rom_bytes"])) $errors[] = "rom_bytes is required with integer value";
    //     if(empty($data["os_id"]) || !is_numeric($data["os_id"])) $errors[] = "os_id is required with integer value";
    //     if(empty($data["connectivity"])) $errors[] = "connectivity is required";
    //     if(empty($data["battery_life_mah"]) || !is_numeric($data["battery_life_mah"])) $errors[] = "battery_life_mah is required with integer value";
    //     if(empty($data["water_resistance_value"]) || !is_numeric($data["water_resistance_value"])) $errors[] = "water_resistance_value is required with integer value";
    //     if(empty($data["water_resistance_unit"])) $errors[] = "water_resistance_unit is required";
    //     if(empty($data["sensor"])) $errors[] = "sensor is required";
    //     if(empty($data["case_material"])) $errors[] = "case_material is required";
    //     if(empty($data["band_material"])) $errors[] = "band_material is required";
    //     if(empty($data["band_size_mm"]) || !is_numeric($data["band_size_mm"])) $errors[] = "band_size_mm is required with integer value";
    //     if(empty($data["band_color"])) $errors[] = "band_color is required";
    //     if(empty($data["weight_milligrams"]) || !is_numeric($data["weight_milligrams"])) $errors[] = "weight_milligrams is required with integer value";
    //     if(empty($data["release_date"]) || !$this->utils->isValidDateTimeFormat($data["release_date"])) $errors[] = "release_date is required with time formatted YYYY-MM-DD HH:MI:SS"; //format: YYYY-MM-DD HH:MI:SS

    //   } else { //check fields that exist
    //     if(
    //       array_key_exists("product_id", $data) &&
    //       (empty($data["product_id"]) || !is_numeric($data["product_id"]))
    //     ) $errors[] = "product_id is empty or not an integer";
    //     if(
    //       array_key_exists("watch_size_mm", $data) &&
    //       (empty($data["watch_size_mm"]) || !is_numeric($data["watch_size_mm"]))
    //     ) $errors[] = "watch_size_mm is empty or not an integer";
    //     if(
    //       array_key_exists("watch_color", $data) &&
    //       (empty($data["watch_color"]))
    //     ) $errors[] = "watch_color is empty";
    //     if(
    //       array_key_exists("price_cents", $data["price_cents"]) &&
    //       (empty($data("price_cents")) || !is_numeric($data["price_cents"]))
    //     ) $errors[] = "price_cents is empty or not an integer value";
    //     if(
    //       array_key_exists("base_price_cents", $data["base_price_cents"]) &&
    //       (empty($data("base_price_cents")) || !is_numeric($data["base_price_cents"]))
    //     ) $errors[] = "base_price_cents is empty or not an integer value";
    //     if(
    //       array_key_exists("image_name", $data) &&
    //       (empty($data["image_name"]))
    //     ) $errors[] = "image_name is empty";
    //     if(
    //       array_key_exists("display_size_mm", $data) &&
    //       (empty($data["display_size_mm"]) || !is_numeric($data["display_size_mm"]))
    //     ) $errors[] = "display_size_mm is empty or not an integer";
    //     if(
    //       array_key_exists("display_type", $data) && empty($data["display_type"])
    //     ) $errors[] = "display_type is empty";
    //     if(
    //       array_key_exists("resolution_h_px", $data) &&
    //       (empty($data["resolution_h_px"]) || !is_numeric($data["resolution_h_px"]))
    //     ) $errors[] = "resolution_h_px is empty or not an integer";
    //     if(
    //       array_key_exists("resolution_w_px", $data) &&
    //       (empty($data["resolution_w_px"]) || !is_numeric($data["resolution_w_px"]))
    //     ) $errors[] = "resolution_w_px is empty or not an integer";
    //     if(
    //       array_key_exists("ram_bytes", $data) &&
    //       (empty($data["ram_bytes"]) || !is_numeric($data["ram_bytes"]))
    //     ) $errors[] = "ram_bytes is empty or not an integer";
    //     if(
    //       array_key_exists("rom_bytes", $data) &&
    //       (empty($data["rom_bytes"]) || !is_numeric($data["rom_bytes"]))
    //     ) $errors[] = "rom_bytes is empty or not an integer";
    //     if(
    //       array_key_exists("os_id", $data) &&
    //       (empty($data["os_id"]) || !is_numeric($data["os_id"]))
    //     ) $errors[] = "os_id is empty or not an integer";
    //     if(
    //       array_key_exists("connectivity", $data) &&
    //       (empty($data["connectivity"]))
    //     ) $errors[] = "connectivity is empty";
    //     if(
    //       array_key_exists("battery_life_mah", $data) &&
    //       (empty($data["battery_life_mah"]) || !is_numeric($data["battery_life_mah"]))
    //     ) $errors[] = "battery_life_mah is empty or not an integer";
    //     if(
    //       array_key_exists("water_resistance_value", $data) &&
    //       (empty($data["water_resistance_value"]) || !is_numeric($data["water_resistance_value"]))
    //     ) $errors[] = "water_resistance_value is empty or not an integer";
    //     if(
    //       array_key_exists("water_resistance_unit", $data) &&
    //       (empty($data["water_resistance_unit"]))
    //     ) $errors[] = "water_resistance_unit is empty";
    //     if(
    //       array_key_exists("sensor", $data) &&
    //       (empty($data["sensor"]))
    //     ) $errors[] = "sensor is empty";
    //     if(
    //       array_key_exists("case_material", $data) &&
    //       (empty($data["case_material"]))
    //     ) $errors[] = "case_material is empty";
    //     if(
    //       array_key_exists("band_material", $data) &&
    //       (empty($data["band_material"]))
    //     ) $errors[] = "band_material is empty";
    //     if(
    //       array_key_exists("band_size_mm", $data) &&
    //       (empty($data["band_size_mm"]) || !is_numeric($data["band_size_mm"]))
    //     ) $errors[] = "band_size_mm is empty or not an integer";
    //     if(
    //       array_key_exists("band_color", $data) &&
    //       (empty($data["band_color"]))
    //     ) $errors[] = "band_color is empty";
    //     if(
    //       array_key_exists("weight_milligrams", $data) &&
    //       (empty($data["weight_milligrams"]) || !is_numeric($data["weight_milligrams"]))
    //     ) $errors[] = "weight_milligrams is empty or not an integer";
    //     if(
    //       array_key_exists("release_date", $data) &&
    //       (empty($data["release_date"]) || !$this->utils->isValidDateTimeFormat($data["release_date"]))
    //     ) $errors[] = "release_date is empty or not right format (YYYY-MM-DD HH:MI:SS)"; //format: YYYY-MM-DD HH:MI:SS
    //   }

    //   if(array_key_exists("stop_selling", $data) && !is_bool($data["stop_selling"])) $errors[] = "stop_selling must be a boolean value";

    //   return $errors;
    // }


    private function getValidationErrors(array $data, bool $new = true): array
    {
        $errors = [];

        if ($new) { // Kiểm tra tất cả các trường khi thêm sản phẩm mới
            $requiredFields = [
                "product_id" => "integer",
                "watch_size_mm" => "integer",
                "watch_color" => "string",
                "price_cents" => "integer",
                "base_price_cents" => "integer",
                // "image_name" => "string",
                "display_size_mm" => "integer",
                "display_type" => "string",
                "resolution_h_px" => "integer",
                "resolution_w_px" => "integer",
                "ram_bytes" => "integer",
                "rom_bytes" => "integer",
                "os_id" => "integer",
                "connectivity" => "string",
                "battery_life_mah" => "integer",
                "water_resistance_value" => "integer",
                "water_resistance_unit" => "string",
                "sensor" => "string",
                "case_material" => "string",
                "band_material" => "string",
                "band_size_mm" => "integer",
                "band_color" => "string",
                "weight_milligrams" => "integer",
                "release_date" => "datetime"
            ];

            foreach ($requiredFields as $field => $type) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $errors[] = "$field is required";
                } elseif ($type === "integer" && !is_numeric($data[$field])) {
                    $errors[] = "$field must be an integer";
                } elseif ($type === "datetime" && !$this->utils->isValidDateTimeFormat($data[$field])) {
                    $errors[] = "$field must be in YYYY-MM-DD HH:MI:SS format";
                }
            }
        } else { // Kiểm tra các trường có tồn tại
            $validatableFields = [
                "product_id" => "integer",
                "watch_size_mm" => "integer",
                "watch_color" => "string",
                "price_cents" => "integer",
                "base_price_cents" => "integer",
                // "image_name" => "string",
                "display_size_mm" => "integer",
                "display_type" => "string",
                "resolution_h_px" => "integer",
                "resolution_w_px" => "integer",
                "ram_bytes" => "integer",
                "rom_bytes" => "integer",
                "os_id" => "integer",
                "connectivity" => "string",
                "battery_life_mah" => "integer",
                "water_resistance_value" => "integer",
                "water_resistance_unit" => "string",
                "sensor" => "string",
                "case_material" => "string",
                "band_material" => "string",
                "band_size_mm" => "integer",
                "band_color" => "string",
                "weight_milligrams" => "integer",
                "release_date" => "datetime"
            ];

            foreach ($validatableFields as $field => $type) {
                if (array_key_exists($field, $data)) {
                    if (empty($data[$field])) {
                        $errors[] = "$field is empty";
                    } elseif ($type === "integer" && !is_numeric($data[$field])) {
                        $errors[] = "$field must be an integer";
                    } elseif ($type === "datetime" && !$this->utils->isValidDateTimeFormat($data[$field])) {
                        $errors[] = "$field must be in YYYY-MM-DD HH:MI:SS format";
                    }
                }
            }
        }

        // Kiểm tra trường stop_selling nếu tồn tại
        if (array_key_exists("stop_selling", $data) && !is_bool($data["stop_selling"])) {
            $errors[] = "stop_selling must be a boolean value";
        }

        return $errors;
    }
}
