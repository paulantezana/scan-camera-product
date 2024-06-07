<?php

namespace App\Entity\Models;

use App\Core\BaseModel;
use App\Entity\Database\Database;
use App\Exceptions\ValidationException;

class AppUser extends BaseModel
{
  public function __construct()
  {
    parent::__construct('app_users', 'id', Database::getInstance()->getConnection());
  }

  public function login(string $user)
  {
    $stmt = $this->db->prepare('SELECT us.id, us.user_name, us.email, us.state, us.password
                                FROM app_users AS us
                                WHERE us.email = :email AND us.state = 1 LIMIT 1');
    $stmt->bindParam(':email', $user);
    if (!$stmt->execute()) {
      throw new \Exception($stmt->errorInfo()[2]);
    }

    $dataUser = $stmt->fetch();

    if ($dataUser === false) {
      $stmt = $this->db->prepare('SELECT us.id, us.user_name, us.email, us.state, us.password
                                            FROM app_users as us
                                            WHERE us.user_name = :user_name AND us.state = 1 LIMIT 1');
      $stmt->bindParam(':user_name', $user);

      if (!$stmt->execute()) {
        throw new \Exception($stmt->errorInfo()[2]);
      }
      $dataUser = $stmt->fetch();

      if ($dataUser == false) {
        throw new ValidationException('Usuario no encontrado.');
      }
    }

    if ($dataUser['state'] == '0') {
      throw new ValidationException('Usted no esta autorizado para ingresar al sistema.');
    }

    return $dataUser;
  }
}
