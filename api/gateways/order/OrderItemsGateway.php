<?php

class OrderItemsGateway {
    private PDO $conn;

    public function __construct(Database $db) {
        $this->conn = $db->getConnection();
    }

    public function getAll(?int $limit, ?int $offset): array | false{
        if($limit && $offset){
            $sql = "SELECT * FROM order_items LIMIT :limit OFFSET :offset";
        } elseif($limit){
            $sql = "SELECT * FROM order_items LIMIT :limit";
        } elseif($offset){
            $sql = "SELECT * FROM order_items LIMIT 100 OFFSET :offset";
        } else {
            $sql = "SELECT * FROM order_items";
        }
        $stmt = $this->conn->prepare($sql);
        if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        if($stmt->execute()){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function get(string $id): array|false {
        $sql = "SELECT * FROM order_items WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        if($stmt->execute()){
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        return false;
    }

    public function create(array $data): array | false {
        $sql = "INSERT INTO order_items (order_id, product_instance_sku, price_cents) VALUES (:order_id, :product_instance_sku, :price_cents)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":order_id", $data["order_id"], PDO::PARAM_INT);
        $stmt->bindValue(":product_instance_sku", $data["product_instance_sku"], PDO::PARAM_STR);
        $stmt->bindValue(":price_cents", $data["price_cents"], PDO::PARAM_INT);
        $stmt->execute();
        $id = $this->conn->lastInsertId();
        return $this->get($id);
    }

    public function update(array $current, array $new): array | false {
        $sql = "UPDATE order_items 
                SET order_id = :order_id, 
                    product_instance_sku = :product_instance_sku, 
                    price_cents = :price_cents 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
    
        $order_id = $new["order_id"] ?? $current["order_id"];
        $product_instance_sku = $new["product_instance_sku"] ?? $current["product_instance_sku"];
        $price_cents = isset($new["price_cents"]) ? $new["price_cents"] : $current["price_cents"]; // Kiểm tra `null`
    
        $stmt->bindValue(":order_id", $order_id, PDO::PARAM_INT);
        $stmt->bindValue(":product_instance_sku", $product_instance_sku, PDO::PARAM_STR);
        $stmt->bindValue(":price_cents", $price_cents, PDO::PARAM_INT); // Nếu null thì mặc định 0
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return $this->get($current["id"]);
        }
        return false;
    }
    

    public function delete(string $id): bool {
        $sql = "DELETE FROM order_items WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
