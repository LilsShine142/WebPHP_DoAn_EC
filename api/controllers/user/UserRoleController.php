<?php

class UserRoleController extends ErrorHandler {

  public function __construct(private UserRoleGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $user_id, ?int $role_id, ?int $limit, ?int $offset): void {
    if($user_id) {
      $this->processResourceRequest($method, $user_id, $role_id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $user_id, ?int $role_id): void {
    $user_roles = $this->gateway->get($user_id, $role_id);
    if(!$user_roles) {
      $this->sendErrorResponse(404, "User's id $user_id with role's id $role_id not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "length" => count($user_roles),
          "data" => $user_roles
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_USER_ROLE");
        if(!$role_id) {
          $this->sendErrorResponse(422, "role_id is required");
          break;
        }
        $res = $this->gateway->delete($user_id, $role_id);

        echo json_encode([
          "success" => $res,
          "message" => "User's id $user_id and role's id $role_id was deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, DELETE method");
        header("Allow: GET, DELETE");
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
        $this->auths->verifyAction("CREATE_USER_ROLE");
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
          "message" => "User's role created",
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

    if($new) { //check all fields for new user
      if(empty($data["user_id"]) || !is_numeric($data["user_id"])) $errors[] = "user_id is required with integer value";
      if(empty($data["role_id"]) || !is_numeric($data["role_id"])) $errors[] = "role_id is required with integer value";

    } else { //check fields that exist
      if(
        array_key_exists("user_id", $data) &&
        (empty($data["user_id"]) || !is_numeric($data["user_id"]))
      ) $errors[] = "user_id must be an integer";
      if(
        array_key_exists("role_id", $data) &&
        (empty($data["role_id"]) || !is_numeric($data["role_id"]))
      ) $errors[] = "role_id must be an integer";
    }

    return $errors;
  }
}