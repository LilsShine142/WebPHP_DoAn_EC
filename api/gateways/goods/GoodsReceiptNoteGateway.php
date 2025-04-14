<?php

class GoodsReceiptNoteGateway
{
  private PDO $conn;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM goods_receipt_notes LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM goods_receipt_notes LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM goods_receipt_notes LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM goods_receipt_notes";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false
  {
    $sql = "INSERT INTO goods_receipt_notes (
      name,
      provider_id,
      staff_id,
      total_price_cents,
      quantity
    ) VALUES (
      :name,
      :provider_id,
      :staff_id,
      :total_price_cents,
      :quantity
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $stmt->bindValue(":provider_id", $data["provider_id"], PDO::PARAM_INT);
    $stmt->bindValue(":staff_id", $data["staff_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_price_cents", $data["total_price_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":quantity", $data["quantity"], PDO::PARAM_INT);
    $stmt->execute();

    $id = $this->conn->lastInsertId();
    return $this->get($id);
  }

  public function get(int $id): array | false
  {
    $sql = "SELECT * FROM goods_receipt_notes WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false
  {
    $sql = "UPDATE goods_receipt_notes SET
      name = :name,
      provider_id = :provider_id,
      staff_id = :staff_id,
      total_price_cents = :total_price_cents,
      quantity = :quantity
      WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $stmt->bindValue(":provider_id", $new["provider_id"] ?? $current["provider_id"], PDO::PARAM_INT);
    $stmt->bindValue(":staff_id", $new["staff_id"] ?? $current["staff_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_price_cents", $new["total_price_cents"] ?? $current["total_price_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":quantity", $new["quantity"] ?? $current["quantity"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool
  {
    if ($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM goods_receipt_notes WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool
  {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM product_instances WHERE goods_receipt_note_id = :goods_receipt_note_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":goods_receipt_note_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }

  public function getByFiltersWithPagination(
    ?int $id,
    ?string $name,
    ?int $provider_id,
    ?int $staff_id,
    ?string $from_date,
    ?string $to_date,
    ?int $limit,
    ?int $offset
  ): array {
    $query = "SELECT SQL_CALC_FOUND_ROWS grn.*, 
                 p.full_name as provider_name,
                 s.full_name as staff_name
                 FROM goods_receipt_notes grn
                 LEFT JOIN providers p ON grn.provider_id = p.id
                 LEFT JOIN users s ON grn.staff_id = s.id
                 WHERE 1=1";

    $params = [];
    $paramTypes = [];

    // Thêm các điều kiện lọc
    if (!empty($id)) {
      $query .= " AND grn.id = :id";
      $params[':id'] = $id;
      $paramTypes[':id'] = PDO::PARAM_INT;
    }

    if (!empty($name)) {
      $query .= " AND grn.name LIKE :name";
      $params[':name'] = "%$name%";
      $paramTypes[':name'] = PDO::PARAM_STR;
    }

    if (!empty($provider_id)) {
      $query .= " AND grn.provider_id = :provider_id";
      $params[':provider_id'] = $provider_id;
      $paramTypes[':provider_id'] = PDO::PARAM_INT;
    }

    if (!empty($staff_id)) {
      $query .= " AND grn.staff_id = :staff_id";
      $params[':staff_id'] = $staff_id;
      $paramTypes[':staff_id'] = PDO::PARAM_INT;
    }

    if (!empty($from_date)) {
      $query .= " AND grn.created_at >= :from_date";
      $params[':from_date'] = $from_date;
      $paramTypes[':from_date'] = PDO::PARAM_STR;
    }

    if (!empty($to_date)) {
      $query .= " AND grn.created_at <= :to_date";
      $params[':to_date'] = $to_date;
      $paramTypes[':to_date'] = PDO::PARAM_STR;
    }

    // Thêm phân trang
    if ($limit !== null) {
      $query .= " LIMIT :limit";
      $params[':limit'] = $limit;
      $paramTypes[':limit'] = PDO::PARAM_INT;
    }

    if ($offset !== null && $limit !== null) {
      $query .= " OFFSET :offset";
      $params[':offset'] = $offset;
      $paramTypes[':offset'] = PDO::PARAM_INT;
    }

    $stmt = $this->conn->prepare($query);

    // Bind các tham số
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, $paramTypes[$key] ?? PDO::PARAM_STR);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tổng số bản ghi
    $stmt = $this->conn->prepare("SELECT FOUND_ROWS()");
    $stmt->execute();
    $total = (int) $stmt->fetchColumn();

    return [
      'data' => $data,
      'total' => $total
    ];
  }

  public function countAll(): int
  {
    $sql = "SELECT COUNT(*) FROM goods_receipt_notes";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
  }
}
