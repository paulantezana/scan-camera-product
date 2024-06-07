<?php

namespace App\Http\Models;

class Result extends \stdClass
{
  public $success;
  public $message;
  public $result;
  public $code;

  function __construct()
  {
    $this->success = false;
    $this->message = '';
    $this->result = null;
  }
}
