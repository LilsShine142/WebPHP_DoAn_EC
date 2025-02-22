<?php

class OrderDeliveryStateGateway {
    private PDO $conn;

    public function __construct(Database $db) {
        $this->conn = $db->getConnection();
    }

    public function getAll(?int $limit, ?int $offset): array | false{
        if($limit && $offset){
            $sql = "SELECT * FROM order_delivery_states LIMIT :limit OFFSET :offset";
        } elseif($limit){
            $sql = "SELECT * FROM order_delivery_states LIMIT :limit";
        } elseif($offset){
            $sql = "SELECT * FROM order_delivery_states OFFSET :offset";
        } else {
            $sql = "SELECT * FROM order_delivery_states";
        }
        $stmt = $this->conn->prepare($sql);
        if($limit) $stmt->bindValue(":limit",$limit, PDO::PARAM_INT);
        if($offset) $stmt->bindValue(":offset",$offset, PDO::PARAM_INT);
        if($stmt->execute()){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function create(array $data): array | false {
         $sql = "INSERT INTO order_delivery_states (name) VALUE (:name)";
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(":name",$data["name"], PDO::PARAM_STR);
         $stmt->execute();
         $id = $this->conn->lastInsertId();
         return $this->get($id);
    }

    public function get(string $id): array | false {
        $sql = "SELECT * FROM order_delivery_states WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id, PDO::PARAM_INT);
        if($stmt->execute()){
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        return false;
    }

    

    public function update(array $current, array $new): array | false {
        $sql = "UPDATE order_delivery_states SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT); // 🔥 THÊM DÒNG NÀY
        $stmt->execute();
        return $this->get($current["id"]);
    }
    

    public function delete(string $id): bool {
        if(!$this->hasConstrain($id)) $sql = "DELETE FROM order_delivery_states WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function hasConstrain(int $id): bool {
        $sql = "SELECT EXISTS (
          SELECT 1 FROM orders WHERE delivery_state_id = :delivery_state_id
        )";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":delivery_state_id", $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }
}
?>
