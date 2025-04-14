<?php

class ProductController extends ErrorHandler
{
  public function __construct(private ProductGateway $gateway, private Auths $auths) {}

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
    $product = $this->gateway->get($id);
    if (!$product) {
      $this->sendErrorResponse(404, "Product with an id $id not found");
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $product
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_PRODUCT");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($product, $data);

        echo json_encode([
          "success" => true,
          "message" => "Product id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_PRODUCT");
        $res = $this->gateway->delete($id);

        echo json_encode([
          "success" => $res,
          "message" => "Product id $id was deleted or stop_selling if there is a constrain"
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
          'name' => trim($_GET['name'] ?? '') ?: null,
          'brand_id' => isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null,
          'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
          'stop_selling' => isset($_GET['stop_selling']) ? (int)$_GET['stop_selling'] : null
        ];

        $this->handleProductList($filters, $limit ?? 0, $offset ?? 0);
        break;


      case "POST":
        $this->auths->verifyAction("CREATE_PRODUCT");
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
          "message" => "Product created",
          "data" => $data
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  private function handleProductList(array $filters, int $limit, int $offset): void
  {
    $hasFilters = !empty(array_filter($filters));

    if ($hasFilters) {
      $result = $this->gateway->getProductsByFiltersWithPagination(
        $filters['id'],
        $filters['name'],
        $filters['brand_id'],
        $filters['category_id'],
        $filters['stop_selling'],
        $limit,
        $offset
      );
      $data = $result['data'];
      $totalElements = $result['total'];
    } else {
      // Gọi getAll khi không có filter
      $data = $this->gateway->getAll($limit, $offset);
      $totalElements = $this->gateway->countAll();
    }

    echo json_encode([
      "success" => true,
      "totalElements" => $totalElements,
      "limit" => $limit,
      "offset" => $offset,
      "length" => count($data),
      "filters" => array_filter($filters), // Chỉ trả về các filter đang active
      "data" => $data
    ]);
  }

  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new product
      if (empty($data["name"])) $errors[] = "name is required";
      if (empty($data["brand_id"]) || !is_numeric($data["brand_id"])) $errors[] = "brand_id is required with integer value";
      if (empty($data["model"])) $errors[] = "model is required";
      if (empty($data["category_id"]) || !is_numeric($data["category_id"])) $errors[] = "category_id is required with integer value";
      if (empty($data["description"])) $errors[] = "description is required";
    } else { //check fields that exist
      if (array_key_exists("name", $data) && empty($data["name"])) $errors[] = "name is empty";
      if (array_key_exists("brand_id", $data) && (empty($data["brand_id"]) || !is_numeric($data["brand_id"]))) $errors[] = "brand_id is empty or not an integer";
      if (array_key_exists("model", $data) && empty($data["model"])) $errors[] = "model is empty";
      if (array_key_exists("category_id", $data) && (empty($data["category_id"]) || !is_numeric($data["category_id"]))) $errors[] = "category_id is empty or not an integer";
      if (array_key_exists("description", $data) && empty($data["description"])) $errors[] = "description is empty";
    }

    if (array_key_exists("stop_selling", $data) && !is_bool($data["stop_selling"])) $errors[] = "stop_selling must be a boolean value";

    return $errors;
  }
}
