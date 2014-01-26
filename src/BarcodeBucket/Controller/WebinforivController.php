<?php
namespace BarcodeBucket\Controller;

use Silex\Application;
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
     */
    public function barcodeAction($fullBarcode)
    {
        $issue = $this->scraper->loadIssue($fullBarcode);

        return $this->application->json([
            'barcode'     => substr($fullBarcode, 0, 13),
            'addon'       => substr($fullBarcode, 13),
            'title'       => $issue->getTitle(),
            'subtitle'    => $issue->getSubtitle(),
            'issueNumber' => $issue->getIssueNumber(),
            'price'       => $issue->getPrice(),
            'lastUpdated' => $issue->getLastUpdate(),
            'sender'   => [
                'id'   => $issue->getSenderId(),
                'name' => $issue->getSender()
            ]
        ]);
    }
}
