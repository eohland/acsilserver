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
    if (404 === $this->controller) {
      header('HTTP/1.1 404 Not Found');
      return false; //TODO: Response:notFound
    }
    $id = $route[1];
    $method = strtolower($route[2]);
    if ('get' === $method && is_null($id))
      $method = 'getAll';
    $this->checkMethod($method);

    //TODO: Response::json
    echo json_encode($this->controller->$method($id, $this->getData()));
  }

  public function getData() {
    return json_decode(file_get_contents('php://input'));
  }
}
?>
