<?php

namespace App\Entity\Models;

use App\Core\BaseModel;
use App\Entity\Database\Database;

class LogException extends BaseModel
{
  public function __construct()
  {
    parent::__construct('log_exceptions', 'id', Database::getInstance()->getConnection());
  }
}
