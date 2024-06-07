<?php

namespace App\Services\Report;

interface IReport
{
  public function build(array $data, string $size);
}
