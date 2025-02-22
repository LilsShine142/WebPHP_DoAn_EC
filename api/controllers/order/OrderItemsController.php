<?php

class OrderItemsController extends ErrorHandler{

    public function __construct(private OrderItemsGateway $gateway, private Auths $auths) {
    }

    public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void {
        if ($id) {
            $this->processResourceRequest($method, $id);
            return;
        }
        $this->processCollectionRequest($method, $limit, $offset);
    }

    private function processResourceRequest(string $method, ?int $id): void {
        $order_item = $this->gateway->get($id);
        if (!$order_item) {
            $this->sendErrorResponse(404, "Order item with an id $id not found");
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode([
                    "success" => true,
                    "data" => $order_item
                ]);
                break;
            case "PUT":
                $this->auths->verifyAction("UPDATE_ORDER_ITEM");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $data = $this->gateway->update($order_item, $data);
                echo json_encode([
                    "success" => true,
                    "message" => "Order item id $id updated",
                    "data" => $data
                ]);
                break;
            case "DELETE":
                $this->auths->verifyAction("DELETE_ORDER_ITEM");
                $res = $this->gateway->delete($id);
                echo json_encode([
                    "success" => $res,
                    "message" => "Order item id $id was deleted"
                ]);
                break;
            default:
                $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
                header("Allow: GET, PUT, DELETE");
        }
    }

    private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void {
        switch ($method) {
            case "GET":
                $data = $this->gateway->getAll($limit, $offset);
                echo json_encode([
                    "success" => true,
                    "data" => $data
                ]);
                break;
            case "POST":
                $this->auths->verifyAction("CREATE_ORDER_ITEM");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $data = $this->gateway->create($data);
                echo json_encode([
                    "success" => true,
                    "message" => "Order item created",
                    "data" => $data
                ]);
                break;
            default:
                $this->sendErrorResponse(405, "only allow GET, POST method");
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(array $data, bool $new_order_item = true): array {
        $errors = [];
    
        if ($new_order_item) { // Kiểm tra tất cả các trường khi tạo mới order item
            if (!isset($data["order_id"]) || !is_int($data["order_id"])) {
                $errors[] = "order_id is required and must be an integer";
            }
            if (!isset($data["product_instance_sku"]) || !is_string($data["product_instance_sku"]) || empty($data["product_instance_sku"])) {
                $errors[] = "product_instance_sku is required and must be a non-empty string";
            }
            if (!isset($data["price_cents"]) || !is_int($data["price_cents"])) {
                $errors[] = "price_cents is required and must be an integer";
            }
        } else { // Kiểm tra chỉ những trường có trong request khi cập nhật
            if (array_key_exists("order_id", $data) && !is_int($data["order_id"])) {
                $errors[] = "order_id must be an integer";
            }
            if (array_key_exists("product_instance_sku", $data) && (!is_string($data["product_instance_sku"]) || empty($data["product_instance_sku"]))) {
                $errors[] = "product_instance_sku must be a non-empty string";
            }
            if (array_key_exists("price_cents", $data) && !is_int($data["price_cents"])) {
                $errors[] = "price_cents must be an integer";
            }
        }
    
        return $errors;
    }    
}
