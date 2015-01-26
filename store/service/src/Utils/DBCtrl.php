<?php
namespace Utils;

use PDO;

class DBCtrl {
  public static function getDB() {
    //TODO:Use a config service
    $pdo = new PDO('sqlite:storedb.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }
}
?>
