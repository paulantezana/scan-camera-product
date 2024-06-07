<?php

namespace App\Services\Report\Factories\ReferralGuide;

use App\Services\Report\Factories\AbstractPDFReport;
use App\Services\Report\Traits\BasicHeaderTrait;
use Paulantezana\Fqrcode\QRcode;

class ReferralGuideBasicPDF extends AbstractPDFReport
{
  use BasicHeaderTrait;

  public function __construct()
  {
    parent::__construct();
    $this->diskName = "local";
    $this->temporal = false;
  }

  public function buildA4(array $data)
  {
    $this->buildA4A5($data, 'A4');
  }

  public function buildA5(array $data)
  {
    $this->buildA4A5($data, 'A5');
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
  }

  private function buildA4A5(array $data, string $pdfSize)
  {
    // Margin
    $marginLeft = 12;
    $marginTop = 12;
    $pageWidth = $this->pdf->GetPageWidth() - ($marginLeft + $marginLeft);
    $this->pdf->SetMargins($marginLeft, $marginTop, $marginLeft);

    // Config
    $gutter = 2;
    $clearCollapse = 1.5;
    $rightRectWidth = 60;
    $rightRectX = ($pageWidth - $rightRectWidth) + $marginLeft;
    $leftRectWidth = ($pageWidth - ($rightRectWidth + $gutter));
    $leftRectX = $marginLeft;
    $fontFamilyName = "Calibri";
    $fontSize = 9;
    $lineHeight = 3.3;

    // Header
    $beforeY = $this->buildCommonHeader($this->pdf, $data, $pdfSize);
    $this->pdf->Ln(10);

    // ===============================================================================
    // DESTINATARIO
    $beforeY = $this->pdf->GetY();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell(0, $lineHeight, "DESTINATARIO");

    $this->cellRow($data['documentNumberInitial'] . ':', $data['documentNumber'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('DENOMINACIÓN:', $data['socialReason'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->pdf->Ln();

    $currentY = $this->pdf->GetY();
    $this->pdf->RoundedRect($marginLeft, $beforeY - 2, $pageWidth, $currentY - $beforeY, 2);
    $this->pdf->Ln();

    // ===============================================================================
    // DATOS DEL TRASLADO
    $beforeY = $this->pdf->GetY();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell(0, $lineHeight, "DATOS DEL TRASLADO");

    $this->cellRow('FECHA EMISIÓN:', $data['dateOfIssue'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('FECHA INICIO DE TRASLADO:', $data['transferStartDate'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('MOTIVO DE TRASLADO:', $data['transferReason'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('MODALIDAD DE TRANSPORTE:', $data['transportType'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('PESO BRUTO TOTAL (KGM):', $data['totalGrossWeight'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('NÚMERO DE BULTOS:', $data['numberPackages'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->pdf->Ln();

    $currentY = $this->pdf->GetY();
    $this->pdf->RoundedRect($marginLeft, $beforeY - 2, $pageWidth, $currentY - $beforeY, 2);
    $this->pdf->Ln();

    // ===============================================================================
    // DATOS DEL TRANSPORTE
    $beforeY = $this->pdf->GetY();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell(0, $lineHeight, "DATOS DEL TRANSPORTE");

    $this->cellRow('TRANSPORTISTA:', $data['carrierDescription'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('VEHÍCULO PRINCIPAL:', $data['vehiclePlateNumber'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('VEHÍCULOS SECUNDARIOS:', $data['vehiclePlateNumberAditional'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('CONDUCTOR PRINCIPAL:', $data['driverDescription'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('CONDUCTORES SECUNDARIOS:', $data['driverDescriptionAditional'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('LICENCIA DE CONDUCIR DEL CONDUCTOR PRINCIPAL:', $data['driverLicence'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('LICENCIA DE CONDUCIR DE LOS CONDUCTORES SECUNDARIOS:', $data['driverLicenceAditional'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->pdf->Ln();

    $currentY = $this->pdf->GetY();
    $this->pdf->RoundedRect($marginLeft, $beforeY - 2, $pageWidth, $currentY - $beforeY, 2);
    $this->pdf->Ln();

    // ===============================================================================
    // DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA
    $beforeY = $this->pdf->GetY();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell(0, $lineHeight, "DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA");

    $this->cellRow('PUNTO DE PARTIDA:', $data['startLocationDescription'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->cellRow('PUNTO DE LLEGADA:', $data['arrivalLocationDescription'], $fontFamilyName, $fontSize, $lineHeight, $pageWidth);
    $this->pdf->Ln();

    $currentY = $this->pdf->GetY();
    $this->pdf->RoundedRect($marginLeft, $beforeY - 2, $pageWidth, $currentY - $beforeY, 2);
    $this->pdf->Ln();

    // ===============================================================================
    // ITEMS
    $this->pdf->SetTableWidths([20, 20, 100, 20, 20]);
    $this->pdf->SetTableHAligns(['C', 'C', 'C', 'L', 'R']);

    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->TableRow(['NRO', 'CÓD', 'DESCRIPCIÓN', 'U/M', 'CANTIDAD'], $lineHeight, true, 'H');
    $this->pdf->SetFont($fontFamilyName, '');

    foreach ($data['itemList'] as $key => $row) {
      $this->pdf->TableRow([
        $key + 1,
        $row['productCode'],
        $row['description'],
        $row['unitMeasureCode'],
        $row['quantity'],
      ], $lineHeight, false, 'H');
    }
    $this->pdf->Ln();

    // ===============================================================================
    // INVOICES
    $this->pdf->SetTableWidths([50, 50]);
    $this->pdf->SetTableHAligns(['C', 'C']);

    foreach ($data['invoiceList'] as $row) {
      $this->pdf->TableRow([
        'DOCUMENTOS RELACIONADOS',
        $row['documentType'] . ' ' .  $row['serie'] . ' ' . $row['number'],
      ], $lineHeight, false, 'V'); // V, H, R
    }

    if (strlen($data['carrierMtcNumber']) > 0) {
      $this->pdf->TableRow(['MTC: ', $data['carrierMtcNumber']], $lineHeight, false, 'R');
    }
    $this->pdf->Ln();

    // ===============================================================================
    // FOOTER
    $beforeY = $this->pdf->GetY();
    $this->pdf->MultiCell(0, $lineHeight, sprintf('Representación impresa de %s, para ver el documento visita', strtoupper($data['documentType'])), 0, 'L');
    $this->pdf->SetFont($fontFamilyName, 'B');
    // $this->pdf->MultiCell(0, $lineHeight, HOST . PORT . URL_PATH . '/see/buscar/' .  $data['companyDocumentNumber'], 0, 'L');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->WriteHTML($data['footerCustom'], 4);
    $this->pdf->Ln();

    $rightRectWidth = $pdfSize === 'A5' ? 27 : 35;
    $rightRectX = ($pageWidth - $rightRectWidth) + $marginLeft;
    $leftRectWidth = ($pageWidth - ($rightRectWidth + $gutter));
    $leftRectX = $marginLeft;

    $this->pdf->RoundedRect($leftRectX, $beforeY - 2, $leftRectWidth, $rightRectWidth, 2);

    // ===============================================================================
    // QR Code
    $qrCode = $this->GenerateQRCode([
      'businessRuc' => $data['businessRuc'],
      'documentTypeCode' => $data['documentTypeCode'],
      'serie' => $data['serie'],
      'correlative' => $data['correlative'],
      'dateOfIssue' => $data['dateOfIssue'],
    ]);
    $qrCode->disableBorder();
    $qrCode->displayFPDF($this->pdf, $rightRectX + 2, $beforeY, $rightRectWidth - 4);

    $this->pdf->RoundedRect($rightRectX, $beforeY - 2, $rightRectWidth, $rightRectWidth, 2);
  }

  private function cellRow($key, $value, $fontFamilyName, $fontSize, $lineHeight, $pageWidth)
  {
    $prevY = $this->pdf->GetY();
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell(60, $lineHeight, $key, 0, 'R');
    $maxY = $this->pdf->GetY();

    $this->pdf->SetXY(60 + 12, $prevY);
    $this->pdf->SetFont($fontFamilyName, '', $fontSize);
    $this->pdf->MultiCell($pageWidth - 60, $lineHeight, $value);
    $newMaxY = $this->pdf->GetY();

    $this->pdf->SetY($maxY > $newMaxY ? $maxY : $newMaxY);
  }

  private function group()
  {
  }

  private function generateQRCode($qrData)
  {
    $qrDataStr = sprintf(
      "%s|%s|%s|%s|%s",
      $qrData['businessRuc'],
      $qrData['documentTypeCode'],
      $qrData['serie'],
      $qrData['correlative'],
      $qrData['dateOfIssue'],
    );

    // mb_convert_encoding($string, 'UTF-8');
    return new QRcode(utf8_encode($qrDataStr), 'Q');
  }
}
