<?php

switch(true) {
  case $uri === SOURCE_URI . "/goods_receipt_notes":
    $gateway = new GoodsReceiptNoteGateway($db);
    $controller = new GoodsReceiptNoteController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
