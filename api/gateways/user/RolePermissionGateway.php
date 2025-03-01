<?php

class RolePermissionGateway {
  private PDO $conn;

  public function __construct(Database $db) {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false {
    if($limit && $offset) {
      $sql = "SELECT * FROM role_permissions LIMIT :limit OFFSET :offset";
    } elseif($limit) {
      $sql = "SELECT * FROM role_permissions LIMIT :limit";
    } elseif($offset) {
      $sql = "SELECT * FROM role_permissions LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM role_permissions";
    }

    $stmt = $this->conn->prepare($sql);
    if($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false {
    $sql = "INSERT INTO role_permissions (role_id, permission_id)
      VALUES (:role_id, :permission_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":role_id", $data["role_id"], PDO::PARAM_INT);
    $stmt->bindValue(":permission_id", $data["permission_id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($data["role_id"], $data["permission_id"]);
  }

  public function get(int $role_id, ?int $permission_id): array | false {
    $sql = $role_id && $permission_id
      ? "SELECT * FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id"
      : "SELECT * FROM role_permissions WHERE role_id = :role_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    if($permission_id) $stmt->bindValue(":permission_id", $permission_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function delete(int $role_id, int $permission_id): bool {
    $sql = "DELETE FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $stmt->bindValue(":permission_id", $permission_id, PDO::PARAM_INT);
    return $stmt->execute();
  }
}