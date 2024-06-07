<?php

namespace App\Services\Report\Base;

use App\Helpers\CustomFPDF;

class CustomFPDFTICKET extends CustomFPDF
{
  private $valid = false;

  public function SetValid($valid): void
  {
    $this->valid = $valid;
  }

  function header()
  {
    //Put the watermark
    if (!$this->valid) {
      $this->SetFont('Arial', 'B', 24);
      $this->SetTextColor(233, 80, 101);
      $this->RotatedText(11, $this->GetPageHeight() / 2.5, 'SIN VALOR LEGAL', 45);
    }
  }

  function Footer()
  {
    // Page Number
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor(100, 100, 100);
    $this->WriteHTML('<p align="center">Emitido desde ' . $_ENV['APP_AUTHOR'] . '</p><p align="center">Contrata un Sistema para tu empresa al ' . $_ENV['APP_PHONE'] . '</p>', 5);
  }
}
