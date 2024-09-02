<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;
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
 * @param array $middleware
 * @return void
 * 
 */

public function registerRoute($method, $uri, $action, $middleware = [])
{
    list($controller, $controllerMethod) = explode('@', $action);
    $this->routes[] = [
        'method' => $method,
        'uri' => $uri,
        'controller' => $controller,
        'controllerMethod' => $controllerMethod,
        'middleware' => $middleware
    ];
}

/**
 * 
 * Add a GET Route
 * 
 * @param string $uri
 * @param string $controller
  * @param array $middleware
 * @return void
 */

 public function get($uri, $controller, $middleware = []){
    $this->registerRoute('GET', $uri, $controller, $middleware);
 }

 /**
 * 
 * Add a POST Route
 * 
 * @param string $uri
 * @param string $controller
  * @param array $middleware
 * @return void
 */

 public function post($uri, $controller, $middleware = []){
    $this->registerRoute('POST', $uri, $controller,$middleware);

 }
 /**
 * 
 * Add a PUT Route
 * 
 * @param string $uri
 * @param string $controller
 * @param array $middleware
 * @return void
 */

 public function put($uri, $controller, $middleware = []){
    $this->registerRoute('PUT', $uri, $controller, $middleware);

 }

  /**
 * 
 * Add a DELETE Route
 * 
 * @param string $uri
 * @param string $controller
 * @param array $middleware
 * @return void
 */

 public function delete($uri, $controller, $middleware = []){
    $this->registerRoute('DELETE', $uri, $controller, $middleware);

 }
 
//  /**
//   * Load error page
//   * @param int $httpcode
//   * @return void
//   */

//   public function error($httpcode = 404)
//   {
//     http_response_code($httpcode);
//     loadView("error/{$httpcode}");
//     exit;
//   }


 /**
  * Route the request
  *
  * @param string $uri
  * @param string $method
  * @return void
  */

  public function route($uri)
  {
      $requestMethod = $_SERVER['REQUEST_METHOD'];
  
      if($requestMethod === 'POST' && isset($_POST['method']))
      {
        $requestMethod = strtoupper($_POST['_method']);
      }

      foreach ($this->routes as $route) {
  
          $uriSegments = explode('/', trim($uri, '/'));
          $routeSegments = explode('/', trim($route['uri'], '/'));
          $match = true;
          if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
              $params = []; // Initialize $params as an array
  
              for ($i = 0; $i < count($uriSegments); $i++) {
                  if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                      $match = false;
                      break;
                  }
                  if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                      $params[$matches[1]] = $uriSegments[$i];
                  }
              }
  
              if ($match) {
                foreach($route['middleware'] as $middleware){
                    (new Authorize())->handle($middleware);
                }
                  $controller = 'App\\Controllers\\' . $route['controller'];
                  $controllerMethod = $route['controllerMethod'];
  
                  $controllerInstance = new $controller();
                  $controllerInstance->$controllerMethod($params);
                  return;
              }
          }
      }
      ErrorController::notFound();
  }
  

}