<?php

class UserAddressController extends ErrorHandler
{

  public function __construct(private UserAddressGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void
  {
    $user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;   //Thêm user_id vào để lấy địa chỉ của user đó

    if ($id !== null) {
      $this->processResourceRequest($method, $id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset, $user_id);
  }


  private function processResourceRequest(string $method, int $id): void
  {
    $address = $this->gateway->get($id);
    if (!$address) {
      $this->sendErrorResponse(404, "Address with an id $id not found");
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $address
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_USER_ADDRESS");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($address, $data);

        echo json_encode([
          "success" => true,
          "message" => "Address id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_USER_ADDRESS");
        $res = $this->gateway->delete($id);

        if (!$res) {
          $this->sendErrorResponse(403, "Can't delete default address");
          break;
        }

        echo json_encode([
          "success" => true,
          "message" => "Address id $id was deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
        header("Allow: GET, PUT, DELETE");
    }
  }

  private function processCollectionRequest(string $method, ?int $limit, ?int $offset, ?int $user_id = null): void
  {
    switch ($method) {
      case "GET":
        if ($user_id) {
          $data = $this->gateway->getAddressByUserId($user_id);

          if (!$data) {
            echo json_encode([
              "success" => false,
              "message" => "No addresses found for user_id $user_id",
              "user_id" => $user_id
            ]);
            return;
          }

          echo json_encode([
            "success" => true,
            "data" => $data
          ]);
        } else {
          $data = $this->gateway->getAll($limit, $offset);
          echo json_encode([
            "success" => true,
            "length" => count($data),
            "data" => $data
          ]);
        }
        break;

      case "POST":
        $this->auths->verifyAction("CREATE_USER_ADDRESS");
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
          $this->sendErrorResponse(400, "Invalid JSON data");
          return;
        }

        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          return;
        }

        $newAddress = $this->gateway->create($data);

        http_response_code(201);
        echo json_encode([
          "success" => true,
          "message" => "Address created successfully",
          "data" => $newAddress
        ]);
        break;

      case "PUT":
        if($user_id) {
          $data = (array) json_decode(file_get_contents("php://input"));
          $errors = $this->getValidationErrors($data, false);
          if (!empty($errors)) {
            $this->sendErrorResponse(422, $errors);
            break;
          }
          $data = $this->gateway->updateAddressByUserId($user_id, $data);

          echo json_encode([
            "success" => true,
            "message" => "Address of user_id $user_id updated",
            "data" => $data
          ]);
        } else {
          $this->sendErrorResponse(400, "user_id is required to update address");
        }
        break;

      default:
        $this->sendErrorResponse(405, "Only allow GET, POST, PUT methods");
        header("Allow: GET, POST, PUT");
    }
  }


  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new user
      if (empty($data["user_id"]) || !is_numeric($data["user_id"])) $errors[] = "user_id is required with integer value";
      if (empty($data["street"])) $errors[] = "street is required";
      if (empty($data["apartment_number"])) $errors[] = "apartment_number is required";
      if (empty($data["ward"])) $errors[] = "ward is required";
      if (empty($data["district"])) $errors[] = "district is required";
      if (empty($data["city_province"])) $errors[] = "city_province is required";
      if (empty($data["phone_number"])) $errors[] = "phone_number is required";
      if (empty($data["name"])) $errors[] = "name is required";
    } else { //check fields which exist
      if (
        array_key_exists("user_id", $data) &&
        (empty($data["user_id"]) || !is_numeric($data["user_id"]))
      ) $errors[] = "user_id is empty or not an integer value";
      if (array_key_exists("street", $data) && empty($data["street"])) $errors[] = "street is empty";
      if (array_key_exists("apartment_number", $data) && empty($data["apartment_number"])) $errors[] = "apartment_number is empty";
      if (array_key_exists("ward", $data) && empty($data["ward"])) $errors[] = "ward is empty";
      if (array_key_exists("district", $data) && empty($data["district"])) $errors[] = "district is empty";
      if (array_key_exists("city_province", $data) && empty($data["city_province"])) $errors[] = "city_province is empty";
      if (array_key_exists("phone_number", $data) && empty($data["phone_number"])) $errors[] = "phone_number is empty";
      if (array_key_exists("name", $data) && empty($data["name"])) $errors[] = "name is empty";
    }

    if (array_key_exists("is_default", $data) && !is_bool($data["is_default"])) $errors[] = "is_default is empty or not a boolean value";

    return $errors;
  }
}
