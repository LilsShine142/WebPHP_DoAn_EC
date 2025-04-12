<?php

class OrderGateway {

  private PDO $conn;
  private OrderItemGateway $orderItem;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
    $this->orderItem = new OrderItemGateway($db);
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    $sql = "SELECT * FROM orders";
    if ($limit !== null && $offset !== null) {
      $sql .= " LIMIT :limit OFFSET :offset";
    } elseif ($limit !== null) {
      $sql .= " LIMIT :limit";
    } elseif ($offset !== null) {
      $sql .= " LIMIT 18446744073709551615 OFFSET :offset";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit !== null) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset !== null) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $order_id): array | false {
    $sql = "SELECT 
    o.*,
    pi.product_variation_id, 
    pv.product_id, 
    pv.watch_size_mm, 
    pv.watch_color, 
    pv.image_name, 
    pr.name,
    COUNT(*) AS quantity,
    oi.price_cents
    FROM 
        orders o
    JOIN 
        order_items oi ON o.id = oi.order_id
    JOIN 
        product_instances pi ON oi.product_instance_sku = pi.sku
    JOIN 
        product_variations pv ON pi.product_variation_id = pv.id
    JOIN 
        products pr ON pv.product_id = pr.id
    WHERE 
        o.id = :order_id
    GROUP BY 
        pi.product_variation_id, 
        pv.product_id, 
        pv.watch_size_mm, 
        pv.watch_color, 
        pv.image_name, 
        pr.name;";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result ?: false;
  }

  public function getByUserId(int $user_id): array | false {
    $sql = "SELECT * FROM orders WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function create(array $data): array | false {
    $order_date = $data["order_date"] ?? null;
    $id_clause = isset($data["id"]) ? "id, " : "";  // Nếu có id, thêm cột id vào câu lệnh
    $id_value_clause = isset($data["id"]) ? ":id, " : "";  // Nếu có id, thêm giá trị bind

    $sql = "INSERT INTO orders (
              $id_clause
              user_id,
              total_cents,
              delivery_address,
              delivery_state_id,
              order_date,
              estimate_received_date,
              received_date,
              payment_method
            ) VALUES (
              $id_value_clause
              :user_id,
              :total_cents,
              :delivery_address,
              :delivery_state_id,
              :order_date,
              :estimate_received_date,
              :received_date,
              :payment_method
            )";

    $stmt = $this->conn->prepare($sql);

    if (isset($data["id"])) {
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
    }
    $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_cents", $data["total_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":delivery_address", $data["delivery_address"], PDO::PARAM_STR);
    $stmt->bindValue(":delivery_state_id", $data["delivery_state_id"], PDO::PARAM_INT);
    $stmt->bindValue(":order_date", $order_date, PDO::PARAM_STR);
    $stmt->bindValue(":estimate_received_date", $data["estimate_received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":received_date", $data["received_date"] ?? null, PDO::PARAM_STR);
    $stmt->bindValue(":payment_method", $data["payment_method"] ?? 'COD', PDO::PARAM_STR);
    $stmt->execute();
    
    return $this->getForUpdate($this->conn->lastInsertId());
  }

  public function getForUpdate(int $id): array | false {
    $sql = "SELECT * FROM orders WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE orders SET
      user_id = :user_id,
      total_cents = :total_cents,
      delivery_address = :delivery_address,
      delivery_state_id = :delivery_state_id,
      order_date = :order_date,
      estimate_received_date = :estimate_received_date,
      received_date = :received_date,
      payment_method = :payment_method
      WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $new["user_id"] ?? $current["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":total_cents", $new["total_cents"] ?? $current["total_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":delivery_address", $new["delivery_address"] ?? $current["delivery_address"], PDO::PARAM_STR);
    $stmt->bindValue(":delivery_state_id", $new["delivery_state_id"] ?? $current["delivery_state_id"], PDO::PARAM_INT);
    $stmt->bindValue(":order_date", $new["order_date"] ?? $current["order_date"], PDO::PARAM_STR);
    $stmt->bindValue(":estimate_received_date", $new["estimate_received_date"] ?? $current["estimate_received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":received_date", $new["received_date"] ?? $current["received_date"], PDO::PARAM_STR);
    $stmt->bindValue(":payment_method", $new["payment_method"] ?? $current["payment_method"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->getForUpdate($current["id"]);
  }

  public function delete(int $id): bool {
    $this->orderItem->deleteByOrderId($id); // delete order items first
    $sql = "DELETE FROM orders WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
  }
}
