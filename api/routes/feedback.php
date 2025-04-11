<?php

switch (true) {
  // Route to get all feedbacks or filter by user_id and product_variation_id
  case $uri === SOURCE_URI . "/feedbacks":
    $gateway = new FeedbackGateway($db);
    $controller = new FeedbackController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/feedbacks/responses":
    $gateway = new FeedbackResponseGateway($db);
    $controller = new FeedbackResponseController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
?>