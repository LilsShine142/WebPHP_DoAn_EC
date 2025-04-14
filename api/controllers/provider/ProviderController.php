<?php

class ProviderController extends ErrorHandler
{
  private Utils $utils;

  public function __construct(private ProviderGateway $gateway, private Auths $auths)
  {
    $this->utils = new Utils();
  }

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void
  {
    if ($id) {
      $this->processResourceRequest($method, $id);
      return;
    }

    $this->processCollectionRequest($method, $limit, $offset);
  }

  private function processResourceRequest(string $method, int $id): void
  {
    $provider = $this->gateway->get($id);
    if (!$provider) {
      $this->sendErrorResponse(404, "Provider with an id $id not found");
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $provider
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_PROVIDER");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($provider, $data);

        echo json_encode([
          "success" => true,
          "message" => "Provider id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_PROVIDER");
        $res = $this->gateway->delete($id);

        if (!$res) {
          $this->sendErrorResponse(403, "Provider id $id can't be deleted because of constrain");
          break;
        }

        echo json_encode([
          "success" => true,
          "message" => "Provider id $id was deleted"
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
        $filters = [
          'id' => isset($_GET['id']) ? (int)$_GET['id'] : null,
          'name' => trim($_GET['full_name'] ?? '') ?: null,
          'contact' => trim($_GET['contact'] ?? '') ?: null,
          'from_date' => trim($_GET['from_date'] ?? '') ?: null,
          'to_date' => trim($_GET['to_date'] ?? '') ?: null
        ];

        $this->handleProviderList($filters, $limit ?? 10, $offset ?? 0);
        break;

      case "POST":
        $this->auths->verifyAction("CREATE_PROVIDER");
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
          "message" => "Provider created",
          "data" => $data
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  private function handleProviderList(array $filters, int $limit, int $offset): void
  {
    try {
      $hasFilters = !empty(array_filter($filters));

      if ($hasFilters) {
        $result = $this->gateway->getByFiltersWithPagination(
          $filters['id'],
          $filters['name'],
          $filters['contact'],
          $filters['from_date'],
          $filters['to_date'],
          $limit,
          $offset
        );
        $data = $result['data'] ?? [];
        $totalElements = $result['total'] ?? 0;
      } else {
        $result = $this->gateway->getAll($limit, $offset);

        // Xử lý cả 2 trường hợp: 
        // - Khi getAll trả về mảng trực tiếp
        // - Khi getAll trả về cấu trúc có data và pagination
        if (isset($result['data'])) {
          $data = $result['data'];
          $totalElements = $result['total'] ?? $this->gateway->countAll();
        } else {
          $data = is_array($result) ? $result : [];
          $totalElements = $this->gateway->countAll();
        }
      }

      // Đảm bảo $data luôn là mảng
      $data = is_array($data) ? $data : [];
      $length = count($data);

      echo json_encode([
        "success" => true,
        "totalElements" => $totalElements,
        "limit" => $limit,
        "offset" => $offset,
        "length" => $length,
        "data" => $data
      ]);
    } catch (Exception $e) {
      $this->sendErrorResponse(500, "Internal server error: " . $e->getMessage());
    }
  }

  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new provider
      if (empty($data["full_name"])) $errors[] = "full_name is required";
      if (empty($data["email"]) || !$this->utils->isValidEmailRobust($data["email"])) $errors[] = "valid email is required";
      if (empty($data["phone_number"]) || !$this->utils->isValidVNPhoneNumber($data["phone_number"])) $errors[] = "valid phone_number is required";
    } else { //check fields that exist
      if (array_key_exists("full_name", $data) && empty($data["full_name"])) $errors[] = "full_name is empty";
      if (
        array_key_exists("email", $data) &&
        (empty($data["email"]) || !$this->utils->isValidEmail($data["email"]))
      ) $errors[] = "email is empty or not a valid email";
      if (
        array_key_exists("phone_number", $data) &&
        (empty($data["phone_number"]) || !$this->utils->isValidVNPhoneNumber($data["phone_number"]))
      ) $errors[] = "phone_number is empty or not a valid phone number";
    }

    return $errors;
  }
}
