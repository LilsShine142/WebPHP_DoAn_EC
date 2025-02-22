<?php

class ProductBrandController extends ErrorHandler {
  public function __construct(private ProductBrandGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void {
    if($id) {
      $this->processResourceRequest($method, $id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $id): void {
    $brand = $this->gateway->get($id);
    if(!$brand) {
      $this->sendErrorResponse(404, "Brand with an id $id not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $brand
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_PRODUCT_BRAND");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($brand, $data);

        echo json_encode([
          "success" => true,
          "message" => "Brand id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_PRODUCT_BRAND");
        $res = $this->gateway->delete($id);

        if(!$res) {
          echo json_encode([
            "success" => $res,
            "message" => "Brand id $id can't be deleted because of constrain"
          ]);
          break;
        }

        echo json_encode([
          "success" => $res,
          "message" => "Brand id $id was deleted"
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
        $this->auths->verifyAction("CREATE_PRODUCT_BRAND");
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
          "message" => "Product created",
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

    if($new) { //check all fields for new product
      if(empty($data["name"])) $errors[] = "name is required";
    } else { //check fields that exist
      if(array_key_exists("name", $data) && empty($data["name"])) $errors[] = "name is required";
    }

    return $errors;
  }
}
