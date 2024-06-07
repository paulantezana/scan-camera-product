<?php

namespace App\Entity\Models;

use App\Core\BaseModel;
use App\Entity\Database\Database;

class AppProduct extends BaseModel
{
  public function __construct()
  {
    parent::__construct('app_products', 'id', Database::getInstance()->getConnection());
  }
}
