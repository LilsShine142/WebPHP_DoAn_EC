<?php

class ProductCategoryGateway
{
  private PDO $conn;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  public function create(array $data): array | false
  {
    $sql = "INSERT INTO product_categories (name) VALUES (:name)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM product_categories LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM product_categories LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM product_categories LIMIT 18446744073709551615 OFFSET: offset";
    } else {
      $sql = "SELECT * FROM product_categories";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $id): array | false
  {
    $sql = "SELECT * FROM product_categories WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Lấy danh sách sản phẩm theo tên danh mục
  public function getCategoryByName(string $name): array | false
  {
    $sql = "SELECT * FROM product_categories WHERE name = :name";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $name, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Hàm lọc danh mục sản phẩm
  public function getCategoryFilter($id, $name, $from_date, $to_date): array | false
  {
    $sql = "SELECT * FROM product_categories WHERE 1=1";

    if ($id) {
      $sql .= " AND id = :id";
    }
    if ($name) {
      $sql .= " AND name LIKE :name";
    }
    if ($from_date && $to_date) {
      $sql .= " AND created_at BETWEEN :from_date AND :to_date";
    }

    $stmt = $this->conn->prepare($sql);

    if ($id) {
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    }
    if ($name) {
      $stmt->bindValue(":name", "%$name%", PDO::PARAM_STR);
    }
    if ($from_date && $to_date) {
      $stmt->bindValue(":from_date", $from_date, PDO::PARAM_STR);
      $stmt->bindValue(":to_date", $to_date, PDO::PARAM_STR);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }



  public function update(array $current, array $new): array | false
  {
    $sql = "UPDATE product_categories SET name = :name WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool
  {
    if ($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM product_categories WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return true;
  }

  private function hasConstrain(int $id): bool
  {
    $sql = "SELECT EXISTS (SELECT 1 FROM products WHERE category_id = :category_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":category_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }
}
