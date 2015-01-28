<?php
namespace Controllers;

use PDO;
use Exception;

use Utils\Authenticate;

class User extends \Utils\BaseController {
  protected function createTable() {
    try {
      $sth = $this->pdo->prepare('
        CREATE TABLE IF NOT EXISTS `users` (
          `id`            INTEGER PRIMARY KEY AUTOINCREMENT,
          `login`         TEXT NOT NULL UNIQUE,
          `password`      TEXT NOT NULL,
          `display_name`  TEXT NOT NULL,
          `email`         TEXT NOT NULL UNIQUE,
          `create_date`   INTEGER,
          `update_date`   INTEGER
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
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`
        FROM `users`;
      ');
      $sth->execute();
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  public function get($id) {
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`
        FROM `users`
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
  public function put($id, $user) {
    try {
      $sth = $this->pdo->prepare('
        INSERT INTO `users`(
          `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`
        ) VALUES(
          :login, :password,
          :display_name, :email,
          :create_date, :update_date
        );
      ');
      //FIXME: valdate data before
      $sth->execute(array(
        'login'        => $user->login,
        'password'     => $user->password,
        'display_name' => $user->display_name,
        'email'        => $user->email,
        'create_date'  => $user->create_date,
        'update_date'  => $user->update_date,
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
  public function post($id, $user) {
    if ($id === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
    try {
      $sth = $this->pdo->prepare('
        INSERT OR REPLACE INTO `users`(
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`
        ) VALUES(
          :id, :login, :password,
          :display_name, :email,
          :create_date, :update_date
        );
      ');
      $sth->execute(array(
        'id'           => $user->id,
        'login'        => $user->login,
        'password'     => $user->password,
        'display_name' => $user->display_name,
        'email'        => $user->email,
        'create_date'  => $user->create_date,
        'update_date'  => $user->update_date,
      ));
      header('HTTP/1.0 204 No Content');
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  public function delete($id) {
    if ($id !== Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
    try {
      $sth = $this->pdo->prepare('
        DELETE FROM `users` WHERE `id` LIKE :id
      ');
      $sth->execute(array(
        'id'          => $id,
      ));
      //FIXME: Return 204
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }
}
?>
