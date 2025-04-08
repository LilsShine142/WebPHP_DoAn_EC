<?php

switch (true) {
  case $uri === SOURCE_URI . "/products":
    $gateway = new ProductGateway($db);
    $controller = new ProductController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/products/os":
    $gateway = new ProductOSGateway($db);
    $controller = new ProductOSController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/products/brands":
    $gateway = new ProductBrandGateway($db);
    $controller = new ProductBrandController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/products/categories":
    $gateway = new ProductCategoryGateway($db);
    $controller = new ProductCategoryController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;


  case strpos($uri, SOURCE_URI . "/products/instances") === 0:
    $utils = new Utils();
    $sku = $utils->isValidProductSKU(end($uri_parts)) ? end($uri_parts) : null;
    $gateway = new ProductInstanceGateway($db);
    $controller = new ProductInstanceController($gateway, $auths);
    $controller->processRequest($method, $sku, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/products/variations":
    $gateway = new ProductVariationGateway($db);
    $controller = new ProductVariationController($gateway, $auths);
    $controller->processRequest($method, null, $id, $limit, $offset);
    break;
  // Xử lý route /products/variations/latest
  case $uri === SOURCE_URI . "/products/variations/latest":
    $gateway = new ProductVariationGateway($db);
    $controller = new ProductVariationController($gateway, $auths);
    // Phân tích URL chính xác
    $parsedUrl = parse_url(url: $uri);
    $path = $parsedUrl['path'] ?? '';
    $pathParts = explode('/', trim($path, '/'));

    $action = $pathParts[4] ?? null;
    // Xử lý route /products/variations/latest
    if (isset($action) && $action === 'latest') {
      $controller->processRequest($method, $action, $id, $limit, $offset);
    }
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
