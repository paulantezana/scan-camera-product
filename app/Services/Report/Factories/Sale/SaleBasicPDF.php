<?php

namespace App\Services\Report\Factories\Sale;

use App\Services\Report\Factories\AbstractPDFReport;
use App\Services\Report\Traits\BasicHeaderTrait;
use Paulantezana\Fqrcode\QRcode;

class SaleBasicPDF extends AbstractPDFReport
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

    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->Cell(35, 4, 'CLIENTE: ', 0, 0);
    $this->pdf->Ln();

    $this->pdf->WriteHTML(sprintf(
      "<b>%s: </b> %s </br> %s </br> %s",
      strtoupper($data['customerDocumentCode']),
      strtoupper($data['customerDocumentNumber']),
      strtoupper($data['customerSocialReason']),
      strtoupper($data['customerFiscalAddress'])
    ), $lineHeight);
    $this->pdf->Ln($lineHeight);

    // Date
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(15, $lineHeight, 'F. EMISIÓN');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->Cell(18, $lineHeight, sprintf(": %s", $data['dateOfIssue']));

    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(15, $lineHeight, 'F. DE VENC');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(": %s", substr($data['dateOfDue'], 0, 10)), 0, 'L');

    // Currency
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(15, $lineHeight, 'MONEDA');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->Cell(18, $lineHeight, sprintf(': %s', strtoupper($data['currencyDescription'])));

    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(15, $lineHeight, 'IGV');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(18, $lineHeight, ': ' . $data['percentageIgv'] . ' %', 0, 'L');
    $this->pdf->Ln($lineHeight / 3);

    // Sale Detail
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
    $this->pdf->Ln(1);

    // Sale Detail resume
    $this->pdf->SetTableWidths([40, 12, 17]);
    $this->pdf->SetTableHAligns(['R', 'R', 'R']);

    $currencySymbol = $data['currencySymbol'];
    $this->pdf->SetFont($fontFamilyName, 'B');
    if ($data['totalDiscount'] > 0) {
      $this->pdf->TableRow(['DESCUENTO', $currencySymbol, $data['totalDiscount']], $lineHeight, false, '');
    }

    if ($data['totalPrepayment'] > 0) {
      $this->pdf->TableRow(['ANTICIPO', $currencySymbol, $data['totalPrepayment']], $lineHeight, false, '');
    }

    if ($data['totalExportation'] > 0) {
      $this->pdf->TableRow(['EXPORTACION', $currencySymbol, $data['totalExportation']], $lineHeight, false, '');
    }

    if ($data['totalExonerated'] > 0) {
      $this->pdf->TableRow(['EXONERADA', $currencySymbol, $data['totalExonerated']], $lineHeight, false, '');
    }

    if ($data['totalUnaffected'] > 0) {
      $this->pdf->TableRow(['INAFECTA', $currencySymbol, $data['totalUnaffected']], $lineHeight, false, '');
    }

    $this->pdf->TableRow(['GRAVADA', $currencySymbol, $data['totalTaxed']], $lineHeight, false, '');

    if ($data['totalIsc'] > 0) {
      $this->pdf->TableRow(['ISC', $currencySymbol, $data['totalIsc']], $lineHeight, false, '');
    }

    $this->pdf->TableRow(['IGV ' . $data['percentageIgv'] . ' %', $currencySymbol, $data['totalIgv']], $lineHeight, false, '');

    if ($data['totalFree'] > 0) {
      $this->pdf->TableRow(['GRATUITA', $currencySymbol, $data['totalFree']], $lineHeight, false, '');
    }

    if ($data['totalCharge'] > 0) {
      $this->pdf->TableRow(['OTROS CARGOS', $currencySymbol, $data['totalCharge']], $lineHeight, false, '');
    }

    if ($data['totalPlasticBagTax'] > 0) {
      $this->pdf->TableRow(['ICBPER', $currencySymbol, $data['totalPlasticBagTax']], $lineHeight, false, '');
    }

    $this->pdf->SetFontSize($fontSize + 3);
    $this->pdf->TableRow(['TOTAL', $currencySymbol, $data['total']], $lineHeight, false, '');

    $this->pdf->SetFontSize($fontSize);
    if (strlen($data['detractionCode'] ?? '') > 0) {
      $this->pdf->TableRow(['DETRACCIÓN (' . $data['detractionCode'] . ') ' . $data['detractionPercentage'] . ' %', $currencySymbol, $data['detractionAmount']], $lineHeight, false, '');
      $this->pdf->Ln();
    }

    $this->pdf->SetFontSize($fontSize);
    if ($data['returned'] > 0) {
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->Ln(.5);
      $this->pdf->Line($pageWidth / 3, $this->pdf->GetY(), $pageWidth + $marginLeft, $this->pdf->GetY());
      $this->pdf->Ln(.5);
      $this->pdf->TableRow(['PAGO', $currencySymbol, $data['returned'] + $data['total']], $lineHeight, false, '');
      $this->pdf->TableRow(['VUELTO', $currencySymbol, $data['returned']], $lineHeight, false, '');
      $this->pdf->Ln(.5);
      $this->pdf->Line($pageWidth / 3, $this->pdf->GetY(), $pageWidth + $marginLeft, $this->pdf->GetY());
      $this->pdf->Ln();
    }

    $this->pdf->SetFont($fontFamilyName, 'B');
    if ($data['perceptionAmount'] > 0) {
      $this->pdf->SetTableWidths([10, 10, 25, 23]);
      $this->pdf->SetTableHAligns(['C', 'C', 'C', 'C']);
      $this->pdf->TableRow(['COD', '%', 'PERCEPCION', 'TOTAL PER'], 7, false, 'H');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->TableRow([$data['perceptionCode'], "{$data['perceptionPercentage']} %", "$currencySymbol {$data['perceptionAmount']}", "$currencySymbol {$data['totalWithPerception']}"], 7, false, 'H');
      $this->pdf->Ln(4);
    }

    // Detraction - 40, 12, 17
    if ($data['detractionCode'] === '027') {
      $this->pdf->WriteHTML('<b>Punto de partida: </b> ' . $data['detractionLocationStartPoint'], $lineHeight);
      $this->pdf->Ln();

      $this->pdf->WriteHTML('<b>Punto de llegada: </b> ' . $data['detractionLocationEndPoint'], $lineHeight);
      $this->pdf->Ln();

      $this->pdf->WriteHTML('<b>Valor Referencial: </b> ' . $data['detractionReferralValue'], $lineHeight);
      $this->pdf->Ln();

      $this->pdf->WriteHTML('<b>Carga Efectiva: </b> ' . $data['detractionEffectiveLoad'], $lineHeight);
      $this->pdf->Ln();

      $this->pdf->WriteHTML('<b>Carga Útil: </b> ' . $data['detractionUsefulLoad'], $lineHeight);
      $this->pdf->Ln();

      $this->pdf->WriteHTML('<b>Detalle del Viaje: </b> ' . $data['detractionTravelDetail'], $lineHeight);
      $this->pdf->Ln();
    }

    if ($data['detractionCode'] === '004') {
      $this->pdf->WriteHTML('<b>Matrícula Embarcación: </b> ' . $data['detractionBoatRegistration'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Nombre Embarcación: </b> ' . $data['detractionBoatName'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Tipo Especie vendida: </b> ' . $data['detractionSpeciesKind'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Lugar de descarga: </b> ' . $data['detractionDeliveryAddress'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Cantidad Especie: </b> ' . $data['detractionQuantity'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Fecha de descarga: </b> ' . $data['detractionDeliveryDate'], $lineHeight);
      $this->pdf->Ln();
    }

    if ($data['whitGuide']) {
      $this->pdf->WriteHTML('<b>Motivo de traslado: </b> ' . $data['transferCode'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Modalidad de transporte: </b> ' . $data['transportCode'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Peso bruto total (KGM): </b> ' . $data['totalGrossWeight'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Transportista denominación: </b> ' . $data['carrierDenomination'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Transportista placa: </b> ' . $data['carrierPlateNumber'], $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML('<b>Conductor: </b> ' . $data['driverDenomination'], $lineHeight);
      $this->pdf->Ln();
    }

    // Description
    $this->pdf->WriteHTML('<b>IMPORTE EN LETRAS: </b> ' . $data['totalInWord'], $lineHeight);
    $this->pdf->Ln();

    $this->pdf->SetFont($fontFamilyName, '');
    if ($data['vehiclePlate']) {
      $this->pdf->WriteHTML("<b>PLACA VEHICULO: </b>" . strtoupper($data['vehiclePlate']), $lineHeight);
      $this->pdf->Ln();
    }
    if ($data['term']) {
      $this->pdf->WriteHTML("<b>CONDICIONES DE PAGO: </b>" . strtoupper($data['term']), $lineHeight);
      $this->pdf->Ln();
    }
    if ($data['purchaseOrder']) {
      $this->pdf->WriteHTML("<b>ORDEN DE COMPRA/SERVICIO: </b>" . strtoupper($data['purchaseOrder']), $lineHeight);
      $this->pdf->Ln();
    }
    if ($data['observation']) {
      $this->pdf->WriteHTML("<b>OBSERVACIONES: </b>" . $data['observation'], $lineHeight);
      $this->pdf->Ln();
    }
    if ($data['reasonUpdate']) {
      $this->pdf->WriteHTML("<b>MOTIVO DE EMISIÓN: </b>" . strtoupper($data['reasonUpdate']), $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML("<b>DOCUMENTO RELACIONADO: </b>" . strtoupper($data['reasonUpdateDocument']), $lineHeight);
      $this->pdf->Ln();
    }
    if ($data['guide']) {
      $docRelated = array_reduce(
        $data['guide'],
        function ($old, $next) {
          return $old . $next['document_code'] . ' ' . $next['serie'] . '  ';
        },
        ''
      );
      $this->pdf->WriteHTML("<b>GUIAS: </b>" . $docRelated, $lineHeight);
      $this->pdf->Ln();
    }

    $this->pdf->WriteHTML("<b>FORMA DE PAGO: </b>" . ($data['paymentForm'] == 2 ? '[CREDITO POR PAGAR]' : '[Contado]'), $lineHeight);
    $this->pdf->Ln();

    foreach ($data['paymentList'] as $key => $row) {
      $this->pdf->WriteHTML('<b>' . $row['method'] . ': </b>' . $currencySymbol . ' ' . $row['amount'], $lineHeight);
      $this->pdf->Ln();
    }

    foreach ($data['creditList'] as $key => $row) {
      $this->pdf->WriteHTML('<b>' . $row['indexId'] . ': </b>' . $currencySymbol . ' ' . $row['amount'] . ' ' . $row['dueDate'], $lineHeight);
      $this->pdf->Ln();
    }

    $this->pdf->WriteHTML('<b>Usuario: </b>' . $data['userName'], $lineHeight);
    $this->pdf->Ln();

    if($data['documentTypeCode'] === '00'){
      $this->pdf->WriteHTML('Documento sin valor tributario', $lineHeight);
      $this->pdf->Ln();
    }

    $this->pdf->MultiCell(0, 4, sprintf('Representación impresa de %s, para ver el documento visita', strtoupper($data['documentType'])), 0, 'L');
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->MultiCell(0, 4, HOST . PORT . URL_PATH . '/see/buscar/' . $data['companyDocumentNumber'], 0, 'L');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->WriteHTML($data['footerCustom'], 4);
    $this->pdf->Ln();

    // QR Code
    if($data['documentTypeCode'] !== '00'){
      $QrCode = $this->GenerateQRCode([
        'businessRuc' => $data['businessRuc'],
        'documentTypeCode' => $data['documentTypeCode'],
        'serie' => $data['serie'],
        'correlative' => $data['correlative'],
        'totalIgv' => $data['totalIgv'],
        'total' => $data['total'],
        'dateOfIssue' => $data['dateOfIssue'],
        'customerDocumentCode' => $data['customerDocumentCode'],
        'customerDocumentNumber' => $data['customerDocumentNumber'],
        'digestValue' => $data['digestValue'],
      ]);
      $QrCode->disableBorder();
      $QrCode->displayFPDF($this->pdf, $marginLeft + ($pageWidth / 2) - 16, $this->pdf->GetY(), 32);
      $this->pdf->Ln(32 + 4);
    }
    // $this->pdf->WriteHTML('<div align="center">Emitido desde <b>' . '' . '</b></div>', 4);
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

    // Customer
    $labelWidth = 26;
    $labelX = $marginLeft + $clearCollapse;
    $descriptionWidth = $pageWidth - ($rightRectWidth + $gutter + $clearCollapse + $labelWidth);

    $this->pdf->SetXY($labelX, $beforeY);
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->Cell(35, 4, 'CLIENTE: ', 0, 0);
    $this->pdf->Ln();

    $this->pdf->SetXY($labelX, $this->pdf->GetY());
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell($labelWidth, $lineHeight, 'RUC');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->Cell($descriptionWidth, $lineHeight, ': ' . ($data['customerDocumentNumber']));
    $this->pdf->Ln();

    $this->pdf->SetXY($labelX, $this->pdf->GetY());
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell($labelWidth, $lineHeight, 'DENOMINACIÓN');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell($descriptionWidth, $lineHeight, ': ' . strtoupper($data['customerSocialReason']), 0, 'L');

    $this->pdf->SetXY($labelX, $this->pdf->GetY());
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell($labelWidth, $lineHeight, 'DIRECCIÓN');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell($descriptionWidth, $lineHeight, ': ' . strtoupper($data['customerFiscalAddress']), 0, 'L');

    $this->pdf->RoundedRect($leftRectX, $beforeY - 1, $leftRectWidth, $this->pdf->GetY() - $beforeY + $lineHeight - 1, 2);

    // Description
    $this->pdf->RoundedRect($rightRectX, $beforeY - 1, $rightRectWidth, $this->pdf->GetY() - $beforeY + $lineHeight - 1, 2);

    $beforeYAux = $beforeY;
    $beforeY = $this->pdf->GetY();

    // Date
    $this->pdf->SetXY($rightRectX + 2, $beforeYAux);
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(28, $lineHeight, 'FECHA EMISIÓN');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(": %s", $data['dateOfIssue']), 0, 'L');

    $this->pdf->SetXY($rightRectX + 2, $this->pdf->GetY());
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(28, $lineHeight, 'FECHA DE VENC');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(": %s", $data['dateOfDue']), 0, 'L');

    // Currency
    $this->pdf->SetXY($rightRectX + 2, $this->pdf->GetY());
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->Cell(28, $lineHeight, 'MONEDA');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->MultiCell(0, $lineHeight, sprintf(': %s', strtoupper($data['currencyDescription'])), 0, 'L');

    // Table
    $this->pdf->SetXY($marginLeft, $beforeY + ($lineHeight / 1.3));

    $this->pdf->SetTableWidths([12, 12, 18, 84, 20, 20, 20]);
    $this->pdf->SetTableHAligns(['C', 'C', 'C', 'L', 'R', 'R', 'R']);

    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->TableRow(['CANT', 'UM', 'CÓD', 'DESCRIPCIÓN', 'V/U', 'P/U', 'IMPORTE'], 4, true, 'H');
    $this->pdf->SetFont($fontFamilyName, '');

    foreach ($data['itemList'] as $key => $row) {
      //            $fill = ($key % 2) == 0;
      $discount = $row['discount'] > 0 ? '-' . $row['discount'] : '';
      $this->pdf->TableRow([
        $row['quantity'],
        $row['unitMeasureCode'],
        $row['productCode'],
        $row['productDescription'],
        $row['unitValue'],
        $row['unitPrice'] . $discount,
        $row['total'],
      ], $lineHeight, false, 'H');
    }
    $this->pdf->Ln(0.5);

    //186.00155555556
    $this->pdf->SetTableWidths([133, 20, 33]);
    $this->pdf->SetTableHAligns(['R', 'R', 'R']);

    $currencySymbol = $data['currencySymbol'];
    $this->pdf->SetFont($fontFamilyName, 'B');

    if ($data['totalDiscount'] > 0) {
      $this->pdf->TableRow(['DESCUENTO(-)', $currencySymbol, $data['totalDiscount']], $lineHeight, false, '');
    }

    if ($data['totalPrepayment'] > 0) {
      $this->pdf->TableRow(['ANTICIPO', $currencySymbol, $data['totalPrepayment']], $lineHeight, false, '');
    }

    if ($data['totalExportation'] > 0) {
      $this->pdf->TableRow(['EXPORTACION', $currencySymbol, $data['totalExportation']], $lineHeight, false, '');
    }

    if ($data['totalExonerated'] > 0) {
      $this->pdf->TableRow(['EXONERADA', $currencySymbol, $data['totalExonerated']], $lineHeight, false, '');
    }

    if ($data['totalUnaffected'] > 0) {
      $this->pdf->TableRow(['INAFECTA', $currencySymbol, $data['totalUnaffected']], $lineHeight, false, '');
    }

    $this->pdf->TableRow(['GRAVADA', $currencySymbol, $data['totalTaxed']], $lineHeight, false, '');

    if ($data['totalIsc'] > 0) {
      $this->pdf->TableRow(['ISC', $currencySymbol, $data['totalIsc']], $lineHeight, false, '');
    }

    $this->pdf->TableRow(['IGV ' . $data['percentageIgv'] . ' %', $currencySymbol, $data['totalIgv']], $lineHeight, false, '');

    if ($data['totalFree'] > 0) {
      $this->pdf->TableRow(['GRATUITA', $currencySymbol, $data['totalFree']], $lineHeight, false, '');
    }

    if ($data['totalCharge'] > 0) {
      $this->pdf->TableRow(['OTROS CARGOS', $currencySymbol, $data['totalCharge']], $lineHeight, false, '');
    }

    if ($data['totalPlasticBagTax'] > 0) {
      $this->pdf->TableRow(['ICBPER', $currencySymbol, $data['totalPlasticBagTax']], $lineHeight, false, '');
    }

    $this->pdf->SetFontSize($fontSize + 2);
    $this->pdf->TableRow(['TOTAL', $currencySymbol, $data['total']], $lineHeight, false, '');
    if ($pdfSize === 'A4') {
      $this->pdf->Ln();
    }

    $this->pdf->SetFontSize($fontSize);
    if (strlen($data['detractionCode'] ?? '') > 0) {
      $this->pdf->TableRow(['DETRACCIÓN (' . $data['detractionCode'] . ') ' . $data['detractionPercentage'] . ' %', $currencySymbol, $data['detractionAmount']], $lineHeight, false, '');
      $this->pdf->Ln();
    }

    if ($data['returned'] > 0) {
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->Ln(.5);
      $this->pdf->Line($pageWidth / 2, $this->pdf->GetY(), $pageWidth + $marginLeft, $this->pdf->GetY());
      $this->pdf->Ln(.5);
      $this->pdf->TableRow(['PAGO', $currencySymbol, $data['returned'] + $data['total']], $lineHeight, false, '');
      $this->pdf->TableRow(['VUELTO', $currencySymbol, $data['returned']], $lineHeight, false, '');
      $this->pdf->Ln(.5);
      $this->pdf->Line($pageWidth / 2, $this->pdf->GetY(), $pageWidth + $marginLeft, $this->pdf->GetY());
      $this->pdf->Ln();
    }

    // Perception
    $this->pdf->SetFont($fontFamilyName, 'B');
    if ($data['perceptionAmount'] > 0) {
      $this->pdf->SetTableWidths([20, 40, 40, 40, 46]);
      $this->pdf->SetTableHAligns(['C', 'C', 'C', 'C', 'C']);
      $this->pdf->TableRow(['CODIGO', 'POCENTAGE', 'PERCEPCION', 'BASE IMPONIBLE', 'TOTAL CON PERCEPCION'], 7, true, 'H');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->TableRow([$data['perceptionCode'], "{$data['perceptionPercentage']} %", "$currencySymbol {$data['perceptionAmount']}", "$currencySymbol {$data['perceptionBase']}", "$currencySymbol {$data['totalWithPerception']}"], 5, false, 'H');
      $this->pdf->Ln();
    }

    // Detraction
    if ($data['detractionCode'] === '027') {
      $this->pdf->SetTableWidths([40, 40, 25, 25, 20, 35]);
      $this->pdf->SetTableHAligns(['C', 'C', 'C', 'C', 'C', 'C']);
      $this->pdf->TableRow(['Punto de partida', 'Punto de llegada', 'Valor Referencial', 'Carga Efectiva', 'Carga Útil', 'Detalle del Viaje'], 5, true, 'H');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->TableRow([
        $data['detractionLocationStartPoint'],
        $data['detractionLocationEndPoint'],
        $data['detractionReferralValue'],
        $data['detractionEffectiveLoad'],
        $data['detractionUsefulLoad'],
        $data['detractionTravelDetail'],
      ], 5, false, 'H');
      $this->pdf->Ln();
    }

    if ($data['detractionCode'] === '004') {
      $this->pdf->SetTableWidths([40, 40, 25, 25, 20, 35]);
      $this->pdf->SetTableHAligns(['C', 'C', 'C', 'C', 'C', 'C']);
      $this->pdf->TableRow(['Matrícula Embarcación', 'Nombre Embarcación', 'Tipo Especie vendida', 'Lugar de descarga', 'Cantidad Especie ', 'Fecha de descarga'], 5, true, 'H');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->TableRow([
        $data['detractionBoatRegistration'],
        $data['detractionBoatName'],
        $data['detractionSpeciesKind'],
        $data['detractionDeliveryAddress'],
        $data['detractionQuantity'],
        $data['detractionDeliveryDate'],
      ], 5, false, 'H');
      $this->pdf->Ln();
    }

    // Referral guide
    if ($data['whitGuide']) {
      $this->pdf->SetTableWidths([50, 100]);
      $this->pdf->SetTableHAligns(['R', 'L']);
      $this->pdf->SetTableFonts([
        ['style' => 'B'],
        ['style' => ''],
      ]);

      $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
      $this->pdf->Cell($pageWidth, 5 + 2, 'DATOS DEL TRASLADO', 0, 0, 'L', true);
      $beforeY = $this->pdf->GetY();
      $this->pdf->Ln();
      $this->pdf->TableRow(['MOTIVO DE TRASLADO:', $data['transferCode']], $lineHeight, false, 'H');
      $this->pdf->TableRow(['MODALIDAD DE TRANSPORTE:', $data['transportCode']], $lineHeight, false, 'H');
      $this->pdf->TableRow(['PESO BRUTO TOTAL (KGM):', $data['totalGrossWeight']], $lineHeight, false, 'H');

      $this->pdf->TableRow(['TRANSPORTISTA DENOMINACIÓN:', $data['carrierDenomination']], $lineHeight, false, 'H');
      $this->pdf->TableRow(['TRANSPORTISTA PLACA:', $data['carrierPlateNumber']], $lineHeight, false, 'H');
      $this->pdf->TableRow(['CONDUCTOR:', $data['driverDenomination']], $lineHeight, false, 'H');

      $this->pdf->TableRow(['PUNTO DE PARTIDA:', $data['locationStartPoint']], $lineHeight, false, 'H');
      $this->pdf->TableRow(['PUNTO DE LLEGADA:', $data['locationEndPoint']], $lineHeight, false, 'H');
      $this->pdf->Ln();
    }

    // Description
    if ($pdfSize === 'A4') {
      $beforeY = $this->pdf->GetY();
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, 4, 'IMPORTE EN LETRAS: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, 4, $data['totalInWord'], 0, 'L');
      $this->pdf->RoundedRect($marginLeft, $beforeY - 2, $pageWidth, $this->pdf->GetY() - $beforeY + $lineHeight, 2);
      $this->pdf->Ln();
    }

    if ($data['vehiclePlate']) {
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, $lineHeight, 'PLACA VEHICULO: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, $lineHeight, strtoupper($data['vehiclePlate']), 0, 'L');
      $this->pdf->Ln(1);
    }
    if ($data['term']) {
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, $lineHeight, 'CONDICIONES DE PAGO: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, $lineHeight, strtoupper($data['term']), 0, 'L');
      $this->pdf->Ln(1);
    }
    if ($data['purchaseOrder']) {
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, $lineHeight, 'ORDEN DE COMPRA/SERVICIO: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, $lineHeight, strtoupper($data['purchaseOrder']), 0, 'L');
      $this->pdf->Ln(1);
    }
    if ($data['observation']) {
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, $lineHeight, 'OBSERVACIONES: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, $lineHeight, $data['observation'], 0, 'L');
      $this->pdf->Ln(1);
    }
    if(strlen($data['reasonUpdate'] ?? '') > 1){
      $this->pdf->WriteHTML("<b>MOTIVO DE EMISIÓN: </b>" . strtoupper($data['reasonUpdate']), $lineHeight);
      $this->pdf->Ln();
      $this->pdf->WriteHTML("<b>DOCUMENTO RELACIONADO: </b>" . strtoupper($data['reasonUpdateDocument']), $lineHeight);
      $this->pdf->Ln();
    }

    if ($data['guide']) {
      $docRelated = array_reduce(
        $data['guide'],
        function ($old, $next) {
          return $old . $next['document_code'] . ' ' . $next['serie'] . '  ';
        },
        ''
      );
      $this->pdf->SetFont($fontFamilyName, 'B');
      $this->pdf->Cell(60, $lineHeight, 'GUIAS: ', 0, 0, 'R');
      $this->pdf->SetFont($fontFamilyName, '');
      $this->pdf->MultiCell(0, $lineHeight, trim($docRelated), 0, 'L');
      $this->pdf->Ln(1);
    }

    $this->pdf->WriteHTML("<b>FORMA DE PAGO: </b>" . ($data['paymentForm'] == 2 ? '[CREDITO POR PAGAR]' : '[Contado]'), $lineHeight);
    $this->pdf->Ln();

    $paymentListHtml = '';
    foreach ($data['paymentList'] as $key => $row) {
      $paymentListHtml .= "<b>{$row['method']}: </b> {$currencySymbol} {$row['amount']} {$row['reference']} ";
    }
    if (count($data['paymentList']) > 0) {
      $this->pdf->WriteHTML($paymentListHtml, $lineHeight);
      $this->pdf->Ln(6);
    }

    $creditListHtml = '';
    foreach ($data['creditList'] as $key => $row) {
      $creditListHtml .= "<b>{$row['indexId']}: </b> {$currencySymbol} {$row['amount']} {$row['dueDate']} ";
    }
    if (count($data['creditList']) > 0) {
      $this->pdf->WriteHTML($creditListHtml, $lineHeight);
      $this->pdf->Ln(6);
    }

    $beforeY = $this->pdf->GetY();

    if ($pdfSize === 'A5') {
      $this->pdf->WriteHTML('<b>IMPORTE EN LETRAS: </b>' . $data['totalInWord'], $lineHeight);
      $this->pdf->Ln();
    }

    $this->pdf->WriteHTML('<b>Usuario: </b>' . $data['userName'], $lineHeight);
    $this->pdf->Ln();

    if($data['documentTypeCode'] === '00'){
      $this->pdf->WriteHTML('Documento sin valor tributario', $lineHeight);
      $this->pdf->Ln();
    }

    $this->pdf->MultiCell(0, $lineHeight, sprintf('Representación impresa de %s, para ver el documento visita', strtoupper($data['documentType'])), 0, 'L');
    $this->pdf->SetFont($fontFamilyName, 'B');
    $this->pdf->MultiCell(0, $lineHeight, HOST . PORT . URL_PATH . '/see/buscar/' .  $data['companyDocumentNumber'], 0, 'L');
    $this->pdf->SetFont($fontFamilyName, '');
    $this->pdf->WriteHTML($data['footerCustom'], 4);
    $this->pdf->Ln();

    $rightRectWidth = $pdfSize === 'A5' ? 27 : 35;
    $rightRectX = ($pageWidth - $rightRectWidth) + $marginLeft;
    $leftRectWidth = ($pageWidth - ($rightRectWidth + $gutter));
    $leftRectX = $marginLeft;

    $this->pdf->RoundedRect($leftRectX, $beforeY - 2, $leftRectWidth, $rightRectWidth, 2);

    // QR Code
    if($data['documentTypeCode'] !== '00'){
      $qrCode = $this->GenerateQRCode([
        'businessRuc' => $data['businessRuc'],
        'documentTypeCode' => $data['documentTypeCode'],
        'serie' => $data['serie'],
        'correlative' => $data['correlative'],
        'totalIgv' => $data['totalIgv'],
        'total' => $data['total'],
        'dateOfIssue' => $data['dateOfIssue'],
        'customerDocumentCode' => $data['customerDocumentCode'],
        'customerDocumentNumber' => $data['customerDocumentNumber'],
        'digestValue' => $data['digestValue'],
      ]);
      $qrCode->disableBorder();
      $qrCode->displayFPDF($this->pdf, $rightRectX + 2, $beforeY, $rightRectWidth - 4);

      $this->pdf->RoundedRect($rightRectX, $beforeY - 2, $rightRectWidth, $rightRectWidth, 2);
    }
  }

  private function generateQRCode($qrData)
  {
    $qrDataStr = sprintf(
      "%s | %s | %s | %s | %s | %s | %s | %s | %s | %s",
      $qrData['businessRuc'],
      $qrData['documentTypeCode'],
      $qrData['serie'],
      $qrData['correlative'],
      $qrData['totalIgv'],
      $qrData['total'],
      $qrData['dateOfIssue'],
      $qrData['customerDocumentCode'],
      $qrData['customerDocumentNumber'],
      $qrData['digestValue']
    );

    // mb_convert_encoding($string, 'UTF-8');
    return new QRcode(utf8_encode($qrDataStr), 'Q');
  }
}
