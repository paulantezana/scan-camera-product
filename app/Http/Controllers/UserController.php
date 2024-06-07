<?php

namespace App\Http\Controllers;

use App\Core\BaseController;
use App\Entity\Models\AppUser;
use App\Exceptions\ValidationException;
use App\Http\Models\Result;
use App\Http\Request;
use App\Http\Response;

class UserController extends BaseController
{
  public function login(Request $request, Response $response)
  {
    return $response->view('login', [], 'site');
  }

  public function loginValidate(Request $request, Response $response)
  {
    $res = new Result();
    $res->userType = 'site';


    $body = $request->getBody();

    // -----------------------------------------------------------------
    // Validate
    if (!isset($body['email']) || !isset($body['password'])) {
      throw new ValidationException('Los campos usuario y contraseña son requeridos');
    }

    $email = htmlspecialchars($body['email']);
    $password = htmlspecialchars($body['password']);

    if (empty($email) || empty($password)) {
      throw new ValidationException('Los campos usuario y contraseña son requeridos');
    }

    // -----------------------------------------------------------------
    // Login
    $configUserModel = new AppUser();
    $loginUser = $configUserModel->login($email);
    if (!password_verify($password, $loginUser['password'])) {
      throw new ValidationException('El nombre de usuario y o contraseña es incorrecta.');
    }

    // -----------------------------------------------------------------
    // Init app
    $responseApp = $this->initApp($loginUser);
    if (!$responseApp->success) {
      session_destroy();
      throw new ValidationException($responseApp->message);
    }

    $res->success = true;
    return $response->json($res);
  }

  public function logout(Request $request, Response $response)
  {
    session_destroy();
    $this->redirect('/user/login');
  }

  private function initApp($user)
  {
    $res = new Result();
    try {
      // -----------------------------------------------------------------
      // Seta data users
      $_SESSION[SESS_USER] = [
        'id' => $user['id'],
        'user_name' => $user['user_name'],
        'email' => $user['email'],
        'avatar' => $user['avatar'] ?? '',
      ];
      $res->success = true;
    } catch (\Exception $e) {
      $res->message = $e->getMessage();
    }
    return $res;
  }
}
