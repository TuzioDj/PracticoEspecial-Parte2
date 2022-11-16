<?php
require_once("./Router.php");
require_once("app/controllers/ProductsApiController.php");
require_once("app/controllers/ApiAuthController.php");

define("BASE_URL", 'http://'.$_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"].dirname($_SERVER["PHP_SELF"]).'/');

// recurso solicitado
$resource = $_GET["resource"];

// método utilizado
$method = $_SERVER["REQUEST_METHOD"];

// instancia el router
$router = new Router();

// arma la tabla de ruteo
$router->addRoute("productos", "GET", "ProductsApiController", "getProducts");
$router->addRoute("productos", "POST", "ProductsApiController", "addProduct");
$router->addRoute("productos/:ID", "GET", "ProductsApiController", "getProduct");
$router->addRoute("productos/:ID", "DELETE", "ProductsApiController", "deleteProduct");
$router->addRoute("productos/:ID", "PUT", "ProductsApiController", "editProduct");
$router->addRoute("getToken", "GET", "AuthApiController", "getToken");

$router->setDefaultRoute("ProductsApiController", "pageNotFound");


// rutea
$router->route($resource, $method);

