<?php
require '../helpers.php';

require basePath('Router.php');
require basePath('Database.php');
// $config = require basePath('config/db.php');
// $db = new Database($config);

// instatiate the router
$router = new Router();

// get routes
$routes = require basePath('routes.php');

// get current uri and http method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// route the request
$router->route($uri, $method);