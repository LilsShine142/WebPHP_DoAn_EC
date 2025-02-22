<?php

define("GATEWAYS_PATH", __DIR__ . "/gateways");
define("CONTROLLERS_PATH", __DIR__ . "/controllers");

define("PRODUCT_GATEWAY_PATH", GATEWAYS_PATH . "/product");
define("PRODUCT_CONTROLLER_PATH", CONTROLLERS_PATH . "/product");

define("USER_GATEWAY_PATH", GATEWAYS_PATH . "/user");
define("USER_CONTROLLER_PATH", CONTROLLERS_PATH . "/user");

define("CART_GATEWAY_PATH", GATEWAYS_PATH . "/cart");
define("CART_CONTROLLER_PATH", CONTROLLERS_PATH . "/cart");

define("PROVIDER_GATEWAY_PATH", GATEWAYS_PATH . "/provider");
define("PROVIDER_CONTROLLER_PATH", CONTROLLERS_PATH . "/provider");

define("GOODS_GATEWAY_PATH", GATEWAYS_PATH . "/goods");
define("GOODS_CONTROLLER_PATH", CONTROLLERS_PATH . "/goods");

return [
  "Database" => __DIR__ . "/config/Database.php",
  "ProductGateway" => PRODUCT_GATEWAY_PATH . "/ProductGateway.php",
  "ProductVariationGateway" => PRODUCT_GATEWAY_PATH . "/ProductVariationGateway.php",
  "ProductBrandGateway" => PRODUCT_GATEWAY_PATH . "/ProductBrandGateway.php",
  "ProductCategoryGateway" => PRODUCT_GATEWAY_PATH . "/ProductCategoryGateway.php",
  "ProductOSGateway" => PRODUCT_GATEWAY_PATH . "/ProductOSGateway.php",
  "ProductInstanceGateway" => PRODUCT_GATEWAY_PATH . "/ProductInstanceGateway.php",

  "UserGateway" => USER_GATEWAY_PATH . "/UserGateway.php",
  "UserRoleGateway" => USER_GATEWAY_PATH . "/UserRoleGateway.php",
  "RoleGateway" => USER_GATEWAY_PATH . "/RoleGateway.php",
  "RolePermissionGateway" => USER_GATEWAY_PATH . "/RolePermissionGateway.php",
  "PermissionGateway" => USER_GATEWAY_PATH . "/PermissionGateway.php",
  "UserAddressGateway" => USER_GATEWAY_PATH  . "/UserAddressGateway.php",

  "CartGateway" => CART_GATEWAY_PATH . "/CartGateway.php",

  "ProviderGateway" => PROVIDER_GATEWAY_PATH . "/ProviderGateway.php",
  
  "GoodsReceiptNoteGateway" => GOODS_GATEWAY_PATH . "/GoodsReceiptNoteGateway.php",

  "ProductController" => PRODUCT_CONTROLLER_PATH . "/ProductController.php",
  "ProductVariationController" => PRODUCT_CONTROLLER_PATH . "/ProductVariationController.php",
  "ProductBrandController" => PRODUCT_CONTROLLER_PATH . "/ProductBrandController.php",
  "ProductCategoryController" => PRODUCT_CONTROLLER_PATH . "/ProductCategoryController.php",
  "ProductOSController" => PRODUCT_CONTROLLER_PATH . "/ProductOSController.php",
  "ProductInstanceController" => PRODUCT_CONTROLLER_PATH . "/ProductInstanceController.php",

  "UserController" => USER_CONTROLLER_PATH . "/UserController.php",
  "UserRoleController" => USER_CONTROLLER_PATH . "/UserRoleController.php",
  "RoleController" => USER_CONTROLLER_PATH . "/RoleController.php",
  "RolePermissionController" => USER_CONTROLLER_PATH . "/RolePermissionController.php",
  "PermissionController" => USER_CONTROLLER_PATH . "/PermissionController.php",
  "UserAddressController" => USER_CONTROLLER_PATH . "/UserAddressController.php",

  "CartController" => CART_CONTROLLER_PATH . "/CartController.php",

  "ProviderController" => PROVIDER_CONTROLLER_PATH . "/ProviderController.php",

  "GoodsReceiptNoteController" => GOODS_CONTROLLER_PATH . "/GoodsReceiptNoteController.php",

  "OrdersGateway" => __DIR__ . "/gateways/order/OrdersGateway.php",
  "OrdersController" => __DIR__ . "/controllers/order/OrdersController.php",
  "OrderDeliveryStateGateway" => __DIR__ . "/gateways/order/OrderDeliveryStateGateway.php",
  "OrderDeliveryStateController" => __DIR__ . "/controllers/order/OrderDeliveryStateController.php",
  "OrderItemsGateway" => __DIR__ . "/gateways/order/OrderItemsGateway.php",
  "OrderItemsController" => __DIR__ . "/controllers/order/OrderItemsController.php",

  "ErrorHandler" => __DIR__ . "/ErrorHandler.php",
  "Auths" => __DIR__ . "/Auths.php",
  "Utils" => __DIR__ . "/Utils.php"
  // ... other classes
];

