<?php

class ProductCategoryController extends ErrorHandler
{
  public function __construct(private ProductCategoryGateway $gateway, private Auths $auths) {}

  public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void
  {
    // Lấy các tham số từ $_GET và loại bỏ khoảng trắng
    $name = isset($_GET['name']) ? trim($_GET['name']) : null;
    $from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : null;
    $to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : null;
    // Debug giá trị nhận được
    error_log("Received params: name=$name, from_date=$from_date, to_date=$to_date");
    // Kiểm tra nếu chỉ có id và không có tham số lọc nào khác
    if ($id && !$name && !$from_date && !$to_date) {
      $this->processResourceRequest($method, $id);
      return;
    }

    // Nếu có thêm tham số lọc, xử lý danh sách
    $this->processCollectionRequest($method, $limit, $offset, $id, $name, $from_date, $to_date);
  }


  private function processResourceRequest(string $method, int $id): void
  {
    $category = $this->gateway->get($id);
    if (!$category) {
      $this->sendErrorResponse(404, "Category with an id $id not found");
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode([
          "success" => true,
          "data" => $category
        ]);
        break;

      case "PUT":
        $this->auths->verifyAction("UPDATE_PRODUCT_CATEGORY");
        $data = (array) json_decode(file_get_contents("php://input"));
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          $this->sendErrorResponse(422, $errors);
          break;
        }
        $data = $this->gateway->update($category, $data);

        echo json_encode([
          "success" => true,
          "message" => "Category id $id updated",
          "data" => $data
        ]);
        break;

      case "DELETE":
        $this->auths->verifyAction("DELETE_PRODUCT_CATEGORY");
        $res = $this->gateway->delete($id);

        if (!$res) {
          echo json_encode([
            "success" => $res,
            "message" => "Category id $id can't be deleted because of constrain"
          ]);
          break;
        }

        echo json_encode([
          "success" => $res,
          "message" => "Category id $id was deleted"
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "only allow GET, PUT, DELETE method");
        header("Allow: GET, PUT, DELETE");
    }
  }

  private function processCollectionRequest(string $method, ?int $limit, ?int $offset, ?int $id = null, ?string $name = null, ?string $from_date = null, ?string $to_date = null): void
  {
    switch ($method) {
      case "GET":
        // Gọi chung một hàm lọc theo tất cả điều kiện
        $data = $this->gateway->getCategoryFilter($id, $name, $from_date, $to_date);

        if (!$data) {
          echo json_encode([
            "success" => false,
            "message" => "Data not found"
          ]);
          break;
        }

        echo json_encode([
          "success" => true,
          "length" => is_array($data) ? count($data) : 1,
          "data" => $data
        ]);
        break;

      case "POST":
        $this->auths->verifyAction("CREATE_PRODUCT_CATEGORY");
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
          "message" => "Product category created",
          "data" => $data
        ]);
        break;

      default:
        $this->sendErrorResponse(405, "Only allow GET, POST method");
        header("Allow: GET, POST");
    }
  }

  // private function processCollectionRequest(string $method, ?int $limit, ?int $offset, ?string $name = null): void
  // {
  //   switch ($method) {
  //     case "GET":
  //       if ($name) {
  //         $data = $this->gateway->getCategoryByName($name);
  //         if (!$data) {
  //           echo json_encode([
  //             "success" => false,
  //             "message" => "data not found"
  //           ]);
  //           break;
  //         }
  //         echo json_encode([
  //           "success" => true,
  //           "data" => $data
  //         ]);
  //       } else {
  //         $data = $this->gateway->getAll($limit, $offset);
  //         echo json_encode([
  //           "success" => true,
  //           "length" => count($data),
  //           "data" => $data
  //         ]);
  //       }
  //       break;

  //     case "POST":
  //       $this->auths->verifyAction("CREATE_PRODUCT_CATEGORY");
  //       $data = (array) json_decode(file_get_contents("php://input"));
  //       $errors = $this->getValidationErrors($data);
  //       if (!empty($errors)) {
  //         $this->sendErrorResponse(422, $errors);
  //         break;
  //       }
  //       $data = $this->gateway->create($data);

  //       http_response_code(201);
  //       echo json_encode([
  //         "success" => true,
  //         "message" => "Product category created",
  //         "data" => $data
  //       ]);
  //       break;

  //     default:
  //       $this->sendErrorResponse(405, "Only allow GET, POST method");
  //       header("Allow: GET, POST");
  //   }
  // }

  private function getValidationErrors(array $data, bool $new = true): array
  {
    $errors = [];

    if ($new) { //check all fields for new product
      if (empty($data["name"])) $errors[] = "name is required";
    } else { //check fields that exist
      if (array_key_exists("name", $data) && empty($data["name"])) $errors[] = "name is required";
    }

    return $errors;
  }
}
