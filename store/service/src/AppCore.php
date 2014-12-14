<?php
class AppCore {
  private $controller;

  public function loadController($controller) {
    $ctrlName = 'Controllers\\' . $controller;
    try {
      $ctrl = new $ctrlName();
    }
    catch (Exception $e) {
      error_log($e->getMessage());
      return 404;
    }
    return $ctrl;
  }

  public function checkMethod($method) {
    if (!method_exists($this->controller, $method))
      return $this->controller->listMethods(); //TODO: Response::allowedMethods
  }

  public function loadRoute($route) {
    $this->controller = $this->loadController($route[0]);
    $id = $route[1];
    $method = (is_null($id)) ? 'getAll' : strtolower($route[2]);
    $this->checkMethod($method);
    echo $this->controller->$method($id); //TODO: Response::json
  }
}
?>
