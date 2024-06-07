<?php

namespace App\Core\Models;

class BaseResult
{
  public $success;
  public $error;

  function __construct()
  {
    $this->success = false;
  }

  public function setError(?Error $error): self
  {
    $this->error = $error;

    return $this;
  }
}
