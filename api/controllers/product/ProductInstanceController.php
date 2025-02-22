<?php

class ProductInstanceController extends ErrorHandler {

  public function __construct(private ProductInstanceGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?string $sku, ?int $limit, ?int $offset): void {
    if($sku) {
      $this->processResourceRequest($method, $sku);
      return;
    }
    
    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, string $sku): void {
    $product = $this->gateway->get($sku);
    if(!$product) {
      $this->sendErrorResponse(404, "Product instance with sku $sku not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $product
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_PRODUCT_INSTANCE");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($product, $data);

        echo json_encode([
          "success" => true,
          "message" => "Product instance sku $sku updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_PRODUCT_INSTANCE");
        $res = $this->gateway->delete($sku);

        echo json_encode([
          "success" => true,
          "message" => "Product instance sku $sku was deleted or is_sold = true if there is a constrain"
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
        $this->auths->verifyAction("CREATE_PRODUCT_INSTANCE");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $res = $this->gateway->create($data);
        if(!$res) {
          $this->sendErrorResponse(422, "product_variation_id = {$data["product_variation_id"]} not found");
          break;
        }

        http_response_code(201);
        echo json_encode([
          "success" => true,
          "message" => "Product instance created",
          "data" => $res
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  private function getValidationErrors(array $data, bool $new=true): array {
    $errors = [];

    if($new) { //check all fields for new product
      if(empty($data["product_variation_id"]) || !is_numeric($data["product_variation_id"])) $errors[] = "product_variation_id is required with integer value";
      if(empty($data["goods_receipt_note_id"]) || !is_numeric($data["goods_receipt_note_id"])) $errors[] = "goods_receipt_note_id is required with integer value";
    } else { //check fields that exist
      if(
        array_key_exists("product_variation_id", $data) &&
        (empty($data["product_variation_id"]) || !is_numeric($data["product_variation_id"]))
      ) $errors[] = "product_variation_id is empty or not an integer";
      if(
        array_key_exists("goods_receipt_note_id", $data) &&
        (empty($data["goods_receipt_note_id"]) || !is_numeric($data["goods_receipt_note_id"]))
      ) $errors[] = "goods_receipt_note_id is empty or not an integer";
    }

    if(array_key_exists("is_sold", $data) && !is_bool($data["is_sold"])) $errors[] = "is_sold must be a boolean value";

    return $errors;
  }

}