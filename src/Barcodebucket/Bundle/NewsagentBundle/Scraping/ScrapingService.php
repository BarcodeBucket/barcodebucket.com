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
     * @param  string     $fullBarcode
     * @return null|Issue
     */
    public function loadIssue($fullBarcode)
    {
        $issue = $this->getIssueOrNull($fullBarcode);

        if ($this->isIssueLoadNeeded($issue)) {
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
        $key = $this->getCacheKeyFromIssue($issue);
        $picture = $this->getPictureOrNull($key);

        if ($this->isPictureLoadNeeded($picture)) {
            $picture = $this->scraper->loadPicture($issue);
            $this->updateCache($picture, $key);
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

    /**
     * @param $picture
     * @return bool
     */
    private function isPictureLoadNeeded($picture)
    {
        return empty($picture) || !$this->isValidPicture($picture);
    }

    /**
     * @param $picture
     * @param $key
     */
    private function updateCache($picture, $key)
    {
        if ($picture === null) {
            $this->cache->removeItem($key);
        } else {
            $this->cache->setItem($key, serialize($picture));
        }
    }

    /**
     * @param $key
     * @return string|null
     */
    private function getPictureOrNull($key)
    {
        $serializedPicture = $this->cache->getItem($key, $success);

        return $success ? unserialize($serializedPicture) : null;
    }

    /**
     * @param  Issue  $issue
     * @return string
     */
    private function getCacheKeyFromIssue(Issue $issue)
    {
        return sha1($issue->getPicture());
    }

    /**
     * @param $issue
     * @return bool
     */
    private function isIssueLoadNeeded($issue)
    {
        return !($issue instanceof Issue);
    }

    /**
     * @param  string     $fullBarcode
     * @return Issue|null
     */
    private function getIssueOrNull($fullBarcode)
    {
        $serializedIssue = $this->cache->getItem($fullBarcode, $success);

        return $success ? unserialize($serializedIssue) : null;
    }
}
