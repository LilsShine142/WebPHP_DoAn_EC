<?php

class FeedbackController extends ErrorHandler {

    public function __construct(private FeedbackGateway $gateway, private Auths $auths) {}

    public function processRequest(string $method, ?string $id, ?int $limit, ?int $offset): void {
        if($id) {
          $this->processResourceRequest($method, $id);
          return;
        }
        $this->processCollectionRequest($method, $limit, $offset);
    }

    private function processResourceRequest(string $method, string $id): void {
        $feedback = $this->gateway->get($id);
        if (!$feedback) {
            $this->sendErrorResponse(404, "Feedback with id $id not found");
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode(["success" => true, "data" => $feedback]);
                break;

            case "PUT":
                $this->auths->verifyAction("UPDATE_FEEDBACK");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $res = $this->gateway->update($feedback, $data);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback updated",
                    "data" => $res
                ]);
                break;

            case "DELETE":
                $this->auths->verifyAction("DELETE_FEEDBACK");
                $this->gateway->delete($id);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback id $id was deleted"
                ]);
                break;

            default:
                $this->sendErrorResponse(405, "Only GET, PUT, DELETE methods are allowed");
                header("Allow: GET, PUT, DELETE");
        }
    }
    private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void {
        switch ($method) {
            case "GET":
                if(isset($_GET["product_variation_id"]) && is_numeric($_GET["product_variation_id"])) {
                    $this->auths->verifyAction("GET_FEEDBACK_BY_PRODUCT_VARIATION_ID");
                    $feedback = $this->gateway->getByProductId($_GET["product_variation_id"]);
                } else {
                    $this->auths->verifyAction("GET_ALL_FEEDBACK");
                    $feedback = $this->gateway->getAll($limit, $offset);
                }
                echo json_encode([
                    "success" => true,
                    "length" => count($feedback),
                    "data" => $feedback
                ]);
                break;

            case "POST":
                $this->auths->verifyAction("CREATE_FEEDBACK");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, true);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $res = $this->gateway->create($data);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback created",
                    "data" => $res
                ]);
                break;
            
            case "PATCH":
                $this->auths->verifyAction("UPDATE_FEEDBACK");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $res = $this->gateway->update($data["id"], $data);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback updated",
                    "data" => $res
                ]);
                break;

            default:
                $this->sendErrorResponse(405, "Only GET, PATCH and POST methods are allowed");
                header("Allow: GET, POST, PATCH");
        }
    }

    private function getValidationErrors(array $data, bool $new = true): array {
        $errors = [];

        if ($new) {
            if (!isset($data["user_id"]) || !is_numeric($data["user_id"])) {
                $errors[] = "user_id is required and must be a number";
            }
            if (!isset($data["product_variation_id"]) || !is_numeric($data["product_variation_id"])) {
                $errors[] = "product_variation_id is required and must be a number";
            }
            if (!isset($data["content"]) || empty(trim($data["content"]))) {
                $errors[] = "content is required";
            }
            if (!isset($data["rating"]) || !is_numeric($data["rating"])) {
                $errors[] = "rating is required and must be a number";
            }
        }

        if (array_key_exists("user_id", $data) && !is_numeric($data["user_id"])) {
            $errors[] = "user_id must be a number";
        }
        if (array_key_exists("product_variation_id", $data) && !is_numeric($data["product_variation_id"])) {
            $errors[] = "product_variation_id must be a number";
        }
        if (array_key_exists("content", $data) && empty(trim($data["content"]))) {
            $errors[] = "content cannot be empty";
        }
        if (array_key_exists("rating", $data) && !is_numeric($data["rating"])) {
            $errors[] = "rating must be a number";
        }
        if (array_key_exists("rating", $data) && ($data["rating"] < 1 || $data["rating"] > 5)) {
            $errors[] = "rating must be between 1 and 5";
        }

        return $errors;
    }   

    
}