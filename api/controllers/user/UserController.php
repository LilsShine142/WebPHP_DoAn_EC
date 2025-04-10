<?php

class UserController extends ErrorHandler
{
  private Utils $utils;

  public function __construct(private UserGateway $gateway, private Auths $auths)
  {
    $this->utils = new Utils();
  }

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void
  {
    // Lấy các tham số từ $_GET và loại bỏ khoảng trắng
    $name = isset($_GET['full_name']) ? trim($_GET['full_name']) : null;
    $contact = isset($_GET['contact']) ? trim($_GET['contact']) : null;
    $from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : null;
    $to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : null;
    // Debug giá trị nhận được
    error_log("Received params: name=$name, contact=$contact, from_date=$from_date, to_date=$to_date");
    // Kiểm tra nếu chỉ có id và không có tham số lọc nào khác
    if ($id && !$name && !$contact && !$from_date && !$to_date) {
      $this->processResourceRequest($method, $id);
      return;
    }

    // Nếu có thêm tham số lọc, xử lý danh sách
    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $id): void
  {
    $user = $this->gateway->get($id);
    if (!$user) {
      $this->sendErrorResponse(404, "User with an id $id not found");
      return;
    }
    unset($user["password"]);

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $user
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_USER");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($user, $data);
        unset($data["password"]);

        echo json_encode([
          "success" => true,
          "message" => "User id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_USER");
        $this->gateway->delete($id);

        echo json_encode([
          "success" => true,
          "message" => "User id $id was deleted or all user's roles was deleted if there is a constrain"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
        header("Allow: GET, PUT, DELETE");
    }
  }

  private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void
  {
    switch ($method) {
      case "GET":
        $email = $_GET['email'] ?? null;
        $password = $_GET['password'] ?? null;

        // ✅ Kiểm tra đăng nhập
        if ($email && $password) {
          $user = $this->gateway->getByEmail($email);
          if (!$user) {
            $this->sendErrorResponse(404, "User with email $email not found");
            return;
          }

          // So sánh mật khẩu nhập vào với mật khẩu hash trong DB
          if (!password_verify($password, $user["password"])) {
            $this->sendErrorResponse(401, "Incorrect password");
            return;
          }

          unset($user["password"]); // Xóa password trước khi trả về

          echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "data" => $user
          ]);
          return;
        }

        $filters = [
          'id' => isset($_GET['id']) ? (int)$_GET['id'] : null,
          'name' => trim($_GET['full_name'] ?? '') ?: null,
          'contact' => trim($_GET['contact'] ?? '') ?: null,
          'from_date' => trim($_GET['from_date'] ?? '') ?: null,
          'to_date' => trim($_GET['to_date'] ?? '') ?: null,
          'type' => $_GET['type'] ?? null
        ];

        $this->handleUserList($filters, $limit ?? 10, $offset ?? 0);
        break;

      case "POST":
        // Lấy dữ liệu từ JSON body thay vì $_POST
        $data = json_decode(file_get_contents("php://input"), true);

        $newEmail = $data['email'] ?? null;
        $newPassword = $data['password'] ?? null;

        if ($newEmail && $newPassword) {
          $user = $this->gateway->getByEmail($newEmail);
          if ($user) {
            $this->sendErrorResponse(409, "User with email $newEmail already exists.");
            return;
          }

          $userData = [
            "email" => $newEmail,
            "password" => $newPassword
          ];

          $errors = $this->getValidationErrors($userData, true);
          if (!empty($errors)) {
            $this->sendErrorResponse(422, $errors);
            break;
          }

          $createdUser = $this->gateway->create($userData);
          unset($createdUser["password"]); // Không trả về password

          echo json_encode([
            "success" => true,
            "message" => "User created successfully",
            "data" => $createdUser
          ]);
          break;
        } else {
          $this->sendErrorResponse(400, "Email and password are required.");
        }
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  private function handleUserList(array $filters, int $limit, int $offset): void
  {
    $hasFilters = !empty(array_filter($filters));

    if ($hasFilters) {
      $result = $this->gateway->getByFiltersWithPagination(
        $filters['id'],
        $filters['name'],
        $filters['contact'],
        $filters['from_date'],
        $filters['to_date'],
        $filters['type'],
        $limit,
        $offset
      );
      $data = $result['data'];
      $totalElements = $result['total'];
    } else {
      $totalElements = $this->gateway->countAll();
      $data = $this->gateway->getAll($limit, $offset);
    }

    $dataFiltered = array_map(function ($user) {
      unset($user['password']);
      return $user;
    }, $data);

    echo json_encode([
      "success" => true,
      "totalElements" => $totalElements,
      "limit" => $limit,
      "offset" => $offset,
      "length" => count($dataFiltered),
      "data" => $dataFiltered
    ]);
  }

  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new user
      if (empty($data["email"]) || !$this->utils->isValidEmailRobust($data["email"])) $errors[] = "valid email is required";
      if (empty($data["password"]) || !$this->utils->isValidPassword($data["password"])) $errors[] = "valid password is required (hint: password must contain at least one letter and one number with min length = 8)";
    } else { //check fields that exist
      if (
        array_key_exists("email", $data) &&
        (empty($data["email"]) || !$this->utils->isValidEmail($data["email"]))
      ) $errors[] = "email is empty or not a valid email";
      if (
        array_key_exists("password", $data) &&
        (empty($data["password"]) || !$this->utils->isValidPassword($data["password"]))
      ) $errors[] = "password is empty or not a valid password (hint: password must contain at least one letter and one number with min length = 8)";
    }

    return $errors;
  }
}
