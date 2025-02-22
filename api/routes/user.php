<?php

switch(true) {
  case $uri === SOURCE_URI . "/users":
    $gateway = new UserGateway($db);
    $controller = new UserController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case strpos($uri, SOURCE_URI . "/users/user_roles") === 0:
    $user_id = (int) $_GET["user_id"] ?? null;
    $role_id = (int) $_GET["role_id"] ?? null;
    $gateway = new UserRoleGateway($db);
    $controller = new UserRoleController($gateway, $auths);
    $controller->processRequest($method, $user_id, $role_id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/users/roles":
    $gateway = new RoleGateway($db);
    $controller = new RoleController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case strpos($uri, SOURCE_URI . "/users/role_permissions") === 0:
    $role_id = (int) $_GET["role_id"] ?? null;
    $permission_id = (int) $_GET["permission_id"] ?? null;
    $gateway = new RolePermissionGateway($db);
    $controller = new RolePermissionController($gateway, $auths);
    $controller->processRequest($method, $role_id, $permission_id, $limit, $offset);
    break;

  case $uri === SOURCE_URI . "/users/permissions":
    $gateway = new PermissionGateway($db);
    $controller = new PermissionController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  case $uri === SOURCE_URI.  "/users/addresses":
    $gateway = new UserAddressGateway($db);
    $controller = new UserAddressController($gateway, $auths);
    $controller->processRequest($method, $id, $limit, $offset);
    break;

  default:
    $errorHandler = new ErrorHandler();
    $errorHandler->sendErrorResponse(404, "Request not found!");
}
