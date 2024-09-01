<?php

use Framework\Router;

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

// spl_autoload_register(function ($class){
//     $path = basePath('Framework/' .$class. '.php');
//     if(file_exists($path)){
//         require $path;
//     }
// });

// instatiate the router
$router = new Router();

// get routes
$routes = require basePath('routes.php');

// get current uri and http method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// route the request
$router->route($uri);