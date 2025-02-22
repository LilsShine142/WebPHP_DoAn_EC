<?php

class RolePermissionController extends ErrorHandler {

  public function __construct(private RolePermissionGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $role_id, ?int $permission_id, ?int $limit, ?int $offset): void {
    if($role_id) {
      $this->processResourceRequest($method, $role_id, $permission_id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $role_id, ?int $permission_id): void {
    $role_permissions = $this->gateway->get($role_id, $permission_id);
    if(!$role_permissions) {
      $this->sendErrorResponse(404, "Role id $role_id with permission id $permission_id not found");
      return;
    }

    switch($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "length" => count($role_permissions),
          "data" => $role_permissions
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_ROLE_PERMISSION");
        if(!$permission_id) {
          $this->sendErrorResponse(422, "permission_id is required");
          break;
        }
        $res = $this->gateway->delete($role_id, $permission_id);

        echo json_encode([
          "success" => $res,
          "message" => "Role id $role_id and permission id $permission_id was deleted"
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
        $this->auths->verifyAction("CREATE_ROLE_PERMISSION");
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
          "message" => "Role's permission created",
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

    if($new) { //check all fields for new role
      if(empty($data["role_id"]) || !is_numeric($data["role_id"])) $errors[] = "role_id is required with integer value";
      if(empty($data["permission_id"]) || !is_numeric($data["permission_id"])) $errors[] = "permission_id is required with integer value";

    } else { //check fields that exist
      if(
        array_key_exists("role_id", $data) &&
        (empty($data["role_id"]) || !is_numeric($data["role_id"]))
      ) $errors[] = "role_id must be an integer";
      if(
        array_key_exists("permission_id", $data) &&
        (empty($data["permission_id"]) || !is_numeric($data["permission_id"]))
      ) $errors[] = "permission_id must be an integer";
    }

    return $errors;
  }
}