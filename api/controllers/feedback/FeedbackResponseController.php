<?php
class FeedbackResponseController extends ErrorHandler {

    public function __construct(private FeedbackResponseGateway $gateway, private Auths $auths) {}

    public function processRequest(string $method, ?string $id, ?int $limit, ?int $offset): void {
        if($id) {
          $this->processResourceRequest($method, $id);
          return;
        }
        $this->processCollectionRequest($method, $limit, $offset);
    }

    private function processResourceRequest(string $method, string $id): void {
        $feedbackResponse = $this->gateway->get($id);
        if (!$feedbackResponse) {
            $this->sendErrorResponse(404, "Feedback response with id $id not found");
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode(["success" => true, "data" => $feedbackResponse]);
                break;

            case "PUT":
                $this->auths->verifyAction("UPDATE_FEEDBACK_RESPONSE");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $res = $this->gateway->update($feedbackResponse["id"], $data);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback response updated",
                    "data" => $res
                ]);
                break;

            case "DELETE":
                $this->auths->verifyAction("DELETE_FEEDBACK_RESPONSE");
                $this->gateway->delete($id);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback response id $id was deleted"
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
                if(isset($_GET["feedback_id"]) && is_numeric($_GET["feedback_id"])) {
                    $feedback_id = $_GET["feedback_id"];
                    $res = $this->gateway->getByFeedbackId($feedback_id);
                } else {
                    $res = $this->gateway->getAll($limit, $offset);
                }
                echo json_encode([
                    "success" => true,
                    "data" => $res
                ]);
                break;

            case "POST":
                $this->auths->verifyAction("CREATE_FEEDBACK_RESPONSE");
                $data = (array) json_decode(file_get_contents("php://input"));
                $errors = $this->getValidationErrors($data, true);
                if (!empty($errors)) {
                    $this->sendErrorResponse(422, $errors);
                    break;
                }
                $res = $this->gateway->create($data);
                echo json_encode([
                    "success" => true,
                    "message" => "Feedback response created",
                    "data" => $res
                ]);
                break;

            default:
                $this->sendErrorResponse(405, "Only GET, POST methods are allowed");
                header("Allow: GET, POST");
        }
    }
    private function getValidationErrors(array $data, bool $isCreate): array {
        $errors = [];

        if ($isCreate) {
            if (empty($data["feedback_id"])) {
                $errors["feedback_id"] = "Feedback ID is required";
            }
            if (empty($data["admin_id"])) {
                $errors["admin_id"] = "Admin ID is required";
            }
        }

        if (empty($data["response_content"])) {
            $errors["response_content"] = "Response content is required";
        }

        return $errors;
    }
}
?>