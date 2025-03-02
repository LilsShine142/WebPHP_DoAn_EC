<?php

class GoodsReceiptNoteController extends ErrorHandler {
  private Utils $utils;

  public function __construct(private GoodsReceiptNoteGateway $gateway, private Auths $auths) {
    $this->utils = new Utils();
  }

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void {
    if($id) {
      $this->processResourceRequest($method, $id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $id): void {
    $receipt_note = $this->gateway->get($id);
    if(!$receipt_note) {
      $this->sendErrorResponse(404, "Receipt note with an id $id not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $receipt_note
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_GOODS_RECEIPT_NOTE");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if(!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($receipt_note, $data);

        echo json_encode([
          "success" => true,
          "message" => "Receipt note id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_GOODS_RECEIPT_NOTE");
        $res = $this->gateway->delete($id);

        if(!$res) {
          $this->sendErrorResponse(403, "Receipt note can't be deleted because of constrain");
          break;
        }

        echo json_encode([
          "success" => $res,
          "message" => "Receipt note id $id was deleted"
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
        $this->auths->verifyAction("CREATE_GOODS_RECEIPT_NOTE");
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
          "message" => "Receipt note created",
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

    if($new) { //check all fields for new receipt_note
      if(empty($data["name"])) $errors[] = "name is required";
      if(empty($data["provider_id"]) || !is_numeric($data["provider_id"])) $errors[] = "provider_id is required with integer value";
      if(empty($data["staff_id"]) || !is_numeric($data["staff_id"])) $errors[] = "staff_id is required with integer value";
      if(empty($data["total_price_cents"]) || !is_numeric($data["total_price_cents"])) $errors[] = "total_price_cents is required with integer value";
      if(empty($data["quantity"]) || !is_numeric($data["quantity"])) $errors[] = "quantity is required with integer value";

    } else { //check fields that exist
      if(array_key_exists("name", $data) && empty($data["name"])) $errors[] = "name is empty";
      if(
        array_key_exists("provider_id", $data) &&
        (empty($data["provider_id"]) || !is_numeric($data["provider_id"]))
      ) $errors[] = "provider_id is empty or not an integer value";
      if(
        array_key_exists("staff_id", $data) &&
        (empty($data["staff_id"]) || !is_numeric($data["staff_id"]))
      ) $errors[] = "staff_id is empty or not an integer value";
      if(
        array_key_exists("total_price_cents", $data) &&
        (empty($data["total_price_cents"]) || !is_numeric($data["total_price_cents"]))
      ) $errors[] = "total_price_cents is empty or not an integer value";
      if(
        array_key_exists("quantity", $data) &&
        (empty($data["quantity"]) || !is_numeric($data["quantity"]))
      ) $errors[] = "quantity is empty or not an integer value";
    }

    return $errors;
  }
}