<?php

class OrderGateway {

  private PDO $conn;
  private OrderItemGateway $orderItem;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
    $this->orderItem = new OrderItemGateway($db);
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM orders LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM orders LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM orders LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM orders";
    }

    $sql = "SELECT * FROM orders";
    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit);
    if($offset) $stmt->bindValue(":offset", $offset);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM orders WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $order_date = $data["order_date"];
    $sql = $order_date
      ?
        "INSERT INTO orders (
          user_id,
          total_cents,
          delivery_address,
          delivery_state_id,
          order_date,
          estimate_received_date,
          received_date
        ) VALUES (
          :user_id,
          :total_cents,
          :delivery_address,
          :delivery_state_id,
          :order_date,
          :estimate_received_date,
          :received_date
        )"
      :
        "INSERT INTO orders (
          user_id,
          total_cents,
          delivery_address,
          delivery_state_id,
          estimate_received_date,
          received_date
        ) VALUES (
          :user_id,
          :total_cents,
          :delivery_address,
          :delivery_state_id,
          :estimate_received_date,
          :received_date
        )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_cents", $data["total_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":delivery_address", $data["delivery_address"], PDO::PARAM_STR);
    $stmt->bindValue(":delivery_state_id", $data["delivery_state_id"], PDO::PARAM_INT);
    if($order_date) $stmt->bindValue(":order_date", $data["order_date"], PDO::PARAM_STR);
    $stmt->bindValue(":estimate_received_date", $data["estimate_received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":received_date", $data["received_date"] ?? null, PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE orders SET
      user_id = :user_id,
      total_cents = :total_cents,
      delivery_address = :delivery_address,
      delivery_state_id = :delivery_state_id,
      order_date = :order_date,
      estimate_received_date = :estimate_received_date,
      received_date = :received_date
      WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $new["user_id"] ?? $current["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_cents", $new["total_cents"] ?? $current["total_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":delivery_address", $new["delivery_address"] ?? $current["delivery_address"], PDO::PARAM_STR);
    $stmt->bindValue(":delivery_state_id", $new["delivery_state_id"] ?? $current["delivery_state_id"], PDO::PARAM_INT);
    $stmt->bindValue(":order_date", $new["order_date"] ?? $current["order_date"], PDO::PARAM_STR);
    $stmt->bindValue(":estimate_received_date", $new["estimate_received_date"] ?? $current["estimate_received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":received_date", $new["received_date"] ?? $current["received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    $this->orderItem->deleteByOrderId($id); //delete order items first
    $sql = "DELETE FROM orders WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
  }
}
