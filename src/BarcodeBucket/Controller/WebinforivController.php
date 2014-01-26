<?php
namespace BarcodeBucket\Controller;

use BarcodeBucket\Data\BarcodeService;
use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebinforivScraper\Scraper;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class WebinforivController
{
    /**
     * @var \Silex\Application
     */
    private $application;

    /**
     * @var \WebinforivScraper\Scraper
     */
    private $scraper;

    /**
     * @var \BarcodeBucket\Data\BarcodeService
     */
    private $barcodeService;

    /**
     * @var \Zend\Validator\Barcode
     */
    private $barcodeValidator;

    /**
     * @param Application    $application
     * @param Scraper        $scraper
     * @param BarcodeService $barcodeService
     * @param Barcode        $barcodeValidator
     */
    public function __construct(Application $application, Scraper $scraper, BarcodeService $barcodeService,
                                Barcode $barcodeValidator)
    {
        $this->application = $application;
        $this->scraper = $scraper;
        $this->barcodeService = $barcodeService;
        $this->barcodeValidator = $barcodeValidator;
    }

    /**
     * @param $fullBarcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function barcodeAction($fullBarcode)
    {
        $barcode = substr($fullBarcode, 0, 13);

        if (!$this->barcodeValidator->isValid($barcode) ||
            ($issue = $this->scraper->loadIssue($fullBarcode)) === null) {
            throw new NotFoundHttpException('Issue not found');
        }

        $uuid = $this->barcodeService->upsert($barcode);

        return $this->application->json([
            'barcode'     => [
                'uuid' => $uuid,
                'ean'  => $barcode,
                'gtin' => '0'.$barcode,
            ],
            'addon'       => substr($fullBarcode, 13),
            'title'       => $issue->getTitle(),
            'subtitle'    => $issue->getSubtitle(),
            'issueNumber' => $issue->getIssueNumber(),
            'date'        => $issue->getDate()->format('Y-m-d'),
            'price'       => $issue->getPrice(),
            'picture'     => $this->getPictureForIssue($issue),
            'termsOfSale' => [
                'taxRate'      => $issue->getTaxRate(),
                'discount'     => $issue->getDiscount(),
                'discountCode' => $issue->getDiscountCode(),
                'foldingFee'   => $issue->getFoldingFee(),
                'waybillPrice' => $issue->getWaybillPrice(),
                'consigment'   => $issue->isConsignment(),
            ],
            'sender'   => [
                'id'   => $issue->getSenderId(),
                'name' => $issue->getSender()
            ],
            'lastUpdated' => $issue->getLastUpdate()
        ]);
    }

    /**
     * @param $issue
     * @return string
     */
    private function getPictureForIssue($issue)
    {
        return $issue->getPicture();
    }
}
