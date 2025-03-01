<?php

class OrderController extends ErrorHandler {

  private Utils $utils;
  public function __construct(private OrderGateway $gateway, private Auths $auths) {
    $this->utils = new Utils();
  }

  public function processRequest(string $method, ?string $id, ?int $limit, ?int $offset): void {
    if($id) {
      $this->processResourceRequest($method, $id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, string $id): void {
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
        $res = $this->gateway->update($order, $data);
        echo json_encode([
          "success" => true,
          "message" => "Order updated",
          "data" => $res
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_ORDER");
        $this->gateway->delete($id);

        echo json_encode([
          "success" => true,
          "message" => "Order id $id was deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "Only GET, PUT, DELETE methods are allowed");
        header("Allow: GET, PUT, DELETE");
    }
  }

  private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void {
    switch ($method) {
      case "GET":
        $orders = $this->gateway->getAll($limit, $offset);
        echo json_encode([
          "success" => true,
          "length" => count($orders),
          "data" => $orders
        ]);
        break;

      case "POST":
        $this->auths->verifyAction("CREATE_ORDER");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $res = $this->gateway->create($data);

        http_response_code(201);
        echo json_encode([
          "success" => true,
          "message" => "Order created",
          "data" => $res
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "Only GET, POST methods are allowed");
        header("Allow: GET, POST");
    }
  }

  private function getValidationErrors(array $data, bool $new = true): array {
    $errors = [];

    if($new) {
      if(empty($data["user_id"]) || !is_numeric($data["user_id"])) $errors[] = "user_id is required and must be an integer";
      if(empty($data["total_cents"]) || !is_numeric($data["total_cents"])) $errors[] = "total_cents is required and must be an integer";
      if(empty($data["delivery_address"])) $errors[] = "delivery_address is required";
      if(empty($data["delivery_state_id"]) || !is_numeric($data["delivery_state_id"])) $errors[] = "delivery_state_id is required and must be an integer";
      if(empty($data["estimate_received_date"]) || !$this->utils->isValidDateTimeFormat($data["estimate_received_date"])) $errors[] = "valid estimate_received_date is required";
      if(empty($data["received_date"]) || !$this->utils->isValidDateTimeFormat($data["received_date"])) $errors[] = "valid received_date is required";

    } else {
      if(
        array_key_exists("user_id", $data) &&
        (empty($data["user_id"]) || !is_numeric($data["user_id"]))
      ) $errors[] = "user_id is empty or not an integer";
      if(
        array_key_exists("total_cents", $data) &&
        (empty($data["total_cents"]) || !is_numeric($data["total_cents"]))
      ) $errors[] = "total_cents is empty or not an integer";
      if(
        array_key_exists("delivery_address", $data) &&
        (empty($data["delivery_address"]))
      ) $errors[] = "delivery_address is empty";
      if(
        array_key_exists("delivery_state_id", $data) &&
        (empty($data["delivery_state_id"]) || !is_numeric($data["delivery_state_id"]))
      ) $errors[] = "delivery_state_id is empty or not an integer";
      if(
        array_key_exists("estimate_received_date", $data) &&
        (empty($data["estimate_received_date"]) || !$this->utils->isValidDateTimeFormat($data["estimate_received_date"])
      )) $errors[] = "estimate_received_date is empty or not valid";
      if(
        array_key_exists("received_date", $data) &&
        (empty($data["received_date"]) || !$this->utils->isValidDateTimeFormat($data["received_date"]))
      ) $errors[] = "received_date is empty or not valid";
    }

    if(
      array_key_exists("order_date", $data) &&
      (empty($data["order_date"]) || !$this->utils->isValidDateTimeFormat($data["order_date"]))
    ) $errors[] = "valid order_date is required";

    return $errors;
  }
}
