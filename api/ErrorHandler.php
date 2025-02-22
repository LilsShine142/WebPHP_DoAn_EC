<?php

class ErrorHandler {
  public static function handleException(Throwable $exception): void {
    http_response_code(500);
    echo json_encode([
      "success" => false,
      "code" => $exception->getCode(),
      "message" => $exception->getMessage(),
      "file" => $exception->getFile(),
      "line" => $exception->getLine()
    ]);
  }

  public static function sendErrorResponse(int $statusCode, string | array $msg): void {
    http_response_code($statusCode);
    echo json_encode([
      "success" => false,
      "message" => $msg,
    ]);
  }
}
