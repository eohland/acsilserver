<?php
namespace Controllers;

use Utils\Authenticate;
use PDO;
use Exception;

class Token extends \Utils\BaseController {
  protected function createTable() {
    try {
      $sth = $this->pdo->prepare('
        CREATE TABLE IF NOT EXISTS `tokens` (
          `token`         TEXT PRIMARY KEY,
          `user_id`       INTEGER NOT NULL,
          `ip_address`    TEXT,
          `create_date`   INTEGER
        );
      ');
      $sth->execute();
    }
    catch (Exception $e) {
      error_log ('Token::createTable: ' . $e->getMessage());
    }
  }

  public function get($token) {
    try {
      $sth = $this->pdo->prepare('
        SELECT
          `token`, `user_id`,
          `ip_address`, `create_date`
        FROM `tokens`
        WHERE token LIKE :token;
      ');
      $sth->execute(array('token' => $token));
      return $sth->fetch(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      error_log ('Token::get: ' . $e->getMessage());
    }
  }

  // Create
  public function put($token, $userData) {
    $user_id = Authenticate::auth($userData->login, $userData->password);
    if (false === $user_id) {
      return 401; //TODO: Return Response object
    }
    $token = self::genToken();
    try {
      $sth = $this->pdo->prepare('
        INSERT INTO `tokens`(
          `token`, `user_id`,
          `ip_address`, `create_date`
        ) VALUES(
          :token, :user_id,
          :ip_address, :create_date
        );
      ');
      $sth->execute(array(
        'token'       => $token,
        'user_id'     => $user_id,
        'ip_address'  => $_SERVER['REMOTE_ADDR'], //TODO:Do better
        'create_date' => time()
      ));
      return $token;
      //FIXME: Return 201 or 204
    }
    catch (Exception $e) {
      error_log ('Token::put: ' . $e->getMessage());
      //TODO: Return 400?
    }
  }

  public function delete($id) {
    //FIXME: Check user permissions
    try {
      $sth = $this->pdo->prepare('
        DELETE FROM `tokens` WHERE `id` LIKE :id
      ');
      $sth->execute(array(
        'id'          => $id,
      ));
      //FIXME: Return 204
    }
    catch (Exception $e) {
      error_log ('Token::delete: ' . $e->getMessage());
    }
  }

  protected static function genToken() {
    $bytes = openssl_random_pseudo_bytes(42);
    return bin2hex($bytes);
  }
}
?>