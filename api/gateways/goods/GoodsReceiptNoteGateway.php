<?php

class GoodsReceiptNoteGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM goods_receipt_notes LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM goods_receipt_notes LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM goods_receipt_notes OFFSET :offset";
    } else {
      $sql = "SELECT * FROM goods_receipt_notes";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO goods_receipt_notes (
      name,
      provider_id,
      staff_id,
      total_price_cents,
      quantity,
    ) VALUES (
      :name,
      :provider_id,
      :staff_id,
      :total_price_cents,
      :quantity,
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

  public function get(int $id): array | false {
    $sql = "SELECT * FROM goods_receipt_notes WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE goods_receipt_notes SET
      name = :name,
      provider_id = :provider_id,
      staff_id = :staff_id,
      total_price_cents = :total_price_cents,
      quantity = :quantity,
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

  public function delete(int $id): bool {
    if($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM goods_receipt_notes WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM product_instances WHERE goods_receipt_note_id = :goods_receipt_note_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":goods_receipt_note_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }
}