<?php

class FeedbackResponseGateway {
    private PDO $conn;

    public function __construct(Database $db) {
        $this->conn = $db->getConnection();
    }

    public function getAll(?int $limit, ?int $offset): array | false {
        if ($limit && $offset) {
            $sql = "SELECT * FROM feedback_responses LIMIT :limit OFFSET :offset";
        } elseif ($limit) {
            $sql = "SELECT * FROM feedback_responses LIMIT :limit";
        } elseif ($offset) {
            $sql = "SELECT * FROM feedback_responses LIMIT 18446744073709551615 OFFSET :offset";
        } else {
            $sql = "SELECT * FROM feedback_responses";
        }

        $stmt = $this->conn->prepare($sql);
        if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array | false {
        $feedback_id = $data["feedback_id"];
        $admin_id = $data["admin_id"];
        $response_content = $data["response_content"];

        $sql = "INSERT INTO feedback_responses (feedback_id, admin_id, response_content) VALUES (:feedback_id, :admin_id, :response_content)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":feedback_id", $feedback_id, PDO::PARAM_INT);
        $stmt->bindValue(":admin_id", $admin_id, PDO::PARAM_INT);
        $stmt->bindValue(":response_content", $response_content, PDO::PARAM_STR);
        $stmt->execute();

        return $this->get($this->conn->lastInsertId());
    }

    public function update(int $id, array $data): array | false {
        $sql = "UPDATE feedback_responses SET
            feedback_id = :feedback_id,
            admin_id = :admin_id,
            response_content = :response_content
            WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":feedback_id", $data["feedback_id"], PDO::PARAM_INT);
        $stmt->bindValue(":admin_id", $data["admin_id"], PDO::PARAM_INT);
        $stmt->bindValue(":response_content", $data["response_content"], PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->get($id);
    }

    public function get(int $id): array | false {
        $sql = "SELECT * FROM feedback_responses WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM feedback_responses WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getByFeedbackId(int $feedback_id): array | false {
        $sql = "SELECT * FROM feedback_responses WHERE feedback_id = :feedback_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":feedback_id", $feedback_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}