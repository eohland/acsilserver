<?php
namespace Utils;

use Utils\DBCtrl;
use PDO;
use Exception;

class Authenticate {
  public static function auth($login, $password) {
    $pdo = DBCtrl::getDB();
    //TODO: Use User class
    $sth = $pdo->prepare('
      SELECT `id`
      FROM `users`
      WHERE (`login` LIKE :login
        OR  `email` LIKE :login)
      AND `password` LIKE :password;
    ');
    $sth->execute(array(
      'login'    => $login,
      'password' => $password
    ));
    return $sth->fetch(PDO::FETCH_COLUMN);
  }

  public static function encodePassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public static function verifyPassword($password, $hash) {
    return password_verify($password , $hash);
  }

  public static function isAuth() {
    $token = self::getHeader('Authorization');
    $user_id = self::authByToken($token);
    if ($user_id >= 1)
      return $user_id;
    return false;
  }

  public static function authByToken($token) {
    $pdo = DBCtrl::getDB();
    //TODO: Use User class
    $sth = $pdo->prepare('
      SELECT `user_id`
      FROM `tokens`
      WHERE `token` LIKE :token;
    ');
    $sth->execute(array(
      'token'    => $token,
    ));
    return $sth->fetch(PDO::FETCH_COLUMN);
  }

  protected static function getHeader($h) {
    if (!function_exists('getallheaders')) { // nginx
      $header = strtoupper($h);
      $header = str_replace('-', '_', $header);
      $header = 'HTTP_' . $header;
      if (array_key_exists($header, $_SERVER))
        return $_SERVER[$header];
      return NULL;
    }
    $headers = getallheaders();
    if (false === $headers) {
      throw Exception('Authenticate::getHeader: getallheaders() failed.');
    }
    return @$headers[$h];
  }
}
?>
