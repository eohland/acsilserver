<?php
namespace Controllers;

use PDO;
use Exception;

use Utils\Authenticate;

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
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  public function getAll() {
    $filterSQL = null;
    $params = null;
    if (isset($_GET['author_id'])) {
      $filterSQL = 'WHERE `author_id` LIKE :author_id';
      $params = array('author_id' => $_GET['author_id']);
    }
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `name`, `author_id`,
          `description`, `keywords`, `version`,
          `create_date`, `update_date`,
          `picture`, `content`
        FROM `plugins`
        ' . $filterSQL . ';
      ');
      $sth->execute($params);
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
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
      $resource = $sth->fetch(PDO::FETCH_ASSOC);
      if (false === $resource) {
        //TODO: Return a Response object
        header('HTTP/1.0 404 Not Found');
        return array('errorCode' => 404, 'errorMessage' => 'Not Found');
      }
      return $resource;
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  // Create
  public function put($id, $plugin) {
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
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
      header('HTTP/1.0 201 Created');
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  // Update
  public function post($id, $plugin) {
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
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
      header('HTTP/1.0 204 No Content');
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  public function delete($id) {
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
    try {
      $sth = $this->pdo->prepare('
        DELETE FROM `plugins` WHERE `id` LIKE :id
      ');
      $sth->execute(array(
        'id'          => $id,
      ));
      //FIXME: Return Response object
      header('HTTP/1.0 204 No Content');
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }
}
?>
