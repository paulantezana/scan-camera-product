<?php

namespace App\Core\Models;

class Error
{
  public $code;
  public $message;

  public function __construct(?string $code = '', ?string $message = '')
  {
    $this->code = $code;
    $this->message = $message;
  }
}
