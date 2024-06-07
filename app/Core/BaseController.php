<?php

namespace App\Core;

class BaseController
{
  protected function redirect(string $url = "")
  {
    header('Location: ' . URL_PATH . $url);
  }
}
