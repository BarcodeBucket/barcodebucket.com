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
            'title'    => $issue->getTitle(),
            'subtitle' => $issue->getSubtitle(),
            'sender'   => [
                'id'   => $issue->getSenderId(),
                'name' => $issue->getSender()
            ]
        ]);
    }
}
