<?php

namespace Framework;

// $routes = require basePath('routes.php');

// if(array_key_exists($uri, $routes)){
//     require(basePath($routes[$uri]));
// }else{
//     http_response_code(404);
//     require basePath($routes['404']);
// }

class Router {
    protected $routes = [];

 
/**
 * Add a new route
 * 
 * @param string $method
 * @param string $url
 * @param string $action
 * @return void
 * 
 */

public function registerRoute($method, $uri, $action)
{
    list($controller, $controllerMethod) = explode('@', $action);
    $this->routes[] = [
        'method' => $method,
        'uri' => $uri,
        'controller' => $controller,
        'controllerMethod' => $controllerMethod
    ];
}

/**
 * 
 * Add a GET Route
 * 
 * @param string $uri
 * @param string $controller
 * @return void
 */

 public function get($uri, $controller){
    $this->registerRoute('GET', $uri, $controller);
 }

 /**
 * 
 * Add a POST Route
 * 
 * @param string $uri
 * @param string $controller
 * @return void
 */

 public function post($uri, $controller){
    $this->registerRoute('POST', $uri, $controller);

 }
 /**
 * 
 * Add a PUT Route
 * 
 * @param string $uri
 * @param string $controller
 * @return void
 */

 public function put($uri, $controller){
    $this->registerRoute('PUT', $uri, $controller);

 }

  /**
 * 
 * Add a DELETE Route
 * 
 * @param string $uri
 * @param string $controller
 * @return void
 */

 public function delete($uri, $controller){
    $this->registerRoute('DELETE', $uri, $controller);

 }
 
 /**
  * Load error page
  * @param int $httpcode
  * @return void
  */

  public function error($httpcode = 404)
  {
    http_response_code($httpcode);
    loadView("error/{$httpcode}");
    exit;
  }


 /**
  * Route the request
  *
  * @param string $uri
  * @param string $method
  * @return void
  */

  public function route($uri, $method)
  {
    foreach($this->routes as $route){
        if($route['uri'] === $uri && $route['method'] === $method){
            $controller = 'App\\Controllers\\' .$route['controller'];
            $controllerMethod = $route['controllerMethod'];

            $controllerInstance = new $controller();
            $controllerInstance->$controllerMethod();
            return;

            // $listing = new ListingController;
            // $listing->index();
        }
    }
   $this->error();
  }


}