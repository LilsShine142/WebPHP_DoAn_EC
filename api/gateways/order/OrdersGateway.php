<?php

class OrdersGateway {
    private PDO $conn;

    public function __construct(Database $db) {
        $this->conn = $db->getConnection();
    }

    public function getAll(?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT * FROM orders";
        $params = [];

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(string $id): array|false {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array {
        $sql = "INSERT INTO orders (user_id, total_cents, delivery_address, delivery_state_id, order_date, estimate_received_date, received_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data["user_id"],
            $data["total_cents"],
            $data["delivery_address"],
            $data["delivery_state_id"],
            $data["order_date"],
            $data["estimate_received_date"],
            $data["received_date"] ?? null
        ]);
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
        $stmt->bindValue(":received_date", $new["received_date"] ?? $current["received_date"], PDO::PARAM_NULL);
        
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->execute();
    
        return $this->get($current["id"]);
    }    
    public function delete(string $id) {
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
