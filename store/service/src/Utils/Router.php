<?php
namespace Utils;

use Exception;

class Router {
  private $routes;

  public function getMethod() {
    if(!isset($_SERVER['REQUEST_METHOD']))
      return 'GET';
    return $_SERVER['REQUEST_METHOD'];
  }

  public function loadRoutes($configFile) {
    if (!is_readable($configFile)) {
      throw new Exception(__METHOD__ . ': ' . $configFile . ' not readable');
      return false;
    }
    $this->routes = yaml_parse_file($configFile);
  }

  public function resolve($searchRoute) {
    if (is_null($this->routes)) {
      throw new Exception(__METHOD__ . ': no routes defined' );
      return false;
    }
    foreach ($this->routes as $route) {
      if ($searchRoute == $route['path'])
        return $route;
    }
    //TODO: route not found
  }

  public function autoResolve($path) {
    if (1 !== preg_match('#^/(\w+)(/\w+)*#', $path, $matches))
      return 404;
    $controller = ucfirst($matches[1]);
    $id = NULL;
    if (isset($matches[2]) && 1 < strlen($matches[2]))
      $id = substr($matches[2], 1);
    return array($controller, $id, strtolower($this->getMethod()));
  }

}
?>
