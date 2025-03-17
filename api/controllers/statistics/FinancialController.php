<?php

class FinancialController extends ErrorHandler
{
    public function __construct(private FinancialGateway $gateway, private Auths $auths) {}

    public function processRequest(string $method, ?int $id, ?int $limit, ?int $offset): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
            return;
        }
        $this->processCollectionRequest($method, $limit, $offset);
    }

    private function processResourceRequest(string $method, int $id): void
    {
        echo json_encode([
            "success" => true,
            "message" => "Chưa code phần này."
        ]);
    }

    private function processCollectionRequest(string $method, ?int $limit, ?int $offset): void
    {
        if ($method !== "GET") {
            $this->sendErrorResponse(405, "Only GET method is allowed");
            return;
        }

        $year = $_GET["year"] ?? null;
        $month = $_GET["month"] ?? null;

        if (!$year) {
            $this->sendErrorResponse(400, "Year parameter is required");
            return;
        }

        // Lấy dữ liệu từ Gateway
        $revenueData = json_decode($this->gateway->getTotalRevenue($month, $year), true);
        $expenseData = json_decode($this->gateway->getTotalExpense($month, $year), true);

        // Chuẩn hóa dữ liệu
        $formattedRevenue = $revenueData["success"] ? $revenueData["data"] : [];
        $formattedExpense = $expenseData["success"] ? $expenseData["data"] : [];

        // Gộp dữ liệu vào một JSON duy nhất
        $response = [
            "success" => true,
            "data" => [
                "revenue" => $formattedRevenue,
                "expense" => $formattedExpense
            ]
        ];

        header("Content-Type: application/json");
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
