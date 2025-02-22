<?php

class CartController extends ErrorHandler {

  public function __construct(private CartGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $user_id, ?int $product_variation_id, ?int $limit, ?int $offset): void {
    if($user_id) {
      $this->processResourceRequest($method, $user_id, $product_variation_id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $user_id, ?int $product_variation_id): void {
    $carts = $this->gateway->get($user_id, $product_variation_id);
    if(!$carts) {
      $this->sendErrorResponse(404, "Cart with user_id $user_id and/or product_variation_id $product_variation_id not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "length" => count($carts),
          "data" => $carts
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_CART");
        if(!$product_variation_id) {
          $this->sendErrorResponse(422, "product_variation_id is required");
          break;
        }
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($carts[0], $data);

        echo json_encode([
          "success" => true,
          "message" => "Cart updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_CART");
        if(!$product_variation_id) {
          $this->sendErrorResponse(422, "product_variation_id is required");
          break;
        }
        $res = $this->gateway->delete($user_id, $product_variation_id);

        echo json_encode([
          "success" => $res,
          "message" => "Cart was deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
        header("Allow: GET, PUT, DELETE");
    }

  }

  private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void {
    switch($method) {
      case "GET":
        $data = $this->gateway->getAll($limit, $offset);

        echo json_encode([
          "success" => true,
          "length" => count($data),
          "data" => $data
        ]);
        break;

      case "POST":
        $this->auths->verifyAction("CREATE_CART");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->create($data);

        http_response_code(201);
        echo json_encode([
          "success" => true,
          "message" => "Cart created",
          "data" => $data
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  private function getValidationErrors(array $data, bool $new=true): array {
    $errors = [];

    if($new) { //check all fields for new cart
      if(empty($data["user_id"]) || !is_numeric($data["user_id"])) $errors[] = "user_id is required with integer value";
      if(empty($data["product_variation_id"]) || !is_numeric($data["product_variation_id"])) $errors[] = "product_variation_id is required with integer value";

    } else { //check fields that exist
      if(
        array_key_exists("user_id", $data) &&
        (empty($data["user_id"]) || !is_numeric($data["user_id"]))
      ) $errors[] = "user_id is empty or not an integer value";
      if(
        array_key_exists("product_variation_id", $data) &&
        (empty($data["product_variation_id"]) || !is_numeric($data["product_variation_id"]))
      ) $errors[] = "product_variation_id is empty or not an integer value";
    }

    if(
      array_key_exists("quantity", $data) &&
      (empty($data["quantity"]) || !is_numeric($data["quantity"]) || (int) $data["quantity"] < 1)
    ) $errors[] = "quantity is empty or not an integer value and must be greater than 0";

    return $errors;
  }
}
