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
}
?>
