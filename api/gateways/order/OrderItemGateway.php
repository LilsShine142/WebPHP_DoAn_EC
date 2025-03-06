<?php

class OrderItemGateway {

  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM order_items LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM order_items LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM order_items LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM order_items";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM order_items WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getByOrderId(int $orderId, ?int $limit, ?int $offset): array | false {
    $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
    
    if ($limit && $offset) {
        $sql .= " LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
        $sql .= " LIMIT :limit";
    } elseif ($offset) {
        $sql .= " LIMIT 18446744073709551615 OFFSET :offset";
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":order_id", $orderId, PDO::PARAM_INT);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO order_items (order_id, product_instance_sku, price_cents) VALUES (:order_id, :product_instance_sku, :price_cents)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":order_id", $data["order_id"], PDO::PARAM_INT);
    $stmt->bindValue(":product_instance_sku", $data["product_instance_sku"], PDO::PARAM_STR);
    $stmt->bindValue(":price_cents", $data["price_cents"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE order_items SET
      order_id = :order_id,
      product_instance_sku = :product_instance_sku,
      price_cents = :price_cents
      WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":order_id", $new["order_id"] ?? $current["order_id"], PDO::PARAM_INT);
    $stmt->bindValue(":product_instance_sku", $new["product_instance_sku"] ?? $current["product_instance_sku"], PDO::PARAM_STR);
    $stmt->bindValue(":price_cents", $new["price_cents"] ?? $current["price_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    $sql = "DELETE FROM order_items WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  public function deleteByOrderId(int $orderId): int {
    $sql = "DELETE FROM order_items WHERE order_id = :order_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":order_id", $orderId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount();
  }
}
