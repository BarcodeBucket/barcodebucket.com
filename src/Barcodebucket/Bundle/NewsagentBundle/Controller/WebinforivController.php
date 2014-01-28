<?php
namespace Barcodebucket\Bundle\NewsagentBundle\Controller;

use Barcodebucket\Bundle\MainBundle\Service\BarcodeService;
use Barcodebucket\Bundle\NewsagentBundle\Scraping\ScrapingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebinforivScraper\Model\Issue;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class WebinforivController
{
    /**
     * @var ScrapingService
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
     * @param ScrapingService $scraper
     * @param BarcodeService  $barcodeService
     * @param Barcode         $barcodeValidator
     */
    public function __construct(ScrapingService $scraper, BarcodeService $barcodeService,
                                Barcode $barcodeValidator)
    {
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
        $gtin = $this->getGtin($fullBarcode);
        $issue = $this->loadIssueOrThrowNotFoundException($fullBarcode, $gtin);

        $uuid = $this->barcodeService->upsert($gtin);

        $response = new JsonResponse($this->issueToArray($uuid, $issue));
        $response->setPublic();
        $response->setLastModified($issue->getLastUpdate());

        return $response;
    }

    /**
     * @param  string   $fullBarcode
     * @return Response
     */
    public function pictureAction($fullBarcode)
    {
        $gtin = $this->getGtin($fullBarcode);
        $issue = $this->loadIssueOrThrowNotFoundException($fullBarcode, $gtin);
        $binaryPicture = $this->scraper->loadPicture($issue);

        $response = new Response($binaryPicture, 200, ['content-type' => 'image/jpeg']);
        $response->setEtag(sha1($binaryPicture));
        $response->setPublic();

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
     * @param  string $uuid
     * @param  Issue  $issue
     * @return array
     */
    private function issueToArray($uuid, Issue $issue)
    {
        $fullBarcode = $issue->getBarcode();

        return [
            'barcode' => [
                'uuid' => $uuid,
                'ean' => $this->getBarcode($fullBarcode),
                'gtin' => $this->getGtin($fullBarcode),
            ],
            'addon' => $this->getAddon($fullBarcode),
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
            'lastUpdated' => $issue->getLastUpdate()->format(\DateTime::W3C)
        ];
    }

    /**
     * @param  string $fullBarcode
     * @return string
     */
    private function getGtin($fullBarcode)
    {
        return '0' . $this->getBarcode($fullBarcode);
    }

    /**
     * @param  string $fullBarcode
     * @return string
     */
    private function getBarcode($fullBarcode)
    {
        return substr($fullBarcode, 0, 13);
    }

    /**
     * @param $fullBarcode
     * @return string
     */
    private function getAddon($fullBarcode)
    {
        return substr($fullBarcode, 13);
    }

    /**
     * @param $fullBarcode
     * @param $gtin
     * @return null|Issue
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function loadIssueOrThrowNotFoundException($fullBarcode, $gtin)
    {
        if (!$this->barcodeValidator->isValid($gtin) || ($issue = $this->scraper->loadIssue($fullBarcode)) === null) {
            throw new NotFoundHttpException('Issue not found');
        }

        return $issue;
    }
}
