<?php

class Auths extends ErrorHandler  {
  private PDO $conn;

  public function __construct(Database $db, protected ?string $usr_email, protected ?string $usr_pwd) {
    $this->conn = $db->getConnection();
  }

  public function verifyAction(string $action_code): void {
    // if(!$this->usr_email || !$this->usr_pwd) {
    //   $this->sendErrorResponse(403, "Unauthorized");
    //   die;
    // }

    // $sql = "SELECT EXISTS (
    //     SELECT 1 FROM users AS usr
    //     INNER JOIN user_roles AS usr_rol ON usr.id = usr_rol.user_id
    //     INNER JOIN role_permissions AS rol_per ON usr_rol.role_id = rol_per.role_id
    //     INNER JOIN permissions AS per ON rol_per.permission_id = per.id
    //     WHERE usr.email = :email
    //     AND usr.password = :password
    //     AND per.action_code = LOWER(:action_code)
    //   )
    // ";

    // $stmt = $this->conn->prepare($sql);
    // $stmt->bindValue(":email", $this->usr_email, PDO::PARAM_STR);
    // $stmt->bindValue(":password", $this->usr_pwd, PDO::PARAM_STR);
    // $stmt->bindValue(":action_code", $action_code, PDO::PARAM_STR);
    // $stmt->execute();

    // $verify = (bool) $stmt->fetchColumn();
    // if(!$verify) {
    //   $this->sendErrorResponse(403, "Unauthorized");
    //   die;
    // }
  }

}
