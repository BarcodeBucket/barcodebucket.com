<?php
namespace Barcodebucket\Bundle\NewsagentBundle\Controller;

use Barcodebucket\Bundle\MainBundle\Service\BarcodeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebinforivScraper\Scraper;
use Zend\Cache\Storage\StorageInterface;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class WebinforivController
{
    /**
     * @var Scraper
     */
    private $scraper;

    /**
     * @var BarcodeService
     */
    private $barcodeService;

    /**
     * @var \Zend\Validator\Barcode
     */
    private $barcodeValidator;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @param Scraper          $scraper
     * @param BarcodeService   $barcodeService
     * @param Barcode          $barcodeValidator
     * @param StorageInterface $cache
     */
    public function __construct(Scraper $scraper, BarcodeService $barcodeService,
                                Barcode $barcodeValidator, StorageInterface $cache)
    {
        $this->scraper = $scraper;
        $this->barcodeService = $barcodeService;
        $this->barcodeValidator = $barcodeValidator;
        $this->cache = $cache;
    }

    /**
     * @param $fullBarcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function barcodeAction($fullBarcode)
    {
        $data = $this->cache->getItem($fullBarcode, $success);

        $cached = $success && !empty($data);
        if ($cached) {
            $data = unserialize($data);
        } else {
            $data = $this->loadData($fullBarcode);
            $this->cache->setItem($fullBarcode, serialize($data));
        }

        $lastUpdated = $data['lastUpdated'];
        $data['lastUpdated'] = $data['lastUpdated']->format(\DateTime::W3C);

        $response = new JsonResponse($data);
        $response->setPublic();
        $response->setLastModified($lastUpdated);

        return $response;
    }

    /**
     * @param $issue
     * @return string
     */
    private function getPictureForIssue($issue)
    {
        return $issue->getPicture();
    }

    /**
     * @param $fullBarcode
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function loadData($fullBarcode)
    {
        $barcode = substr($fullBarcode, 0, 13);
        $gtin = '0' . $barcode;

        if (!$this->barcodeValidator->isValid($gtin) ||
            ($issue = $this->scraper->loadIssue($fullBarcode)) === null
        ) {
            throw new NotFoundHttpException('Issue not found');
        }

        $uuid = $this->barcodeService->upsert($gtin);

        return [
            'barcode' => [
                'uuid' => $uuid,
                'ean' => $barcode,
                'gtin' => $gtin,
            ],
            'addon' => substr($fullBarcode, 13),
            'title' => $issue->getTitle(),
            'subtitle' => $issue->getSubtitle(),
            'issueNumber' => $issue->getIssueNumber(),
            'date' => $issue->getDate()->format('Y-m-d'),
            'price' => $issue->getPrice(),
            'picture' => $this->getPictureForIssue($issue),
            'termsOfSale' => [
                'taxRate' => $issue->getTaxRate(),
                'discount' => $issue->getDiscount(),
                'discountCode' => $issue->getDiscountCode(),
                'foldingFee' => $issue->getFoldingFee(),
                'waybillPrice' => $issue->getWaybillPrice(),
                'consigment' => $issue->isConsignment(),
            ],
            'sender' => [
                'id' => $issue->getSenderId(),
                'name' => $issue->getSender()
            ],
            'lastUpdated' => $issue->getLastUpdate()
        ];
    }
}
