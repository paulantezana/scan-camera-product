<?php

namespace App\Services;

use App\Adapters\ReferralGuideReportDataAdapter;
use App\Adapters\SaleReportDataAdapter;
use App\Billing\BillingSender;
use App\Billing\See\SunatBilling\Billing;
use App\Entity\Models\AppCompany;
use App\Entity\Models\ProcessReferralGuide;
use App\Entity\Models\ProcessReferralGuideInvoice;
use App\Entity\Models\ProcessReferralGuideItem;
use App\Entity\Models\ProcessSale;
use App\Entity\Models\ProcessSaleAvoidance;
use App\Entity\Models\ProcessSaleCreditPayment;
use App\Entity\Models\ProcessSaleItem;
use App\Entity\Models\ProcessSalePayment;
use App\Entity\Models\ProcessSaleSummary;
use App\Entity\Models\ProcessSaleSummaryItem;
use App\Exceptions\ValidationException;
use App\Services\Report\ReportManager;
use App\Services\SendManager\EmailManager;
use App\Storage\Storage;

class BuildInvoice
{
  public function buildSaleBilling(int $saleId, int $companyId)
  {
    $sunat = new \stdClass();

    $processSaleModel = new ProcessSale($companyId);
    $processSaleItemModel = new ProcessSaleItem($companyId);
    $processSalePaymentModel = new ProcessSalePayment($companyId);
    $processSaleCreditPaymentModel = new ProcessSaleCreditPayment($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $sale = $processSaleModel->getByIdBilling($saleId);
    $sale['item'] = $processSaleItemModel->getBySaleIdBilling($saleId);
    $sale['payments'] = $processSalePaymentModel->getAllBySaleId($saleId);
    $sale['credits'] = $processSaleCreditPaymentModel->getAllBySaleId($saleId);

    $company = $appCompanyModel->getById($sale['company_id']);

    if (in_array($sale['document_type_id'], [2, 3, 4, 5])) {
      // Send to sunat
      $billingSender = new BillingSender();
      $billingSender->setProvider($company['billing_provider']);
      $sunat = $billingSender->sendInvoice($sale, $company);

      // Send Email
      $this->sendSaleByEmail($sale['email'], $sale, $company);
    }

    // Generate pdf
    $saleData = SaleReportDataAdapter::adapt($sale, $company);
    $report = new ReportManager('Sale', 'SaleBasic', 'PDF');
    $document = $report->create($saleData, $sale['pdf_format']);

    // Update Data
    $processSaleModel->updateById($sale['id'], [
      'pdf_path' => $document['queryPath']
    ], false);
    $documentPath = HOST . PORT . URL_PATH . $document['queryPath'];

    return [
      'sunat' => $sunat,
      'document' => $documentPath,
      'documentTypeId' => $sale['document_type_id'],
    ];
  }

  public function buildBillingAvoidance(int $saleAvoidanceId, int $companyId)
  {
    $sunat = new \stdClass();

    $processSaleAvoidanceModel = new ProcessSaleAvoidance($companyId);
    $processSaleModel = new ProcessSale($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $saleAvoidance = $processSaleAvoidanceModel->getById($saleAvoidanceId);
    $saleAvoidance['sale'] = $processSaleModel->getByIdBilling($saleAvoidance['sale_id']);

    $company = $appCompanyModel->getById($saleAvoidance['sale']['company_id']);

    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->sendAvoidance($saleAvoidance, $company);

    return $sunat;
  }

  public function buildBillingSummary(int $saleSummaryId, int $companyId)
  {
    $processSaleSummaryModel = new ProcessSaleSummary($companyId);
    $processSaleSummaryItemModel = new ProcessSaleSummaryItem($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $saleSummary = $processSaleSummaryModel->getById($saleSummaryId);
    $saleSummary['item'] = $processSaleSummaryItemModel->getAllBySaleSummaryIdBilling($saleSummaryId);
    $company = $appCompanyModel->getById($saleSummary['company_id']);

    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->sendSummary($saleSummary, $company);

    return $sunat;
  }

  public function buildGuide(int $referralGuideId, int $companyId)
  {
    $processReferralGuideModel = new ProcessReferralGuide($companyId);
    $processReferralGuideItemModel = new ProcessReferralGuideItem($companyId);
    $processReferralGuideInvoiceModel = new ProcessReferralGuideInvoice($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $referralGuide = $processReferralGuideModel->getByIdBilling($referralGuideId);
    $referralGuide['item'] = $processReferralGuideItemModel->getByReferralGuideIdBilling($referralGuideId);
    $referralGuide['invoice'] = $processReferralGuideInvoiceModel->getByReferralGuideIdBilling($referralGuideId);

    $company = $appCompanyModel->getById($referralGuide['company_id']);

    // Send to sunat
    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->sendGuide($referralGuide, $company);

    // Generate pdf
    $referralGuideData = ReferralGuideReportDataAdapter::adapt($referralGuide, $company);
    $report = new ReportManager('ReferralGuide', 'ReferralGuideBasic', 'PDF');
    $document = $report->create($referralGuideData, 'A4');

    // Update Data
    $processReferralGuideModel->updateById($referralGuide['id'], [
      'pdf_path' => $document['queryPath']
    ], false);
    $documentPath = HOST . PORT . URL_PATH . $document['queryPath'];

    return [
      'sunat' => $sunat,
      'document' => $documentPath,
    ];
  }

  public function buildGetStatusSaleBilling(int $saleId, int $companyId)
  {
    $sunat = new \stdClass();

    $processSaleModel = new ProcessSale($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $sale = $processSaleModel->getByIdBilling($saleId);
    $company = $appCompanyModel->getById($sale['company_id']);

    if (in_array($sale['document_type_id'], [2, 3, 4, 5])) {
      // Send to sunat
      $billingSender = new BillingSender();
      $billingSender->setProvider($company['billing_provider']);
      $sunat = $billingSender->getStatusInvoice($sale, $company);

      // Send Email
      $this->sendSaleByEmail($sale['email'], $sale, $company);
    }

    return $sunat;
  }

  public function buildGetStatusAvoidance(int $avoidanceId, int $companyId)
  {
    $processSaleAvoidanceModel = new ProcessSaleAvoidance($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $saleAvoidance = $processSaleAvoidanceModel->getByIdBilling($avoidanceId);
    $company = $appCompanyModel->getById($saleAvoidance['company_id']);

    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->getStatusAvoidance($saleAvoidance, $company);

    return $sunat;
  }

  public function buildGetStatusSummary(int $saleSummaryId, int $companyId)
  {
    $processSaleSummaryModel = new ProcessSaleSummary($companyId);
    $processSaleSummaryItemModel = new ProcessSaleSummaryItem($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $saleSummary = $processSaleSummaryModel->getById($saleSummaryId);
    $saleSummary['item'] = $processSaleSummaryItemModel->getAllBySaleSummaryIdBilling($saleSummaryId);
    $company = $appCompanyModel->getById($saleSummary['company_id']);

    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->getStatusSummary($saleSummary, $company);

    return $sunat;
  }

  public function buildGetStatusGuide(int $referralGuideId, int $companyId)
  {
    $processReferralGuideModel = new ProcessReferralGuide($companyId);
    $processReferralGuideItemModel = new ProcessReferralGuideItem($companyId);
    $processReferralGuideInvoiceModel = new ProcessReferralGuideInvoice($companyId);
    $appCompanyModel = new AppCompany($companyId);

    $referralGuide = $processReferralGuideModel->getByIdBilling($referralGuideId);
    $referralGuide['item'] = $processReferralGuideItemModel->getByReferralGuideIdBilling($referralGuideId);
    $referralGuide['invoice'] = $processReferralGuideInvoiceModel->getByReferralGuideIdBilling($referralGuideId);
    $company = $appCompanyModel->getById($referralGuide['company_id']);

    if (strlen($referralGuide['ticket']) === 0) {
      throw new ValidationException('El documento no cuenta con número de ticket para consultar el estado del comprobante');
    }

    // Send to sunat
    $billingSender = new BillingSender();
    $billingSender->setProvider($company['billing_provider']);
    $sunat = $billingSender->sendGuide($referralGuide, $company);

    return $sunat;
  }

  public function queryInvoice(array $sale, array $company)
  {
    $billing = new Billing(SUNAT_INVOICE, $company);

    $invoice = [
      'supplierRuc' => $company['document_number'],
      'documentTypeCode' => $sale['document_type_id_code'],
      'serie' => $sale['serie'],
      'number' => $sale['number'],
      'issueDate' => date('d/m/Y', strtotime($sale['date_of_issue'])),
      'total' => $sale['total']
    ];

    return $billing->queryInvoice($invoice);
  }

  public function queryInvoiceFromSummary(array $saleSummaryItem, array $company)
  {
    $billing = new Billing(SUNAT_INVOICE, $company);

    $invoice = [
      'supplierRuc' => $company['document_number'],
      'documentTypeCode' => $saleSummaryItem['document_type_id_code'],
      'serie' => $saleSummaryItem['serie'],
      'number' => $saleSummaryItem['number'],
      'issueDate' => date('d/m/Y', strtotime($saleSummaryItem['date_of_issue'])),
      'total' => $saleSummaryItem['total']
    ];

    return $billing->queryInvoice($invoice);
  }

  public function sendSaleByEmail(string $to, array $sale, array $company)
  {
    $email = new EmailManager();
    $rootDir = Storage::disk('local')->getRootDir();

    return $email->sendInvoice(
      $to,
      "{$sale['document_type_id_description']} {$sale['serie']}-{$sale['number']} | {$sale['social_reason']}",
      $_ENV['APP_EMAIL'],
      $company['commercial_reason'],
      [
        'documentDescription' => $sale['document_type_id_description'],
        'serie' => $sale['serie'],
        'number' => $sale['number'],
        'socialReason' => $sale['social_reason'],
        'dateOfIssue' => $sale['date_of_issue'],
        'dateOfDue' => $sale['date_of_due'],
        'total' => "{$sale['currency_id_symbol']} {$sale['total']}",
        'documentUrl' => HOST . PORT . URL_PATH . "/query?ruc={$company['document_number']}&serie={$sale['serie']}&number={$sale['number']}",
      ],
      [
        $rootDir . $sale['pdf_path'],
        $rootDir . $sale['xml_path'],
        $rootDir . $sale['cdr_path'],
      ]
    );
  }
}
