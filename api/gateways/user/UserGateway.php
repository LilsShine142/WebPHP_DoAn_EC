<?php

class UserGateway
{
  private PDO $conn;
  private UserRoleGateway $userRole;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
    $this->userRole = new UserRoleGateway($db);
  }

  public function getByEmail(string $email): ?array
  {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM users LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM users LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM users LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM users";
    }

    $stmt = $this->conn->prepare($sql);
    if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create(array $data): array | false
  {
    $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
    $stmt->bindValue(":password", password_hash($data["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
    $stmt->execute();

    $id = $this->conn->lastInsertId();
    return $this->get($id);
  }

  public function get(int $id): array | false
  {
    $sql = "SELECT * FROM users WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Hàm lọc user
  // public function getByFilters($id, $name, $contact, $from_date, $to_date, $type): array
  // {
  //   $query = "SELECT u.*, r.name as role_name 
  //             FROM users u
  //             LEFT JOIN user_roles ur ON u.id = ur.user_id
  //             LEFT JOIN roles r ON ur.role_id = r.id
  //             WHERE 1=1";
  //   $params = [];

  //   if (!empty($id)) {
  //     $query .= " AND u.id = ?";
  //     $params[] = $id;
  //   }
  //   if (!empty($name)) {
  //     $query .= " AND u.full_name LIKE ?";
  //     $params[] = "%{$name}%";
  //   }
  //   if (!empty($contact)) {
  //     $query .= " AND (u.email LIKE ? OR u.phone_number LIKE ?)";
  //     $params[] = "%{$contact}%";
  //     $params[] = "%{$contact}%";
  //   }
  //   if (!empty($from_date)) {
  //     $query .= " AND u.created_at >= ?";
  //     $params[] = $from_date;
  //   }
  //   if (!empty($to_date)) {
  //     $query .= " AND u.created_at <= ?";
  //     $params[] = $to_date;
  //   }
  //   // Lọc theo loại người dùng (Nhân viên / Khách hàng)
  //   if (!empty($type)) {
  //     if ($type === "employee") {
  //       $query .= " AND ur.role_id NOT IN (2)";  // Loại bỏ khách hàng
  //     } elseif ($type === "customer") {
  //       $query .= " AND ur.role_id = 2";  // Chỉ lấy khách hàng
  //     }
  //   }
  //   $stmt = $this->conn->prepare($query);
  //   $stmt->execute($params);

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
  public function getByFilters($id, $name, $contact, $from_date, $to_date, $type): array
  {
    $query = "SELECT u.*, GROUP_CONCAT(DISTINCT r.name SEPARATOR ', ') as role_names
              FROM users u
              LEFT JOIN user_roles ur ON u.id = ur.user_id
              LEFT JOIN roles r ON ur.role_id = r.id
              WHERE 1=1";
    $params = [];

    if (!empty($id)) {
      $query .= " AND u.id = ?";
      $params[] = $id;
    }

    if (!empty($name)) {
      $query .= " AND u.full_name LIKE ?";
      $params[] = "%{$name}%";
    }

    if (!empty($contact)) {
      $query .= " AND (u.email LIKE ? OR u.phone_number LIKE ?)";
      $params[] = "%{$contact}%";
      $params[] = "%{$contact}%";
    }

    if (!empty($from_date)) {
      $query .= " AND u.created_at >= ?";
      $params[] = $from_date;
    }

    if (!empty($to_date)) {
      $query .= " AND u.created_at <= ?";
      $params[] = $to_date;
    }

    if (!empty($type)) {
      if ($type === "employee") {
        $query .= " AND ur.role_id IS NOT NULL AND ur.role_id <> 2";
      } elseif ($type === "customer") {
        $query .= " AND ur.role_id = 2";
      }
    }

    $query .= " GROUP BY u.id";

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function update(array $current, array $new): array | false
  {
    // Khởi tạo câu lệnh SQL động
    $sql = "UPDATE users SET
      full_name = :full_name,
      email = :email,
      phone_number = :phone_number";

    // Chỉ thêm cột password nếu có mật khẩu mới
    if (!empty($new["password"])) {
      $sql .= ", password = :password";
    }

    $sql .= " WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":full_name", $new["full_name"] ?? $current["full_name"], PDO::PARAM_STR);
    $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
    $stmt->bindValue(":phone_number", $new["phone_number"] ?? $current["phone_number"], PDO::PARAM_STR);

    // Chỉ bind password nếu có mật khẩu mới
    if (!empty($new["password"])) {
      $stmt->bindValue(":password", password_hash($new["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
    }

    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }


  public function delete(int $id): bool
  {
    if ($this->hasConstrain($id)) { //delete all user roles instead
      return $this->userRole->deleteByUserId($id);
    }

    $sql = "DELETE FROM users WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  private function hasConstrain(int $id): bool
  {
    $sql = "SELECT EXISTS (
      SELECT 1 FROM user_addresses WHERE user_id = :user_id
      UNION
      SELECT 1 FROM user_roles WHERE user_id = :user_id
      UNION
      SELECT 1 FROM carts WHERE user_id = :user_id
      UNION
      SELECT 1 FROM orders WHERE user_id = :user_id
      UNION
      SELECT 1 FROM goods_receipt_notes WHERE staff_id = :user_id
    )";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return (bool) $stmt->fetchColumn();
  }

  /**
   * Đếm tổng số employee (không phải customer)
   */
  public function countEmployees(): int
  {
    $sql = "SELECT COUNT(DISTINCT u.id) 
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                WHERE ur.role_id NOT IN (2)"; // Loại bỏ role customer (2)

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
  }

  /**
   * Đếm tổng số customer
   */
  public function countCustomers(): int
  {
    $sql = "SELECT COUNT(DISTINCT u.id) 
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                WHERE ur.role_id = 2"; // Chỉ lấy role customer (2)

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
  }

  public function getByFiltersWithPagination(
    ?int $id,
    ?string $name,
    ?string $contact,
    ?string $from_date,
    ?string $to_date,
    ?string $type,
    ?int $limit,
    ?int $offset
  ): array {
    // Xây dựng query cơ bản
    $query = "SELECT SQL_CALC_FOUND_ROWS u.*, r.name as role_name 
              FROM users u
              LEFT JOIN user_roles ur ON u.id = ur.user_id
              LEFT JOIN roles r ON ur.role_id = r.id";

    $conditions = [];
    $params = [];
    $paramTypes = [];

    // Thêm điều kiện lọc
    if (!empty($id)) {
      $conditions[] = "u.id = ?";
      $params[] = $id;
      $paramTypes[] = PDO::PARAM_INT;
    }

    if (!empty($name)) {
      $conditions[] = "u.full_name LIKE ?";
      $params[] = "%$name%";
      $paramTypes[] = PDO::PARAM_STR;
    }

    if (!empty($contact)) {
      $conditions[] = "(u.email LIKE ? OR u.phone_number LIKE ?)";
      $params[] = "%$contact%";
      $paramTypes[] = PDO::PARAM_STR;
      $params[] = "%$contact%";
      $paramTypes[] = PDO::PARAM_STR;
    }

    if (!empty($from_date)) {
      $conditions[] = "u.created_at >= ?";
      $params[] = $from_date;
      $paramTypes[] = PDO::PARAM_STR;
    }

    if (!empty($to_date)) {
      $conditions[] = "u.created_at <= ?";
      $params[] = $to_date;
      $paramTypes[] = PDO::PARAM_STR;
    }

    if (!empty($type)) {
      if ($type === "employee") {
        $conditions[] = "ur.role_id NOT IN (2)";
      } elseif ($type === "customer") {
        $conditions[] = "ur.role_id = 2";
      }
    }

    // Kết hợp điều kiện WHERE nếu có
    if (!empty($conditions)) {
      $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Thêm phân trang
    if ($limit !== null) {
      $query .= " LIMIT ?";
      $params[] = $limit;
      $paramTypes[] = PDO::PARAM_INT;
    }

    if ($offset !== null && $limit !== null) { // Chỉ thêm OFFSET nếu có LIMIT
      $query .= " OFFSET ?";
      $params[] = $offset;
      $paramTypes[] = PDO::PARAM_INT;
    }

    $stmt = $this->conn->prepare($query);

    // Bind các tham số với kiểu dữ liệu tương ứng
    foreach ($params as $i => $param) {
      $stmt->bindValue($i + 1, $param, $paramTypes[$i] ?? PDO::PARAM_STR);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tổng số bản ghi (không phân trang)
    $stmt = $this->conn->prepare("SELECT FOUND_ROWS()");
    $stmt->execute();
    $total = (int) $stmt->fetchColumn();

    return [
      'data' => $data,
      'total' => $total
    ];
  }
  public function countAll(): int
  {
    $sql = "SELECT COUNT(*) FROM users";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
  }
}
