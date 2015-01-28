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
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
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
  public function put($token, $userData) {
    $user_id = Authenticate::auth($userData->login, $userData->password);
    if (false === $user_id) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
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
      return array('token' => $token);
      //FIXME: Return 201 or 204
      header('HTTP/1.0 201 Created');
    }
    catch (Exception $e) {
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  public function delete($id) {
    //FIXME: Check user permissions
    if (false === Authenticate::isAuth()) {
      //TODO: Use a Response class
      header('HTTP/1.0 401 Unauthorized');
      return array('errorCode'=> 401, 'errorMessage' => 'Not Authorized');
    }
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
      error_log (__METHOD__ . ': ' . $e->getMessage());
      header('HTTP/1.0 503 Service Unavailable');
    }
  }

  protected static function genToken() {
    $bytes = openssl_random_pseudo_bytes(42);
    return bin2hex($bytes);
  }
}
?>
