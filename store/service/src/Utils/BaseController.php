<?php
namespace Utils;

use PDO;

class BaseController {
  protected $pdo;

  public function __construct() {
    $this->getPDO();
    $this->createTable();
  }

  protected function createTable() {
  }

  public function listMethods() {
    //TODO: return existing methods only
    return array(
      'get',
      'getAll',
      'create',
      'update',
      'delete',
    );
  }

  //TODO: Move to a base entity
  public function getPDO() {
    //TODO:Use a config service
    $this->pdo = new PDO('sqlite:storedb.sqlite');
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $this->pdo;
  }
}
?>
