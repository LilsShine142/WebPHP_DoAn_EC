<?php

switch(true) {
  case $uri === SOURCE_URI . "/orders":
    $gateway = new OrderGateway($db);
    $controller = new OrderController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/orders/delivery_states":
    $gateway = new OrderDeliveryStateGateway($db);
    $controller = new OrderDeliveryStateController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/orders/items":
    $gateway = new OrderItemGateway($db);
    $controller = new OrderItemController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

    case preg_match("#^" . SOURCE_URI . "/order_items$#", $uri) ? true : false:
      $gateway = new OrderItemGateway($db);
      $controller = new OrderItemController($gateway, $auths);
      $controller->processRequest($method, $id, $limit, $offset, $_GET["order_id"] ?? null);
      break;  

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
