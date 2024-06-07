<?php

namespace App\Http\Controllers;

use App\Core\BaseController;
use App\Http\Request;
use App\Http\Response;

class PageController extends BaseController
{
  public function home()
  {
    $this->redirect('/admin');
  }

  public function ayuda(Request $request, Response $response)
  {
    return $response->view('help', [], 'site');
  }
}
