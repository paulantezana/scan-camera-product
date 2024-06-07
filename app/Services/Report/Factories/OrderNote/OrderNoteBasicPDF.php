<?php

namespace App\Services\Report\Factories\OrderNote;

use App\Services\Report\Factories\AbstractPDFReport;
use App\Services\Report\Traits\BasicHeaderTrait;

class OrderNoteBasicPDF extends AbstractPDFReport
{
  use BasicHeaderTrait;

  public function buildA4(array $data)
  {
  }

  public function buildA5(array $data)
  {
  }

  public function buildTICKET(array $data)
  {
    // Margin
    $marginLeft = 3;
    $marginTop = 3;
    $pageWidth = $this->pdf->GetPageWidth() - ($marginLeft + $marginLeft);
    $this->pdf->SetMargins($marginLeft, $marginTop, $marginLeft);

    // Config
    $fontFamilyName = "Calibri";
    $fontSize = 9;
    $lineHeight = 3.5;

    $this->buildCommonHeader($this->pdf, $data, 'TICKET');

    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->Cell(35, 4, 'CLIENTE: ', 0, 0);
    $this->pdf->Ln();

    $this->pdf->WriteHTML(sprintf(
      "<b>%s: </b> %s </br> %s </br> %s",
      strtoupper($data['providerDocumentCode']),
      strtoupper($data['providerDocumentNumber']),
      strtoupper($data['providerSocialReason']),
      strtoupper($data['providerFiscalAddress'])
    ), $lineHeight);
    $this->pdf->Ln($lineHeight);

    // Date
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(28, $lineHeight, 'FECHA EMISIÓN');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(": %s", $data['dateOfIssue']), 0, 'L');

    // Currency
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(28, $lineHeight, 'MONEDA');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(': %s', strtoupper($data['currencyDescription'])), 0, 'L');

    // Invoice Detail
    $this->pdf->SetTableWidths([40, 12, 17]);
    $this->pdf->SetTableHAligns(['L', 'R', 'R']);

    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->TableRow(['[CANT.] DESCRIPCIÓN', 'P/U', 'IMPORTE'], 7, false, 'H');
    $this->pdf->SetFont($fontFamilyName, '');

    foreach ($data['itemList'] as $key => $row) {
      $this->pdf->TableRow([
        sprintf("[%s] %s", ($row['quantity']), $row['productDescription']),
        $row['unitPrice'],
        $row['total'],
      ], 4, false, 'H');
    }
    $this->pdf->Ln(2);

    // Invoice Detail resume
    $this->pdf->SetTableWidths([40, 12, 17]);
    $this->pdf->SetTableHAligns(['R', 'R', 'R']);

    $currencySymbol = $data['currencySymbol'];
    $this->pdf->SetFont($fontFamilyName, 'B');
    if ($data['totalDiscount'] > 0) {
      $this->pdf->TableRow(['DESCUENTO', $currencySymbol, $data['totalDiscount']], $lineHeight, false, '');
    }

    $this->pdf->TableRow(['GRAVADA', $currencySymbol, $data['totalTaxed']], $lineHeight, false, '');

    $this->pdf->TableRow(['TOTAL', $currencySymbol, $data['total']], $lineHeight, false, '');
    $this->pdf->Ln();

    // Description
    $this->pdf->WriteHTML('<b>IMPORTE EN LETRAS: </b> ' . $data['totalInWord'], $lineHeight);
    $this->pdf->Ln();

    $this->pdf->SetFont($fontFamilyName, '');
    if ($data['observation']) {
      $this->pdf->WriteHTML("<b>OBSERVACIONES: </b>" . $data['observation'], $lineHeight);
      $this->pdf->Ln();
    }
  }
}
