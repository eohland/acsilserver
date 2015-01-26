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
      return $sth->fetch(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log ('Plugin::get: ' . $e->getMessage());
    }
  }

  // Create
  public function put($id, $plugin) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        INSERT INTO `plugins`(
          `name`, `author_id`,
          `description`, `keywords`, `version`,
          `create_date`, `update_date`,
          `picture`, `content`
        ) VALUES(
          :name, :author_id,
          :description, :keywords, :version,
          :create_date, :update_date,
          :picture, :content
        );
      ');
      //FIXME: valdate data before
      $sth->execute(array(
        'name'        => $plugin->name,
        'author_id'   => $plugin->author_id,
        'description' => $plugin->description,
        'keywords'    => $plugin->keywords,
        'version'     => $plugin->version,
        'create_date' => $plugin->create_date,
        'update_date' => $plugin->update_date,
        'picture'     => $plugin->picture,
        'content'     => $plugin->content,
      ));
      //FIXME: Return 201 or 204
    }
    catch (Exception $e) {
      error_log ('Plugin::put: ' . $e->getMessage());
      //TODO: Return 400?
    }
  }

  // Update
  public function post($id, $plugin) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        INSERT OR REPLACE INTO `plugins`(
          `id`, `name`, `author_id`,
          `description`, `keywords`, `version`,
          `create_date`, `update_date`,
          `picture`, `content`
        ) VALUES(
          :id, :name, :author_id,
          :description, :keywords, :version,
          :create_date, :update_date,
          :picture, :content
        );
      ');
      $sth->execute(array(
        'id'          => $plugin->id,
        'name'        => $plugin->name,
        'author_id'   => $plugin->author,
        'description' => $plugin->description,
        'keywords'    => $plugin->keywords,
        'version'     => $plugin->version,
        'create_date' => $plugin->create_date,
        'update_date' => $plugin->update_date,
        'picture'     => $plugin->picture,
        'content'     => $plugin->content,
      ));
    }
    catch (Exception $e) {
      error_log ('Plugin::post: ' . $e->getMessage());
      //TODO: Return 400?
    }
  }

  public function delete($id) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        DELETE FROM `plugins` WHERE `id` LIKE :id
      ');
      $sth->execute(array(
        'id'          => $id,
      ));
      //FIXME: Return 204
    }
    catch (Exception $e) {
      error_log ('Plugin::delete: ' . $e->getMessage());
    }
  }
}
?>
