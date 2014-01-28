<?php

namespace Barcodebucket\Bundle\NewsagentBundle\Scraping;

use WebinforivScraper\Model\Issue;
use WebinforivScraper\Scraper;
use Zend\Cache\Storage\StorageInterface;

class ScrapingService
{
    /**
     * @var Scraper
     */
    private $scraper;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @param Scraper          $scraper
     * @param StorageInterface $cache
     */
    public function __construct(Scraper $scraper, StorageInterface $cache)
    {
        $this->scraper = $scraper;
        $this->cache = $cache;
    }

    public function loadIssue($fullBarcode)
    {
        $serializedIssue = $this->cache->getItem($fullBarcode, $success);
        $cached = $success && ($issue = unserialize($serializedIssue)) instanceof Issue;

        if (!$cached) {
            $issue = $this->scraper->loadIssue($fullBarcode);
            $this->cache->setItem($fullBarcode, serialize($issue));
        }

        return $issue;
    }
}
