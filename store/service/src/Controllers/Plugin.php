<?php
namespace Controllers;

use PDO;
use Exception;

class Plugin extends \Utils\BaseController {
  protected function createTable() {
    try {
      $sth = $this->pdo->prepare('
        CREATE TABLE IF NOT EXISTS `plugins` (
          `id`            INTEGER PRIMARY KEY AUTOINCREMENT,
          `name`          TEXT NOT NULL,
          `author_id`     INTEGER NOT NULL,
          `description`   TEXT NOT NULL,
          `keywords`      TEXT,
          `version`       TEXT NOT NULL,
          `create_date`   INTEGER,
          `update_date`   INTEGER,
          `picture`       TEXT,
          `content`       TEXT NOT NULL
        );
      ');
      $sth->execute();
    }
    catch (Exception $e) {
      error_log ('Plugin::createTable: ' . $e->getMessage());
    }
  }

  public function getAll() {
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `name`, `author_id`,
          `description`, `keywords`, `version`,
          `create_date`, `update_date`,
          `picture`, `content`
        FROM `plugins`;
      ');
      $sth->execute();
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log ('Plugin::getAll: ' . $e->getMessage());
    }
  }

  public function get($id) {
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `name`, `author_id`,
          `description`, `keywords`, `version`,
          `create_date`, `update_date`,
          `picture`, `content`
        FROM `plugins`
        WHERE id LIKE :id;
      ');
      $sth->execute(array('id' => $id));
      return $sth->fetchAll(PDO::FETCH_COLUMN);
    }
    catch (Exception $e) {
      error_log ('Plugin::get: ' . $e->getMessage());
    }
  }

  public function create($plugin) {
    
  }
}
?>
