<?php

namespace App\Services\Report\Traits;

use App\Helpers\CustomFPDF;
use App\Storage\Storage;

trait BasicHeaderDocumentTrait
{
  protected function buildCommonHeader(CustomFPDF $pdf, array $data, string $size)
  {
    if (in_array(strtoupper($size), ['A4', 'A5'])) {
      return $this->buildCommonHeaderA4($pdf, $data);
    }

    if (strtoupper($size) == 'TICKET') {
      return $this->buildCommonHeaderTicket($pdf, $data);
    }
  }

  private function buildCommonHeaderA4(CustomFPDF $pdf, $data)
  {
    // Margin
    $marginLeft = 12;
    $marginTop = 12;
    $pageWidth = $this->pdf->GetPageWidth() - ($marginLeft + $marginLeft);
    $this->pdf->SetMargins($marginLeft, $marginTop, $marginLeft);

    // Config
    $gutter = 2;
    $clearCollapse = 1.5;
    $rightRectWidth = 80;
    $rightRectX = ($pageWidth - $rightRectWidth) + $marginLeft;
    $leftRectWidth = ($pageWidth - ($rightRectWidth + $gutter));
    $leftRectX = $marginLeft;
    $fontFamilyName = "Calibri";
    $fontSize = 9;
    $lineHeight = 5;

    // Init
    $this->pdf->AliasNbPages();
    $this->pdf->AddPage();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize + 2);
    $this->pdf->SetLineWidth(0.1);

    // Company Identity
    if (isset($data['logoLarge']) && $data['logoLarge'] != '') {
      $fileDirectory = Storage::disk('public')->getRootDir() . $data['logoLarge'];
      if (file_exists($fileDirectory)) {
        $this->pdf->Image($fileDirectory, $marginLeft, $marginTop, 0, 16);
        $this->pdf->SetXY($marginLeft, $marginTop + 16);
      } else {
        $this->pdf->SetXY($marginLeft, $marginTop + 14);
      }
    } else {
      $this->pdf->SetFont($fontFamilyName, 'B', $fontSize + 8);
      $this->pdf->Cell(0, 5, ($data['companyCommercialReason']));
      $this->pdf->SetXY($marginLeft, $marginTop + 14);
      $this->pdf->Ln();
    }

    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize + 2);
    $this->pdf->MultiCell($rightRectX - $marginLeft, $lineHeight - 2, ($data['businessSocialReason']));
    // $this->pdf->Ln();

    $companyContent = $data['businessAddress']  . PHP_EOL;
    $companyContent .= $data['businessLocation'] . PHP_EOL;
    $companyContent = strtoupper($companyContent);
    $this->pdf->SetFont($fontFamilyName, '', $fontSize - 1);
    $this->pdf->MultiCell(0, $lineHeight / 1.8, $companyContent);

    $this->pdf->MultiCell(0, $lineHeight / 1.8, $data['headerCustom']);
    $this->pdf->Ln();

    $beforeY = $this->pdf->GetY();

    // Content invoice
    $this->pdf->SetFillColor(200,200,200);
    $this->pdf->Rect($rightRectX, $marginTop, 2, 20, 'F');

    $this->pdf->SetXY($rightRectX + 5, $marginTop + 4);
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize + 2);

    $saleContent = sprintf('RUC: %s', $data['businessRuc']) . PHP_EOL;
    $saleContent .= sprintf('%s', $data['documentType']) . PHP_EOL;
    $saleContent .= sprintf('%s-%08d', $data['serie'], $data['correlative']);

    $this->pdf->Rect($rightRectX + 70, $marginTop, 10, 20, 'F');

    $this->pdf->MultiCell(0, 4, $saleContent, 0, 'L');

    return $beforeY;
  }

  private function buildCommonHeaderTicket(CustomFPDF $pdf, $data)
  {
    // Margin
    $marginLeft = 3;
    $marginTop = 3;
    $pageWidth = $pdf->GetPageWidth() - ($marginLeft + $marginLeft);
    $pdf->SetMargins($marginLeft, $marginTop, $marginLeft);

    // Config
    $fontFamilyName = "Calibri";
    $fontSize = 9;
    $lineHeight = 3.5;

    // Init
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetLineWidth(0.1);


    // Company Identity
    if (isset($data['logoLarge']) && $data['logoLarge'] != '') {
      $fileDirectory = Storage::disk('public')->getRootDir() . $data['logoLarge'];
      if (file_exists($fileDirectory)) {
        $pdf->Image($fileDirectory, $marginLeft + 3, $marginTop, 0, 15);
        $pdf->SetXY($marginLeft, $marginTop + 15);
      } else {
        $pdf->SetXY($marginLeft, $marginTop + 15);
      }
    } else {
      $pdf->SetFont($fontFamilyName, 'B', $fontSize + 8);
      $pdf->Cell(0, 5, ($data['companyCommercialReason']));
      $pdf->SetXY($marginLeft, $marginTop + 15);
      $pdf->Ln();
    }

    // Company
    // $pdf->SetFont($fontFamilyName, "B", $fontSize + 7);
    // $pdf->MultiCell(0, $lineHeight + 1, $data['businessCommercialReason'], 0, 'C');

    $pdf->SetFont($fontFamilyName, "B", $fontSize);
    $pdf->MultiCell(0, $lineHeight, $data['businessSocialReason'], 0, 'C');
    $pdf->SetFont($fontFamilyName, "", $fontSize);

    $addressContent = $data['businessAddress']  . PHP_EOL;
    $addressContent .= $data['businessLocation'] . PHP_EOL;
    $addressContent = strtoupper($addressContent);
    $pdf->MultiCell(0, $lineHeight, $addressContent, 0, 'C');
    $pdf->SetFont($fontFamilyName, "B", $fontSize);
    $pdf->MultiCell(0, $lineHeight, "RUC " . $data['businessRuc'], 0, 'C');
    $pdf->SetFont($fontFamilyName, "", $fontSize - 1);

    $pdf->MultiCell(0, $lineHeight, $data['headerCustom'], 0, 'C');
    $pdf->SetFont($fontFamilyName, "B", $fontSize);

    // Invoice
    $saleContent = sprintf('%s', $data['documentType']) . PHP_EOL;
    $saleContent .= sprintf('%s-%08d', $data['serie'], $data['correlative']);
    $pdf->MultiCell(0, $lineHeight, $saleContent, 0, 'C');
    $pdf->Ln(3);
  }
}
