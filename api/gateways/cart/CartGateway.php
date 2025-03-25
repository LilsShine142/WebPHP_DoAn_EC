<?php

class CartGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM carts LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM carts LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM carts LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM carts";
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

    $sql = $this->isExist($user_id, $product_variation_id) //exist -> accumulate quantity
      ? "UPDATE carts SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_variation_id = :product_variation_id"
      : "INSERT INTO carts (user_id, product_variation_id, quantity) VALUES (:user_id, :product_variation_id, :quantity)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
    $stmt->bindValue(":quantity", $data["quantity"] ?? 1, PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($user_id, $product_variation_id);
  }

  public function update(array $current, array $new): array | false {
    $user_id = $new["user_id"] ?? $current["user_id"];
    $product_variation_id = $new["product_variation_id"] ?? $current["product_variation_id"];
    $sql = "UPDATE carts SET
      user_id = :user_id,
      product_variation_id = :product_variation_id,
      quantity = :quantity
      WHERE user_id = :current_user_id AND product_variation_id = :current_product_variation_id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
    $stmt->bindValue(":quantity", $new["quantity"] ?? $current["quantity"], PDO::PARAM_INT);
    $stmt->bindValue(":current_user_id", $current["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":current_product_variation_id", $current["product_variation_id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($user_id, $product_variation_id);
  }

  public function get(int $user_id, ?int $product_variation_id): array | false {
    $sql = $user_id && $product_variation_id
      ? "SELECT * FROM carts WHERE user_id = :user_id AND product_variation_id = :product_variation_id"
      : "SELECT * FROM carts WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    if($product_variation_id) $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function delete(int $user_id, ?int $product_variation_id): bool {
    $sql = "DELETE FROM carts WHERE user_id = :user_id AND product_variation_id = :product_variation_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  private function isExist(int $user_id, int $product_variation_id): bool {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM carts WHERE user_id = :user_id AND product_variation_id = :product_variation_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindValue(":product_variation_id", $product_variation_id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }
}
