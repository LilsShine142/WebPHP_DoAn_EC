<?php

class UserRoleController extends ErrorHandler
{

  public function __construct(private UserRoleGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $user_id, ?int $role_id, ?int $limit, ?int $offset): void
  {
    if ($user_id) {
      $this->processResourceRequest($method, $user_id, $role_id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset, $role_id);
  }

  private function processResourceRequest(string $method, int $user_id, ?int $role_id): void
  {
    $user_roles = $this->gateway->get($user_id, $role_id);
    if (!$user_roles) {
      $this->sendErrorResponse(404, "User with ID $user_id and Role ID $role_id not found");
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "length" => count($user_roles),
          "data" => $user_roles
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_USER_ROLE");

        // Đọc dữ liệu từ request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Kiểm tra xem role_id mới có được gửi lên không
        if (!isset($data["role_id"])) {
          $this->sendErrorResponse(422, "role_id is required");
          break;
        }

        // Cập nhật vai trò của người dùng
        $success = $this->gateway->update($user_id, $data["role_id"]);

        if ($success) {
          echo json_encode([
            "success" => true,
            "message" => "User's role updated successfully"
          ]);
        } else {
          $this->sendErrorResponse(500, "Failed to update user's role");
        }
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_USER_ROLE");

        if (!$role_id) {
          $this->sendErrorResponse(422, "role_id is required");
          break;
        }

        $this->gateway->delete($user_id, $role_id);

        echo json_encode([
          "success" => true,
          "message" => "User ID $user_id and Role ID $role_id were deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "Only GET, PUT, DELETE methods are allowed");
        header("Allow: GET, PUT, DELETE");
    }
  }


  private function processCollectionRequest(string $method, ?int $limit, ?int $offset, ?int $role_id = null): void
  {
    switch ($method) {
      case "GET":
        if ($role_id) {
          // Lấy danh sách user theo role_id cụ thể, kèm thông tin user và role
          $data = $this->gateway->getUsersByRoleID($role_id);
        } else {
          // Lấy tất cả user_roles
          $data = $this->gateway->getAll($limit, $offset);
        }

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
        if (!empty($errors)) {
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

  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new user
      if (empty($data["user_id"]) || !is_numeric($data["user_id"])) $errors[] = "user_id is required with integer value";
      if (empty($data["role_id"]) || !is_numeric($data["role_id"])) $errors[] = "role_id is required with integer value";
    } else { //check fields that exist
      if (
        array_key_exists("user_id", $data) &&
        (empty($data["user_id"]) || !is_numeric($data["user_id"]))
      ) $errors[] = "user_id must be an integer";
      if (
        array_key_exists("role_id", $data) &&
        (empty($data["role_id"]) || !is_numeric($data["role_id"]))
      ) $errors[] = "role_id must be an integer";
    }

    return $errors;
  }
}
