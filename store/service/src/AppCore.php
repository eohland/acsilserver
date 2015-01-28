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
      //TODO: Response:notFound
      header('HTTP/1.0 404 Not Found');
      echo json_encode(array(
        'errorCode' => 404, 'errorMessage' => 'Not Found'
      ), JSON_NUMERIC_CHECK);
      return false;
    }
    $id = $route[1];
    $method = strtolower($route[2]);
    if ('get' === $method && is_null($id))
      $method = 'getAll';
    $this->checkMethod($method);

    //TODO: Response::json
    echo json_encode(
      $this->controller->$method($id, $this->getData()),
      JSON_NUMERIC_CHECK
    );
  }

  public function getData() {
    return json_decode(file_get_contents('php://input'));
  }
}
?>
