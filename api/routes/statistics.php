<?php

switch (true) {
    case $uri === SOURCE_URI . "/statistics/financial":
        $gateway = new FinancialGateway($db);
        $controller = new FinancialController($gateway, $auths);
        $controller->processRequest($method, $id, $limit, $offset);
        break;

    default:
        $errorHandler = new ErrorHandler();
        $errorHandler->sendErrorResponse(404, "Request nots found!");
}
