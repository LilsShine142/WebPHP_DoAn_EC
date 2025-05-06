<?php

class ProductInstanceGateway
{
  private PDO $conn;
  private Utils $utils;
  private ProductGateway $product;
  private ProductVariationGateway $variation;
  private ProductCategoryGateway $category;
  private ProductBrandGateway $brand;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
    $this->utils = new Utils();
    $this->product = new ProductGateway($db);
    $this->variation = new ProductVariationGateway($db);
    $this->category = new ProductCategoryGateway($db);
    $this->brand = new ProductBrandGateway($db);
  }

  public function create(array $data): array | false
  {
    $variation = $this->variation->get($data["product_variation_id"]);
    if (!$variation) return false;
    $product = $this->product->get($variation["product_id"]);
    $category = $this->category->get($product["category_id"]);
    $brand = $this->brand->get($product["brand_id"]);
    $sku = $this->utils->genProductSKU(
      $category["name"],
      $brand["name"],
      $product["model"],
      $variation["watch_size_mm"],
      $variation["watch_color"],
      $variation["band_material"]
    );
    $sql = "INSERT INTO product_instances (
      sku,
      product_variation_id,
      goods_receipt_note_id,
      is_sold
    ) VALUES (
      :sku,
      :product_variation_id,
      :goods_receipt_note_id,
      :is_sold
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":sku", $sku, PDO::PARAM_STR);
    $stmt->bindValue(":product_variation_id", $data["product_variation_id"], PDO::PARAM_INT);
    $stmt->bindValue(":goods_receipt_note_id", $data["goods_receipt_note_id"], PDO::PARAM_INT);
    $stmt->bindValue(":is_sold", $data["is_sold"] ?? false, PDO::PARAM_BOOL);
    $stmt->execute();

    return $this->get($sku);
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM product_instances LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM product_instances LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM product_instances LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM product_instances";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(string $sku): array | false
  {
    $sql = "SELECT * FROM product_instances WHERE sku = :sku";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":sku", $sku, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // getByProductVariationId
  public function getByProductVariationIdAndQuantity($variationId, $quantity): array | false
  {
    $sql = "SELECT * FROM product_instances WHERE product_variation_id = :product_variation_id AND is_sold = false LIMIT :quantity";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":product_variation_id", $variationId, PDO::PARAM_INT);
    $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false
  {
    $sql = "UPDATE product_instances SET
      goods_receipt_note_id = :goods_receipt_note_id,
      is_sold = :is_sold
    WHERE sku = :sku";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":goods_receipt_note_id", $new["goods_receipt_note_id"] ?? $current["goods_receipt_note_id"], PDO::PARAM_INT);
    $stmt->bindValue(":is_sold", $new["is_sold"] ?? $current["is_sold"], PDO::PARAM_BOOL);
    $stmt->bindValue(":sku", $current["sku"], PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($current["sku"]);
  }

  public function delete(string $sku): bool
  {
    $sql = $this->hasConstrain($sku)
      ? "UPDATE product_instances SET is_sold = true WHERE sku = :sku"
      : "DELETE FROM product_instances WHERE sku = :sku";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":sku", $sku, PDO::PARAM_STR);
    return $stmt->execute();
  }

  private function hasConstrain(string $sku): bool
  {
    $sql = "SELECT EXISTS (SELECT 1 FROM order_items WHERE product_instance_sku = :sku)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":sku", $sku, PDO::PARAM_STR);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }

  // Thêm hàm lọc theo product_variation_id
  // public function getByProductVariationId(
  //   int $product_variation_id,
  //   // ?bool $is_sold = null,
  //   int $limit = 0,
  //   int $offset = 0
  // ): array {
  //   $sql = "SELECT * FROM product_instances WHERE product_variation_id = :product_variation_id";

  //   // if ($is_sold !== null) {
  //   //   $sql .= " AND is_sold = :is_sold";
  //   // }

  //   if ($limit > 0) {
  //     $sql .= " LIMIT :limit OFFSET :offset";
  //   }

  //   $stmt = $this->conn->prepare($sql);
  //   $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);

  //   // if ($is_sold !== null) {
  //   //   $stmt->bindValue(":is_sold", $is_sold, PDO::PARAM_BOOL);
  //   // }

  //   if ($limit > 0) {
  //     $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
  //     $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
  //   }

  //   $stmt->execute();

  //   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  //   return $result ?: [];
  // }

  public function getByProductVariationIdWithPagination(
    ?int $product_variation_id = null,
    ?int $goods_receipt_note_id = null,
    // ?bool $is_sold = null,
    ?int $limit = 20,
    ?int $offset = 0
  ): array {
    // Câu lệnh SQL cơ bản với SQL_CALC_FOUND_ROWS
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM product_instances WHERE 1=1";
    $params = [];

    // Thêm điều kiện lọc theo product_variation_id
    if ($product_variation_id !== null) {
      $sql .= " AND product_variation_id = :product_variation_id";
      $params[':product_variation_id'] = $product_variation_id;
    }

    // Thêm điều kiện lọc theo goods_receipt_note_id
    if ($goods_receipt_note_id !== null) {
      $sql .= " AND goods_receipt_note_id = :goods_receipt_note_id";
      $params[':goods_receipt_note_id'] = $goods_receipt_note_id;
    }

    // Thêm điều kiện lọc theo trạng thái bán
    // if ($is_sold !== null) {
    //   $sql .= " AND is_sold = :is_sold";
    //   $params[':is_sold'] = $is_sold;
    // }

    // Sắp xếp mặc định
    // $sql .= " ORDER BY id DESC";

    // Xử lý phân trang
    $sql .= " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;

    // Chuẩn bị và thực thi câu lệnh SQL
    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
      $paramType = PDO::PARAM_STR;
      if (is_int($value)) {
        $paramType = PDO::PARAM_INT;
      } elseif (is_bool($value)) {
        $paramType = PDO::PARAM_BOOL;
      }

      $stmt->bindValue($key, $value, $paramType);
    }

    $stmt->execute();

    // Lấy dữ liệu
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tổng số bản ghi
    $total = $this->conn->query("SELECT FOUND_ROWS()")->fetchColumn();

    return [
      'data' => $data,
      'total' => (int)$total,
      'limit' => $limit,
      'offset' => $offset
    ];
  }

  public function countAll(): int
  {
    $sql = "SELECT COUNT(*) FROM product_instances";
    $stmt = $this->conn->query($sql);
    return (int) $stmt->fetchColumn();
  }
}
