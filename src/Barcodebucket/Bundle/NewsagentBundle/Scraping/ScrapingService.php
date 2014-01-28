<?php

namespace Barcodebucket\Bundle\NewsagentBundle\Scraping;

use Barcodebucket\Scraper\binary;
use Barcodebucket\Scraper\Model\Issue;
use Barcodebucket\Scraper\ScraperInterface;
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

    /**
     * @param  Issue  $issue
     * @return binary
     */
    public function loadPicture(Issue $issue)
    {
        $key = sha1($issue->getPicture());
        $serializedPicture = $this->cache->getItem($key, $success);

        $picture = $success ? unserialize($serializedPicture) : null;
        if (empty($picture) || !$this->isValidPicture($picture)) {
            $picture = $this->scraper->loadPicture($issue);

            if ($picture === null) {
                $this->cache->removeItem($key);
            } else {
                $this->cache->setItem($key, serialize($picture));
            }
        }

        return $picture;
    }

    /**
     * @param $picture
     * @return bool
     */
    private function isValidPicture($picture)
    {
        return preg_match('/DOCTYPE HTML PUBLIC/', $picture) === 0;
    }
}
