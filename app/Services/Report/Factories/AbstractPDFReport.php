<?php

namespace App\Services\Report\Factories;

use App\Helpers\CustomFPDF;
use App\Helpers\MakeStorageFolder;
use App\Helpers\OpensslUtility;
use App\Services\Report\Base\CustomFPDFA4;
use App\Services\Report\Base\CustomFPDFA5;
use App\Services\Report\Base\CustomFPDFTICKET;
use App\Services\Report\FontTrait;
use App\Services\Report\IReport;
use App\Storage\Storage;
use Exception;

abstract class AbstractPDFReport implements IReport
{
  use FontTrait;

  protected $pdf;
  protected $diskName;
  protected $temporal;

  public function __construct()
  {
    $this->pdf = new CustomFPDF();
    $this->diskName = 'public';
    $this->temporal = true;
    $this->setDefaultFontStyles($this->pdf); // Usa el trait para configurar estilos de fuente
  }

  protected function setFontStyles()
  {
    $this->setDefaultFontStyles($this->pdf);
  }

  public function build(array $data, string $size)
  {
    $this->setDocumentBase($size, $data);

    if (strtoupper($size) === 'A4') {
      $this->buildA4($data);
    } elseif (strtoupper($size) === 'A5') {
      $this->buildA5($data);
    } elseif (strtoupper($size) == 'TICKET') {
      $this->buildTICKET($data);
    } else {
      throw new Exception('unsupported document size!');
    }

    $folderPath = $this->buildFolderPath($data, $size);
    $this->pdf->Output('F', $folderPath['absolutePath']);

    return $folderPath;
  }

  protected function setDocumentBase($size, $data)
  {
    $aditionalHeight = 0;
    $baseHeight = 130;

    if ($data['documentTypeCode'] !== '00') {
      $baseHeight = 175;
    }

    for ($i = 2; $i < count($data['itemList'] ?? []); $i++) {
      $aditionalHeight += 12;
    }

    if ($aditionalHeight > 240) {
      $aditionalHeight = 240;
    }

    $size = strtoupper($size);
    switch ($size) {
      case "A4":
        $this->pdf = new CustomFPDFA4('P', 'mm', 'A4');
        break;
      case "A5":
        $this->pdf = new CustomFPDFA5('L', 'mm', 'A5');
        break;
      case "TICKET":
        $this->pdf = new CustomFPDFTICKET('P', 'mm', [75, $baseHeight + $aditionalHeight]); // 240
        break;
      default:
        $this->pdf = new CustomFPDFA4('P', 'mm', 'A4');
        break;
    }

    $this->pdf->setValid($data['production'] == 1);

    $this->setDefaultFontStyles($this->pdf); // Usa el trait para configurar estilos de fuente
  }

  abstract public function buildA4(array $data);

  abstract public function buildA5(array $data);

  abstract public function buildTICKET(array $data);

  protected function buildFolderPath(array $data, string $size)
  {
    if ($this->temporal) {
      $fileName = $data['companyDocumentNumber'] . '-' . $data['documentTypeCode'] . '-' . $data['serie'] . '-' . $data['correlative'] . '-' . $size . '.pdf';
      $path = MakeStorageFolder::make($data['companyId'], 'pdf/' . date('Ym'), true);
    } else {
      $fileName = $data['companyDocumentNumber'] . '-' . $data['documentTypeCode'] . '-' . $data['serie'] . '-' . $data['correlative'] . '-' . $size . '.pdf';
      $path = MakeStorageFolder::make($data['companyId'], 'pdf/' . date('Ym') . '/' . $data['documentTypeCode']);
    }

    $keyName = OpensslUtility::encrypt($data['companyId'] . '-' . $data['documentTypeCode'] . '-' . $data['id']);
    $fullDirPath = Storage::disk($this->diskName)->ensureDirectoryExists($path);

    return [
      'relativePath' => $path . $fileName,
      'absolutePath' => $fullDirPath . $fileName,
      'queryPath' => '/see/pdf/' . $keyName,
    ];
  }
}
