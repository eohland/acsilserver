<?php
namespace Utils;

use Utils\DBCtrl;

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
    $this->pdo = DBCtrl::getDb();
    return $this->pdo;
  }
}
?>
