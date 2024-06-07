<?php

namespace App\Services\Report;


class ReportManager
{
  private $generator;

  public function __construct(string $type, string $strategy, string $outputType)
  {
    $this->generator = ReportFactory::create($type, $strategy, $outputType);
  }

  public function create(array $data, string $size)
  {
    return $this->generator->build($data, $size);
  }
}
