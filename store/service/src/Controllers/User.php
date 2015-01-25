<?php
namespace Controllers;

use PDO;
use Exception;

class User extends \Utils\BaseController {
  protected function createTable() {
    try {
      $sth = $this->pdo->prepare('
        CREATE TABLE IF NOT EXISTS `users` (
          `id`            INTEGER PRIMARY KEY AUTOINCREMENT,
          `login`         TEXT NOT NULL,
          `password`      TEXT NOT NULL,
          `display_name`  TEXT NOT NULL,
          `email`         TEXT NOT NULL,
          `create_date`   INTEGER,
          `update_date`   INTEGER,
        );
      ');
      $sth->execute();
    }
    catch (Exception $e) {
      error_log ('User::createTable: ' . $e->getMessage());
    }
  }

  public function getAll() {
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`,
        FROM `users`;
      ');
      $sth->execute();
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log ('User::getAll: ' . $e->getMessage());
    }
  }

  public function get($id) {
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`,
        FROM `users`
        WHERE id LIKE :id;
      ');
      $sth->execute(array('id' => $id));
      return $sth->fetch(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log ('User::get: ' . $e->getMessage());
    }
  }

  // Create
  public function put($id, $user) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        INSERT INTO `users`(
          `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`,
        ) VALUES(
          :login, :password,
          :display_name, :email,
          :create_date, :update_date,
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
    }
    catch (Exception $e) {
      error_log ('User::put: ' . $e->getMessage());
      //TODO: Return 400?
    }
  }

  // Update
  public function post($id, $user) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        INSERT OR REPLACE INTO `users`(
          `id`, `login`, `password`,
          `display_name`, `email`,
          `create_date`, `update_date`,
        ) VALUES(
          :id, :login, :password,
          :display_name, :email,
          :create_date, :update_date,
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
    }
    catch (Exception $e) {
      error_log ('User::post: ' . $e->getMessage());
      //TODO: Return 400?
    }
  }

  public function delete($id) {
    //FIXME: Check user permissions
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
      error_log ('User::delete: ' . $e->getMessage());
    }
  }
}
?>
