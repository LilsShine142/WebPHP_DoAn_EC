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
        $type = $_GET["type"] ?? "revenue"; // Mặc định là doanh thu

        if (!$year) {
            $this->sendErrorResponse(400, "Year parameter is required");
            return;
        }

        // Xác định lấy doanh thu hay chi phí
        if ($type === "revenue") {
            echo $this->gateway->getTotalRevenue($month, $year);
        } elseif ($type === "expense") {
            echo $this->gateway->getTotalExpense($month, $year);
        } else {
            $this->sendErrorResponse(400, "Invalid type parameter. Use 'revenue' or 'expense'.");
        }
    }
}
