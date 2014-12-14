<?php
namespace Controllers;

class Plugin extends \Utils\BaseController {
  public function getAll() {
    return 'Plugin List';
  }

  public function get($id) {
    return 'Get plugin ' . $id;
  }
}
?>
