<?php

class ProductGateway
{
  private PDO $conn;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM products LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM products LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM products LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM products";
    }
    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false
  {
    // $imageFileName = null;

    // // Nếu có ảnh Base64, lưu vào thư mục và lấy tên file
    // if (!empty($data["image_base64"])) {
    //   $imageFileName = $this->saveBase64Image($data["image_base64"]);
    //   if ($imageFileName === false) {
    //     return false; // Lưu ảnh thất bại
    //   } else {
    //     $data["image_name"] = "default.png";
    //   }
    // }

    // Câu lệnh SQL lưu sản phẩm vào database
    $sql = "INSERT INTO products (name, brand_id, model, category_id, description, stop_selling)
            VALUES (:name, :brand_id, :model, :category_id, :description, :stop_selling)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $stmt->bindValue(":brand_id", $data["brand_id"], PDO::PARAM_INT);
    $stmt->bindValue(":model", $data["model"], PDO::PARAM_STR);
    $stmt->bindValue(":category_id", $data["category_id"], PDO::PARAM_INT);
    $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
    $stmt->bindValue(":stop_selling", $data["stop_selling"] ?? false, PDO::PARAM_BOOL);
    //$stmt->bindValue(":image_name", $imageFileName, PDO::PARAM_STR);
    $stmt->execute();

    $id = $this->conn->lastInsertId();
    return $this->get($id);
  }


  public function get(int $id): array | false
  {
    $sql = "SELECT * FROM products WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false
  {
    // // Kiểm tra nếu có ảnh mới (dưới dạng Base64)
    // if (isset($new["image_base64"])) {
    //   // Giải mã Base64 và lưu ảnh
    //   $imageName = $this->saveBase64Image($new["image_base64"]);

    //   // Nếu lưu ảnh thất bại, trả về false
    //   if ($imageName === false) {
    //     return false;
    //   }
    // } else {
    //   // Nếu không có ảnh mới, giữ ảnh cũ
    //   $imageName = $current["image_name"];
    // }

    // Cập nhật thông tin sản phẩm
    $sql = "UPDATE products SET
        name = :name,
        brand_id = :brand_id,
        model = :model,
        category_id = :category_id,
        description = :description,
        stop_selling = :stop_selling
        -- image_name = :image_name  
        WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $stmt->bindValue(":brand_id", $new["brand_id"] ?? $current["brand_id"], PDO::PARAM_INT);
    $stmt->bindValue(":model", $new["model"] ?? $current["model"], PDO::PARAM_STR);
    $stmt->bindValue(":category_id", $new["category_id"] ?? $current["category_id"], PDO::PARAM_INT);
    $stmt->bindValue(":description", $new["description"] ?? $current["description"], PDO::PARAM_STR);
    $stmt->bindValue(":stop_selling", $new["stop_selling"] ?? $current["stop_selling"], PDO::PARAM_BOOL);
    //$stmt->bindValue(":image_name", $imageName, PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    // Trả về sản phẩm đã cập nhật
    return $this->get($current["id"]);
  }

  // Hàm lưu ảnh từ Base64
  private function saveBase64Image(string $base64Image): string | false
  {
    $uploadDir = __DIR__ . "/../../../backend/uploads/products/";
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true); // Tạo thư mục nếu chưa tồn tại
    }

    // Tạo tên file duy nhất
    $imageName = uniqid() . ".png"; // Mặc định lưu dưới dạng PNG
    $targetFile = $uploadDir . $imageName;

    // Giải mã Base64 và lưu file
    $imageData = base64_decode($base64Image);
    if (file_put_contents($targetFile, $imageData)) {
      return $imageName;
    } else {
      error_log("Lỗi khi lưu ảnh từ Base64.");
      return false;
    }
  }



  public function delete(int $id): bool
  {
    $sql = $this->hasConstrain($id)
      ? "UPDATE products SET stop_selling = true WHERE id = :id"
      : "DELETE FROM products WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool
  {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM product_variations WHERE product_id = :product_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":product_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }

  // Hàm lấy sản phẩm theo các điều kiện lọc
  public function getProductsByFiltersWithPagination(
    ?int $id,
    ?string $name,
    ?int $brand_id,
    ?int $category_id,
    ?int $stop_selling,
    int $limit,
    int $offset
  ): array {
    // Câu lệnh SQL cơ bản với SQL_CALC_FOUND_ROWS để tính tổng số bản ghi
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM products WHERE 1=1";
    $params = [];

    // Thêm các điều kiện lọc
    if ($id !== null) {
      $sql .= " AND id = :id";
      $params[':id'] = $id;
    }

    if ($name !== null) {
      $sql .= " AND name LIKE :name";
      $params[':name'] = '%' . $name . '%';
    }

    if ($brand_id !== null) {
      $sql .= " AND brand_id = :brand_id";
      $params[':brand_id'] = $brand_id;
    }

    if ($category_id !== null) {
      $sql .= " AND category_id = :category_id";
      $params[':category_id'] = $category_id;
    }

    if ($stop_selling !== null) {
      $sql .= " AND stop_selling = :stop_selling";
      $params[':stop_selling'] = $stop_selling;
    }

    // Thêm sắp xếp mặc định theo ID giảm dần (mới nhất lên đầu)
    $sql .= " ORDER BY id DESC";

    // Thêm phân trang
    $sql .= " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;

    // Chuẩn bị và thực thi câu lệnh SQL
    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      // Xác định kiểu dữ liệu cho binding
      $paramType = PDO::PARAM_STR;
      if (is_int($value)) {
        $paramType = PDO::PARAM_INT;
      } elseif (is_float($value)) {
        // PDO không có PARAM_FLOAT, dùng PARAM_STR
        $paramType = PDO::PARAM_STR;
      }

      $stmt->bindValue($key, $value, $paramType);
    }

    $stmt->execute();

    // Lấy dữ liệu sản phẩm
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tổng số bản ghi (kể cả khi không phân trang)
    $total = $this->conn->query("SELECT FOUND_ROWS()")->fetchColumn();

    return [
      'data' => $data,
      'total' => (int)$total
    ];
  }

  // Hàm đếm tổng sản phẩm
  public function countAll(): int
  {
    $sql = "SELECT COUNT(*) FROM products";
    $stmt = $this->conn->query($sql);
    return (int) $stmt->fetchColumn();
  }
}
