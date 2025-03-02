<?php

switch(true) {
  case $uri === SOURCE_URI . "/providers":
    $gateway = new ProviderGateway($db);
    $controller = new ProviderController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
