<?php

class ProviderGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM providers LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM providers LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM providers LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM providers";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO providers (full_name, email, phone_number)
      VALUES (:full_name, :email, :phone_number)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":full_name", $data["full_name"], PDO::PARAM_STR);
    $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
    $stmt->bindValue(":phone_number", $data["phone_number"], PDO::PARAM_STR);
    $stmt->execute();

    $id = $this->conn->lastInsertId();
    return $this->get($id);
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM providers WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE providers SET
      full_name = :full_name,
      email = :email,
      phone_number = :phone_number
      WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":full_name", $new["full_name"] ?? $current["full_name"], PDO::PARAM_STR);
    $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
    $stmt->bindValue(":phone_number", $new["phone_number"] ?? $current["phone_number"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    if($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM providers WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM goods_receipt_notes WHERE provider_id = :provider_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":provider_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }
}
