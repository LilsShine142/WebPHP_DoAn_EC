<?php

class OrdersController extends ErrorHandler {
    public function __construct(private OrdersGateway $gateway, private Auths $auths) {}

    public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void {
        if($id) {
            $this->processResourceRequest($method, $id);
            return;
        }
        
        $this->processCollectionRequest($method, $limit, $offset);
    }

    private function processResourceRequest(string $method, int $id): void {
        $order = $this->gateway->get($id);
        if (!$order) {
            $this->sendErrorResponse(404, "Order with id $id not found");
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode(["success" => true, "data" => $order]);
                break;

            case "PUT":
                $this->auths->verifyAction("UPDATE_ORDER");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $updatedOrder = $this->gateway->update($order, $data);
                echo json_encode(["success" => true, "message" => "Order updated", "data" => $updatedOrder]);
                break;

            case "DELETE":
                $this->auths->verifyAction("DELETE_ORDER");
                $deleted = $this->gateway->delete($id);
                echo json_encode(["success" => $deleted, "message" => $deleted ? "Order deleted" : "Failed to delete order"]);
                break;

            default:
                $this->sendErrorResponse(405, "Only GET, PUT, DELETE methods are allowed");
                header("Allow: GET, PUT, DELETE");
        }
    }

    private function processCollectionRequest(string $method, ?int $limit = null, ?int $offset = null): void {
        switch ($method) {
            case "GET":
                $orders = $this->gateway->getAll($limit, $offset);
                echo json_encode(["success" => true, "length" => count($orders), "data" => $orders]);
                break;

            case "POST":
                $this->auths->verifyAction("CREATE_ORDER");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $createdOrder = $this->gateway->create($data);
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Order created", "data" => $createdOrder]);
                break;

            default:
                $this->sendErrorResponse(405, "Only GET, POST methods are allowed");
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(array $data, bool $newOrder = true): array {
        $errors = [];

        if ($newOrder) {
            if (empty($data["user_id"])) $errors[] = "user_id is required";
            if (empty($data["delivery_address"])) $errors[] = "delivery_address is required";
            if (empty($data["delivery_state_id"])) $errors[] = "delivery_state_id is required";
            if (empty($data["order_date"])) $errors[] = "order_date is required";
            if (empty($data["estimate_received_date"])) $errors[] = "estimate_received_date is required";
        }

        return $errors;
    }
}
