<?php

class UserAddressGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM user_addresses LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM user_addresses LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM user_addresses LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM user_addresses";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO user_addresses (
      user_id,
      street,
      apartment_number,
      ward,
      district,
      city_province,
      phone_number,
      is_default,
      name
    ) VALUES (
      :user_id,
      :street,
      :apartment_number,
      :ward,
      :district,
      :city_province,
      :phone_number,
      :is_default,
      :name
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":street", $data["street"], PDO::PARAM_STR);
    $stmt->bindValue(":apartment_number", $data["apartment_number"], PDO::PARAM_STR);
    $stmt->bindValue(":ward", $data["ward"], PDO::PARAM_STR);
    $stmt->bindValue(":district", $data["district"], PDO::PARAM_STR);
    $stmt->bindValue(":city_province", $data["city_province"], PDO::PARAM_STR);
    $stmt->bindValue(":phone_number", $data["phone_number"], PDO::PARAM_STR);
    $stmt->bindValue(":is_default", $data["is_default"] ?? false, PDO::PARAM_BOOL);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM user_addresses WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE user_addresses SET
      user_id = :user_id,
      street = :street,
      apartment_number = :apartment_number,
      ward = :ward,
      district = :district,
      city_province = :city_province,
      phone_number = :phone_number,
      is_default = :is_default,
      name = :name
      WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $new["user_id"] ?? $current["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":street", $new["street"] ?? $current["street"], PDO::PARAM_STR);
    $stmt->bindValue(":apartment_number", $new["apartment_number"] ?? $current["apartment_number"], PDO::PARAM_STR);
    $stmt->bindValue(":ward", $new["ward"] ?? $current["ward"], PDO::PARAM_STR);
    $stmt->bindValue(":district", $new["district"] ?? $current["district"], PDO::PARAM_STR);
    $stmt->bindValue(":city_province", $new["city_province"] ?? $current["city_province"], PDO::PARAM_STR);
    $stmt->bindValue(":phone_number", $new["phone_number"] ?? $current["phone_number"], PDO::PARAM_STR);
    $stmt->bindValue(":is_default", $new["is_default"] ?? $current["is_default"], PDO::PARAM_BOOL);
    $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    if($this->hasConstrain($id)) {
      return false;
    }

    $sql = "DELETE FROM user_addresses WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool { //can't delete default address
    $sql = "SELECT is_default FROM user_addresses WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return (bool) $data["is_default"];
  }
}