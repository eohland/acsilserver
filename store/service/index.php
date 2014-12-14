<?php
  require_once ('src/Utils/autoload.php');

  use Utils\Router;

  $router = new Router();
  $core   = new AppCore();
  //$router->loadRoutes('config/routes.yml');
  $route = $router->autoResolve($_SERVER['PATH_INFO']);
  $core->loadRoute($route);
?>
