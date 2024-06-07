<?php

namespace App\Services\Report;

use InvalidArgumentException;

class ReportFactory
{
  public static function create(string $type, string $format, string $outputType): IReport
  {
    $formatClass = "App\\Services\\Report\\Factories\\" . $type . "\\" . $format . $outputType;

    if (class_exists($formatClass)) {
      return new $formatClass();
    }

    throw new InvalidArgumentException("Invalid report type or format: {$type}, {$format}, {$outputType}");
  }
}
