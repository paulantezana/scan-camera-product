<?php

namespace App\Services\Report;

trait FontTrait
{
  protected function setDefaultFontStyles($pdf)
  {
    $pdf->AddFont('Calibri', '', 'Calibri_Regular.php', __DIR__ . '/fonts');
    $pdf->AddFont('Calibri', 'B', 'Calibri_Bold.php', __DIR__ . '/fonts');
    $pdf->AddFont('Calibri', 'I', 'Calibri_Italic.php', __DIR__ . '/fonts');
  }
}
