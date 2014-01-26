<?php
namespace BarcodeBucket\Controller;

use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebinforivScraper\Scraper;

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
     * @param Application $application
     * @param Scraper     $scraper
     */
    public function __construct(Application $application, Scraper $scraper)
    {
        $this->application = $application;
        $this->scraper = $scraper;
    }

    /**
     * @param $fullBarcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function barcodeAction($fullBarcode)
    {
        $issue = $this->scraper->loadIssue($fullBarcode);
        if ($issue == null) {
            throw new NotFoundHttpException('Issue not found');
        }

        return $this->application->json([
            'barcode'     => substr($fullBarcode, 0, 13),
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
