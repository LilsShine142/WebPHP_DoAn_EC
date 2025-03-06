<?php

class UserRoleGateway
{
  private PDO $conn;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM user_roles LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM user_roles LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM user_roles LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM user_roles";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false
  {
    $sql = "INSERT INTO user_roles (user_id, role_id)
      VALUES (:user_id, :role_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":role_id", $data["role_id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($data["user_id"], $data["role_id"]);
  }

  public function get(int $user_id, ?int $role_id): array | false
  {
    $sql = $user_id && $role_id
      ? "SELECT * FROM user_roles WHERE user_id = :user_id AND role_id = :role_id"
      : "SELECT * FROM user_roles WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    if ($role_id) $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  //Hàm cập nhật role cho user
  public function update(int $user_id, int $role_id): bool
  {
    $sql = "UPDATE user_roles 
            SET role_id = :role_id 
            WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

    return $stmt->execute();
  }


  public function delete(int $user_id, int $role_id): bool
  {
    $sql = "DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  public function deleteByUserId(int $user_id): bool
  {
    $sql = "DELETE FROM user_roles WHERE user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount();
  }

  //LẤY TẤT CẢ USER CÓ ROLE_ID CỤ THỂ
  public function getUsersByRoleID(int $role_id): array | false
  {
    $sql = "SELECT * FROM user_roles WHERE role_id = :role_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // //LẤY TẤT CẢ USER CÓ ROLE_ID CỤ THỂ (CÓ TRUY VẤN CẢ NAME Ở BẢNG ROLES)
  // public function getUsersWithRoleInfoByRoleID(int $role_id): array | false {
  //   $sql = "SELECT u.id, u.full_name, u.email, r.name
  //           FROM users u
  //           JOIN user_roles ur ON u.id = ur.user_id
  //           JOIN roles r ON ur.role_id = r.id
  //           WHERE r.id = :role_id";

  //   $stmt = $this->conn->prepare($sql);
  //   $stmt->bindValue(":role_id", $role_id, PDO::PARAM_INT);
  //   $stmt->execute();

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
}
