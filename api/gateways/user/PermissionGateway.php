<?php

class PermissionGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO permissions (action_name, action_code) VALUES (:action_name, :action_code)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":action_name", $data["action_name"], PDO::PARAM_STR);
    $stmt->bindValue(":action_code", $data["action_code"], PDO::PARAM_STR);
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM permissions LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM permissions LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM permissions OFFSET: offset";
    } else {
      $sql = "SELECT * FROM permissions";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get(int $id): array | false {
    $sql = "SELECT * FROM permissions WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update(array $current, array $new): array | false {
    $sql = "UPDATE permissions SET action_name = :action_name AND action_code = :action_code WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":action_name", $new["action_name"] ?? $current["action_name"], PDO::PARAM_STR);
    $stmt->bindValue(":action_code", $new["action_code"] ?? $current["action_code"], PDO::PARAM_STR);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }

  public function delete(int $id): bool {
    if($this->hasConstrain($id)) return false;

    $sql = "DELETE FROM permissions WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return true;
  }

  private function hasConstrain(int $id): bool {
    $sql = "SELECT EXISTS (SELECT 1 FROM role_permissions WHERE permission_id = :permission_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":permission_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }
}
