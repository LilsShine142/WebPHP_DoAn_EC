<?php

class ProductBrandGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO product_brands (name) VALUES (:name)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM product_brands LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM product_brands LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM product_brands LIMIT 18446744073709551615 OFFSET: offset";
    } else {
      $sql = "SELECT * FROM product_brands";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM product_brands WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE product_brands SET name = :name WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    if($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM product_brands WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return true;
  }

  private function hasConstrain(int $id): bool {
    $sql = "SELECT EXISTS (SELECT 1 FROM products WHERE brand_id = :brand_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":brand_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }


}