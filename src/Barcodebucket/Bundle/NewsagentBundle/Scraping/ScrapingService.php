<?php

namespace Barcodebucket\Bundle\NewsagentBundle\Scraping;

use WebinforivScraper\Model\Issue;
use WebinforivScraper\ScraperInterface;
use Zend\Cache\Storage\StorageInterface;

class ScrapingService implements ScraperInterface
{
    /**
     * @var ScraperInterface
     */
    private $scraper;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @param ScraperInterface $scraper
     * @param StorageInterface $cache
     */
    public function __construct(ScraperInterface $scraper, StorageInterface $cache)
    {
        $this->scraper = $scraper;
        $this->cache = $cache;
    }

    /**
     * @param $fullBarcode
     * @return null|Issue
     */
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
