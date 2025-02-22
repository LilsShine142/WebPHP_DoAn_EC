<?php

switch(true) {
  case $uri === SOURCE_URI . "/carts";
    $user_id = (int) $_GET["user_id"] ?? null;
    $product_variation_id = (int) $_GET["product_variation_id"] ?? null;
    $gateway = new CartGateway($db);
    $controller = new CartController($gateway, $auths);
    $controller->processRequest($method, $user_id, $product_variation_id, $limit, $offset);
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
