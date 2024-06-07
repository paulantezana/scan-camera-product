<?php

namespace App\Services\Report\Factories\ReportGlobal;

use App\Services\Report\Factories\AbstractPDFReport;
use App\Services\Report\Traits\BasicHeaderDocumentTrait;

class ReportGlobalBasicPDF extends AbstractPDFReport
{
  use BasicHeaderDocumentTrait;

  public function __construct()
  {
    parent::__construct();
    $this->diskName = "public";
    $this->temporal = true;
  }

  public function buildA4(array $data)
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
    $lineHeight = 4;

    $tableHeaderBg = [201, 201, 201];
    $tableHeaderBgAlt = [240, 240, 240];
    // $tableHeaderColor = [201, 201, 201];
    // // Header
    // $this->pdf->AliasNbPages();
    // $this->pdf->AddPage();

    // Header
    $beforeY = $this->buildCommonHeader($this->pdf, $data, 'A4');
    $this->pdf->SetY($beforeY);

    // Title Ventas
    // $this->pdf->SetFillColor(230, 78, 98);
    // $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    // $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'VENTAS',0,'C',true);
    // $beforeY = $this->pdf->GetY();

    // ===================================================================================
    // ===================================================================================
    // Title Ventas
    $this->pdf->SetFillColor($tableHeaderBg[0], $tableHeaderBg[1], $tableHeaderBg[2]);
    $this->pdf->SetDrawColor(201, 201, 201);
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'VENTAS', 0, 'C', true);
    $beforeY = $this->pdf->GetY();

    // Left
    $this->pdf->SetFillColor($tableHeaderBgAlt[0], $tableHeaderBgAlt[1], $tableHeaderBgAlt[2]);
    $this->tableInColumn(
      [
        ['Ventas a contado', $data['salePaidCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['salePaidTotal'])],
        ['Ventas a crédito', $data['saleCreditCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['saleCreditTotal'])],
        ['Total', $data['saleCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['saleTotal'])],
      ],
      [
        'x' => $marginLeft,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Right
    $beforeY = $this->tableInColumn(
      [
        ['Ventas a anuladas', $data['salePaidCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['salePaidCanceledTotal'])],
        ['Ventas a crédito anuladas', $data['saleCreditCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['saleCreditCanceledTotal'])],
        ['Total', $data['saleCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['saleCanceledTotal'])],
      ],
      [
        'x' => $marginLeft + 70 + $gutter,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Diference
    $beforeY = $this->tableInColumnDiference(
      [
        ['', 'DIFERENCIA'],
        [$data['saleDiferenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['saleDiferenceTotal'])],
      ],
      [
        'x' => $marginLeft + 140 + ($gutter * 2),
        'y' => $beforeY - ($lineHeight * 2),
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );
    $this->pdf->Ln();

    // ===================================================================================
    // ===================================================================================
    // Title Compras
    $this->pdf->SetFillColor($tableHeaderBg[0], $tableHeaderBg[1], $tableHeaderBg[2]);
    $this->pdf->SetDrawColor(201, 201, 201);
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'COMPRAS', 0, 'C', true);
    $beforeY = $this->pdf->GetY();

    // Left
    $this->pdf->SetFillColor($tableHeaderBgAlt[0], $tableHeaderBgAlt[1], $tableHeaderBgAlt[2]);
    $this->tableInColumn(
      [
        ['Compras a contado', $data['purchasePaidCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchasePaidTotal'])],
        ['Compras a crédito', $data['purchaseCreditCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseCreditTotal'])],
        ['Total', $data['purchaseCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseTotal'])],
      ],
      [
        'x' => $marginLeft,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Right
    $beforeY = $this->tableInColumn(
      [
        ['Compras a anuladas', $data['purchaseCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseCanceledTotal'])],
        ['Compras a crédito anuladas', $data['purchaseCreditCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseCreditCanceledTotal'])],
        ['Total', $data['purchaseCanceledCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseCanceledTotal'])],
      ],
      [
        'x' => $marginLeft + 70 + $gutter,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Diference
    $beforeY = $this->tableInColumnDiference(
      [
        ['', 'DIFERENCIA'],
        [$data['purchaseDiferenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['purchaseDiferenceTotal'])],
      ],
      [
        'x' => $marginLeft + 140 + ($gutter * 2),
        'y' => $beforeY - ($lineHeight * 2),
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );
    $this->pdf->Ln();

    // ===================================================================================
    // ===================================================================================
    // Title Entradas / Salidas
    $this->pdf->SetFillColor($tableHeaderBg[0], $tableHeaderBg[1], $tableHeaderBg[2]);
    $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'ENTRADAS / SALIDAS', 0, 'C', true);
    $beforeY = $this->pdf->GetY();

    // Left
    $this->pdf->SetFillColor($tableHeaderBgAlt[0], $tableHeaderBgAlt[1], $tableHeaderBgAlt[2]);
    $this->tableInColumn(
      [
        ['Entradas / Ventas', $data['movementSaleIncomeCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementSaleIncomeTotal'])],
        ['Entradas / Crédito recup.', $data['movementSaleCreditIncomeCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementSaleCreditIncomeTotal'])],
        ['Entradas / Compras anul.', $data['movementPurchaseCancelCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementPurchaseCancelTotal'])],
        ['Entradas / Movimiento', $data['movementMovIncomeCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementMovIncomeTotal'])],
        ['Total', $data['movementIncomeCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementIncomeTotal'])],
      ],
      [
        'x' => $marginLeft,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Right
    $beforeY = $this->tableInColumn(
      [
        ['Salidas / Compras', $data['movementPurchaseExpenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementPurchaseExpenceTotal'])],
        ['Salidas / Pago de crédito', '0.00', '0.00'],
        ['Salidas / Ventas anul.', $data['movementSaleCancelCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementSaleCancelTotal'])],
        ['Salidas / Movimiento', $data['movementMovExpenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementMovExpenceTotal'])],
        ['Total', $data['movementExpenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementExpenceTotal'])],
      ],
      [
        'x' => $marginLeft + 70 + $gutter,
        'y' => $beforeY,
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    // Diference
    $beforeY = $this->tableInColumnDiference(
      [
        ['', 'DIFERENCIA'],
        [$data['movementDiferenceCount'], sprintf("%s %.2f", $data['currencySymbol'], $data['movementDiferenceTotal'])],
      ],
      [
        'x' => $marginLeft + 140 + ($gutter * 2),
        'y' => $beforeY - ($lineHeight * 2),
        'lineHeight' => $lineHeight,
        'fontFamilyName' => $fontFamilyName,
      ]
    );

    $this->pdf->Ln();

    // // ===================================================================================
    // // ===================================================================================
    // // Title Utilidades
    // $this->pdf->SetFillColor($tableHeaderBg[0], $tableHeaderBg[1], $tableHeaderBg[2]);
    // $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    // $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'UTILIDADES', 0, 'C', true);
    // $this->pdf->Ln();

    // // ===================================================================================
    // // ===================================================================================
    // // Title Documentos
    // $this->pdf->SetFillColor($tableHeaderBg[0], $tableHeaderBg[1], $tableHeaderBg[2]);
    // $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    // $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'DOCUMENTOS', 0, 'C', true);
    // $this->pdf->Ln();

    // // Title Notas De Débito
    // $this->pdf->SetFillColor(230, 78, 98);
    // $this->pdf->SetTextColor(255, 255, 255);
    // $this->pdf->SetFont($fontFamilyName, 'B', $fontSize);
    // $this->pdf->MultiCell($pageWidth, $lineHeight + 1, 'INVENTARIOS Y CRÉDITOS', 0, 'C', true);
    // $this->pdf->Ln();
  }

  public function buildA5(array $data)
  {
  }

  public function buildTICKET(array $data)
  {
  }

  private function tableInColumn(array $data, array $options)
  {
    // Right
    $this->pdf->SetTableWidths([42, 10, 18]);
    $this->pdf->SetTableHAligns(['L', 'R', 'R']);

    $this->pdf->SetFont($options['fontFamilyName'], '');
    $this->pdf->SetXY($options['x'], $options['y']);
    foreach ($data as $key => $row) {
      $this->pdf->SetX($options['x']);
      if ($key >= count($data) - 1) {
        $this->pdf->SetFont($options['fontFamilyName'], 'B');
        $this->pdf->TableRow([$row[0], $row[1], $row[2]], $options['lineHeight'], true, 'H');
      } else {
        $this->pdf->TableRow([$row[0], $row[1], $row[2]], $options['lineHeight'], false, 'H');
      }
    }

    return $this->pdf->GetY();
  }

  private function tableInColumnDiference(array $data, array $options)
  {
    // Right
    $this->pdf->SetTableWidths([16, 26]);
    $this->pdf->SetTableHAligns(['R', 'R']);

    $this->pdf->SetFont($options['fontFamilyName'], 'B');
    $this->pdf->SetXY($options['x'], $options['y']);
    foreach ($data as $key => $row) {
      $this->pdf->SetX($options['x']);
      if ($key >= count($data) - 1) {
        $this->pdf->TableRow([$row[0], $row[1]], $options['lineHeight'], true, 'H');
      } else {
        $this->pdf->TableRow([$row[0], $row[1]], $options['lineHeight'], false, 'H');
      }
    }

    return $this->pdf->GetY();
  }
}
