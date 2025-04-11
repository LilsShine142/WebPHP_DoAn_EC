<?php

class FeedbackGateway {
    private PDO $conn;

    public function __construct(Database $db) {
        $this->conn = $db->getConnection();
    }

    public function getAll(?int $limit, ?int $offset): array | false {
        if($limit && $offset) {
            $sql = "SELECT * FROM feedback LIMIT :limit OFFSET :offset";
        } elseif($limit) {
            $sql = "SELECT * FROM feedback LIMIT :limit";
        } elseif($offset) {
            $sql = "SELECT * FROM feedback LIMIT 18446744073709551615 OFFSET :offset";
        } else {
            $sql = "SELECT * FROM feedback";
        }

        $stmt = $this->conn->prepare($sql);
        if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array | false {
        $user_id = $data["user_id"];
        $product_variation_id = $data["product_variation_id"];
        $content = $data["content"];
        $rating = $data["rating"];

        $sql = "INSERT INTO feedback (user_id, product_variation_id, content, rating) VALUES (:user_id, :product_variation_id, :content, :rating)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->bindValue(":rating", $rating, PDO::PARAM_INT);
        $stmt->execute();

        return $this->get($this->conn->lastInsertId());
    }

    public function update(int $id, array $data): array | false {
        $sql = "UPDATE feedback SET
            user_id = :user_id,
            product_variation_id = :product_variation_id,
            content = :content,
            rating = :rating
            WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":product_variation_id", $data["product_variation_id"], PDO::PARAM_INT);
        $stmt->bindValue(":content", $data["content"], PDO::PARAM_STR);
        $stmt->bindValue(":rating", $data["rating"], PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->get($id);
    }

    public function get(int $id): array | false {
        $sql = "SELECT * FROM feedback WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM feedback WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getByProductId(int $product_variation_id): array | false {
        $sql = "SELECT * FROM feedback WHERE product_variation_id = :product_variation_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}